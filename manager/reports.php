<?php
session_start();
if (!isset($_SESSION['login_session']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;}

include("../hotel_open.inc");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>營業報表</title>
    <style>
        /* 讓整體字體與間距更舒服 */
        body {
            font-family: "微軟正黑體", "Microsoft JhengHei", sans-serif;
            background-color:rgb(232, 232, 232);
            color: #333;
        }

        /* 主標題置中並增加底部空白 */
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 40px;
        }

        /* 區塊卡片 */
        section.report-block {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
            padding: 20px 30px;
            margin: 45px;
            transition: box-shadow 0.3s ease;
        }
        section.report-block:hover {
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }

        /* 區塊標題 */
        section.report-block > h2 {
            color: #34495e;
            margin-top: 0;
            border-bottom: 2px solid #3498db;
            padding-bottom: 8px;
            font-weight: 600;
            font-size: 1.4em;
        }

        /* 表格美化 */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: center;
        }
        th {
            background-color:rgb(34, 109, 159);
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f2f6fc;
        }
        tr:hover {
            background-color: #dbe9fc;
        }
        .btn {
            text-align: center;
        }

        .mbtn {
            padding: 8px 15px;
            background-color: rgb(34, 109, 159);
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    
    </style>
</head>

<body>
    <?php include 'manage_common_header.php'; ?>
    <h2>管理員營業報表</h2>

    <section class="report-block">
        <h2>1. 總收入</h2>
        <?php
        $sql_order = "
        SELECT r.*, c.CustomerID, cv.Discount
        FROM Reservation r
        JOIN Customer c ON r.CustomerID = c.CustomerID
        JOIN CustomerWithVIP cv ON c.CustomerID = cv.CustomerID
        ORDER BY r.ReservationID DESC
    ";

    $result_order = mysqli_query($link, $sql_order);
    $bills = [];

    while ($row = mysqli_fetch_assoc($result_order)) {
        $rid = $row['ReservationID'];
        $bills[$rid] = $row;
        $bills[$rid]['RoomIDs'] = [];
        $bills[$rid]['RoomPrice'] = 0;
        $bills[$rid]['Services'] = [];
        $bills[$rid]['Discount'] = floatval($row['Discount']);
    }

    if (count($bills) > 0) {
        $rid_list = implode(',', array_keys($bills));

        // 查房價與房號
        $sql_rooms = "SELECT rr.ReservationID, rr.RoomID, r.RoomPrice
            FROM Reservation_Room rr
            JOIN Room r ON rr.RoomID = r.RoomID
            WHERE rr.ReservationID IN ($rid_list)";
        $result_rooms = mysqli_query($link, $sql_rooms);
        while ($row = mysqli_fetch_assoc($result_rooms)) {
            $rid = $row['ReservationID'];
            $bills[$rid]['RoomIDs'][] = $row['RoomID'];
            $bills[$rid]['RoomPrice'] = $row['RoomPrice'];
        }

        // 查服務資料
        $sql_services = "SELECT rs.ReservationID, s.ServiceName, s.ServiceFee
                        FROM Reservation_Service rs JOIN Service s ON rs.ServiceID = s.ServiceID
                        WHERE rs.ReservationID IN ($rid_list)";
        $result_services = mysqli_query($link, $sql_services);
        while ($row = mysqli_fetch_assoc($result_services)) {
            $bills[$row['ReservationID']]['Services'][] = $row;
        }
    }

    // 報表統計：印出所有訂單，含折扣後訂房與服務收入
    $total_room_all = 0;
    $total_service_all = 0;
    $total_all = 0;

    echo "<h2>訂單報表</h2><table border='1' cellpadding='10'>
    <tr><th>訂單編號</th><th>顧客ID</th><th>入住日期</th><th>退房日期</th><th>房型</th><th>房數</th><th>人數</th><th>折扣</th><th>訂房收入</th><th>服務收入</th><th>總金額</th></tr>";

    foreach ($bills as $bill) {
        $discount = $bill['Discount'];
        if($discount == 0) $discount = 1;
        
        $nights = (new DateTime($bill['CheckOutDate']))->diff(new DateTime($bill['CheckInDate']))->days;

        $room_total = $bill['RoomPrice'] * $nights * $bill['Quantity'];
        $service_total = 0;
        foreach ($bill['Services'] as $service) {
            $service_total += $service['ServiceFee'] * $nights * $bill['People'];
        }

        $room_discounted = $room_total * $discount;
        $service_discounted = $service_total * $discount;
        $total_discounted = $room_discounted + $service_discounted;

        echo "<tr>
            <td>{$bill['ReservationID']}</td>
            <td>{$bill['CustomerID']}</td>
            <td>{$bill['CheckInDate']}</td>
            <td>{$bill['CheckOutDate']}</td>
            <td>{$bill['RoomType']}</td>
            <td>{$bill['Quantity']}</td>
            <td>{$bill['People']}</td>
            <td>{$discount}</td>
            <td>" . number_format($room_discounted) . "</td>
            <td>" . number_format($service_discounted) . "</td>
            <td>" . number_format($total_discounted) . "</td>
        </tr>";

        $total_room_all += $room_discounted;
        $total_service_all += $service_discounted;
        $total_all += $total_discounted;
    }

    echo "<tr style='font-weight: bold; background-color: #eee;'>
        <td colspan='8'>總計</td>
        <td>" . number_format($total_room_all) . "</td>
        <td>" . number_format($total_service_all) . "</td>
        <td>" . number_format($total_all) . "</td>
    </tr>
    </table>";
    ?>
    </section>

    <section class="report-block">
        <h2>2. 最多預訂次數顧客排名</h2>
        <?php
        $sql = "
            SELECT Customer.CustomerID, Customer.CustomerName, COUNT(*) AS BookingCount
            FROM Reservation
            JOIN Customer ON Reservation.CustomerID = Customer.CustomerID
            GROUP BY Customer.CustomerID
            ORDER BY BookingCount DESC
            LIMIT 5;
        ";
        $result = mysqli_query($link, $sql);
        echo "<table border='1'>
                <tr><th>ID</th><th>姓名</th><th>預訂次數</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['CustomerID']}</td>
                    <td>{$row['CustomerName']}</td>
                    <td>{$row['BookingCount']}</td>
                </tr>";
        }
        echo "</table>";
        ?>
    </section>

    <section class="report-block">
        <h2>3. 近6個月預訂率超過50%的房間</h2>
        <?php
        $sql = "
            SELECT 
                Room.RoomID, 
                Room.RoomType, 
                COUNT(Reservation_Room.RoomID) AS BookedCount
            FROM Reservation
            JOIN Reservation_Room ON Reservation.ReservationID = Reservation_Room.ReservationID
            JOIN Room ON Reservation_Room.RoomID = Room.RoomID
            WHERE CheckInDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY Room.RoomID
            HAVING BookedCount >= (6 * 30 * 0.25) / 1;
            ";
            //-- 以每日估計，6個月內有45次預訂（一次預定至少有2天）
        $result = mysqli_query($link, $sql);
        echo "<table border='1'>
                <tr><th>ID</th><th>房型</th><th>近6個月被預訂次數</th></tr>";
        if (mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='3'>沒有超過50%預訂次數的房間</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['RoomID']}</td>
                        <td>{$row['RoomType']}</td>
                        <td>{$row['BookedCount']}</td>
                    </tr>";
            }
        }
        echo "</table>";
        ?>
    </section>

    <section class="report-block">
        <h2>4. 最常被預訂之房型</h2>
        <?php
        $sql = "
            SELECT Room.RoomType, COUNT(*) AS TimesBooked
            FROM Reservation_Room
            JOIN Room ON Reservation_Room.RoomID = Room.RoomID
            GROUP BY Room.RoomType
            ORDER BY TimesBooked DESC;
        ";
        $result = mysqli_query($link, $sql);
        echo "<table border='1'>
                <tr><th>房型</th><th>預訂次數</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['RoomType']}</td>
                    <td>{$row['TimesBooked']}</td>
                </tr>";
        }
        echo "</table>";
        ?>
    </section>
            <div class="btn">
            <a href="manager.php" class="mbtn">回到主頁</a>
        </div>
</body>
</html>

<?php
include("../hotel_close.inc");

?>