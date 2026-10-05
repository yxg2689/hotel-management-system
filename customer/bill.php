<?php
    session_start();
    if (!isset($_SESSION['login_session']) || $_SESSION['role'] !== 'customer') {
        header("Location: ../login.php");
        exit;
    }
    $id = $_SESSION['login_session'];

    include("../hotel_open.inc");

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['paid_reservation'])){
        $paid_id = intval($_POST['paid_reservation']);
        $sql_paid = "UPDATE Reservation SET Paid = 1 WHERE ReservationID = $paid_id";
        mysqli_query($link, $sql_paid);}

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_reservation'])) {
        $delete_id = intval($_POST['delete_reservation']);

        // 驗證這筆訂單是屬於這個顧客的
        $sql_check = "SELECT * FROM Reservation WHERE ReservationID = $delete_id AND CustomerID = $id AND Paid = 0";
        $result_check = mysqli_query($link, $sql_check);

        if (mysqli_num_rows($result_check) > 0) {
            // 刪除 Reservation_Service 及 Reservation_Room 資料
            $sql_delete_services = "DELETE FROM Reservation_Service WHERE ReservationID = $delete_id";
            $sql_delete_rooms = "DELETE FROM Reservation_Room WHERE ReservationID = $delete_id";
            $sql_delete_reservation = "DELETE FROM Reservation WHERE ReservationID = $delete_id";
            $sql_delete_times = "UPDATE Customer SET CustomerTimes = CustomerTimes - 1 WHERE CustomerID = $id";

            mysqli_query($link, $sql_delete_services);
            mysqli_query($link, $sql_delete_rooms);
            mysqli_query($link, $sql_delete_reservation);
            mysqli_query($link, $sql_delete_times);

            $delete_success = true;} 
        
        else {
            $delete_error = "無法刪除此訂單（可能已付款或不存在）";}
    }

    // VIP 折扣
    $sql_vip = "SELECT Discount FROM CustomerWithVIP WHERE CustomerID = $id";
    $result_vip = mysqli_query($link, $sql_vip);
    $row_discount = mysqli_fetch_assoc($result_vip);
    $discount = $row_discount ? floatval($row_discount['Discount']) : 1;

    // 查訂單
    $sql_order = "SELECT * FROM Reservation WHERE CustomerID = $id ORDER BY ReservationID DESC";
    $result_order = mysqli_query($link, $sql_order);
    $bills = [];
    while ($row = mysqli_fetch_assoc($result_order)) {
        $rid = $row['ReservationID'];
        $bills[$rid] = $row;
        $bills[$rid]['RoomIDs'] = [];
        $bills[$rid]['RoomPrice'] = 0;
        $bills[$rid]['Services'] = [];}

    if (count($bills) > 0) {
        $rid_list = implode(',', array_keys($bills));

        // 查房價 + RoomID
        $sql_rooms = "SELECT rr.ReservationID, rr.RoomID, r.RoomPrice
                      FROM Reservation_Room rr JOIN Room r ON rr.RoomID = r.RoomID
                      WHERE rr.ReservationID IN ($rid_list)";
        $result_rooms = mysqli_query($link, $sql_rooms);
        while ($row = mysqli_fetch_assoc($result_rooms)) {
            $rid = $row['ReservationID'];
            $bills[$rid]['RoomIDs'][] = $row['RoomID'];
            $bills[$rid]['RoomPrice'] = $row['RoomPrice'];}

        // 查服務
        $sql_services = "SELECT rs.ReservationID, s.ServiceName, s.ServiceFee
                        FROM Reservation_Service rs JOIN Service s ON rs.ServiceID = s.ServiceID
                        WHERE rs.ReservationID IN ($rid_list)";
        $result_services = mysqli_query($link, $sql_services);
        while ($row = mysqli_fetch_assoc($result_services)) {
            $bills[$row['ReservationID']]['Services'][] = $row;}
    }

    include("../hotel_close.inc");

    // 分頁
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 10;
    $bills_array = array_values($bills);
    $totalPages = ceil(count($bills_array) / $perPage);
    $page = min($page, $totalPages);
    $start = ($page - 1) * $perPage;
    $display_bills = array_slice($bills_array, $start, $perPage);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>帳單</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Microsoft JhengHei', Arial, sans-serif;
        }
        
        h2{
            text-align: center;
            margin-bottom: 20px;
        }
        body {
            background-color: #f5f5f5;
        }

        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logout-btn {
            padding: 8px 15px;
            background-color: white;
            color: #4CAF50;
            text-decoration: none;
            border-radius: 4px;
        }

        .content {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .btn {
            text-align: center;
        }

        .pbtn {
            text-align: center;
        }

        .pay-btn {
            display: inline-block;
            margin: 0 10px 20px 10px;
            width: 200px;  
            padding: 10px; 
            text-align: center; 
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .paid-label { 
            color: red; 
            font-weight: bold; 
            text-align: center; 
        }

        .page_btn {
            text-align: center;
        }

        .a_btn {
            background-color: rgb(29, 101, 184);
            color: white;
            border-radius: 3px;
            text-decoration: none;
            padding: 3px 10px;
        }

        .mbtn {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .success-message {
            color: green;
            margin: 10px 0;
        }

        .error-message {
            color: red;
            margin: 10px 0;
        }

    </style>
</head>
<body>

    <?php include 'customer_common_header.php'; ?>


    <div class="content">
        <h2>所有訂單</h2>

        <!-- 訂單刪除訊息 -->
        <?php if (isset($delete_success) && $delete_success): ?>  
            <p class="success-message">訂單刪除成功！</p>        
        <?php elseif (isset($delete_error)): ?>                  
            <p class="error-message"><?php echo $delete_error; ?></p>
        <?php endif; ?>                                          

        <!-- 印出所有訂單 -->
        <?php foreach ($display_bills as $bill): ?>
            <?php
                $nights = (new DateTime($bill['CheckOutDate']))->diff(new DateTime($bill['CheckInDate']))->days;
                $room_price = $bill['RoomPrice'];
                $total_room = $room_price * $nights * $bill['Quantity'];

                $total_service = 0;
                $service_names = [];
                foreach ($bill['Services'] as $service) {
                    $total_service += $service['ServiceFee'] * $nights * $bill['People'];
                    $service_names[] = $service['ServiceName'];}

                $ori_total = $total_room + $total_service;
                $total = number_format($ori_total * $discount);
                $room_ids = !empty($bill['RoomIDs']) ? implode("、", $bill['RoomIDs']) : "尚未分配";
                $info_service = !empty($service_names) ? implode("、", $service_names) : "無";
            ?>

            <div class="container">
                <label>訂單編號：</label><?php echo $bill['ReservationID']; ?><br>
                <label>顧客編號：</label><?php echo $id; ?><br>
                <label>入住時間：</label><?php echo $bill['CheckInDate']; ?><br>
                <label>退房時間：</label><?php echo $bill['CheckOutDate']; ?><br>
                <label>人數：</label><?php echo $bill['People']; ?><br>
                <label>房型：</label><?php echo $bill['RoomType']; ?><br>
                <label>房間數：</label><?php echo $bill['Quantity']; ?><br>
                <label>房間號：</label><?php echo $room_ids; ?><br>
                <label>使用的服務：</label><?php echo $info_service; ?><br>
                <label>折扣：</label><?php echo $discount; ?> 折<br>
                <label>原價格：</label><?php echo $ori_total; ?> 元<br>
                <label>總金額：</label><?php echo $total; ?> 元<br>
                    
                <?php if (!$bill['Paid']): ?>
                <div class="pbtn">
                        <form method="POST" onsubmit="return confirm('確定要付款嗎？');" style="display: inline;">
                            <input type="hidden" name="paid_reservation" value="<?= $bill['ReservationID']; ?>">
                            <button type="submit" class="pay-btn">前往付款</button>
                        </form>
                        <form method="POST" onsubmit="return confirm('確定要取消這筆訂單嗎？');" style="display: inline;">
                            <input type="hidden" name="delete_reservation" value="<?= $bill['ReservationID']; ?>">
                            <button type="submit" class="pay-btn" style="background-color: red;">取消訂單</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p class="paid-label">已付款</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="page_btn">
            <?php if ($page > 1): ?>
            <a href="?id=<?= $id ?>&page=<?= $page - 1 ?>" class="a_btn">上一頁</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
             <a href="?id=<?= $id ?>&page=<?= $i ?>"  class="a_btn"<?= $i == $page ? ' active' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
            <a href="?id=<?= $id ?>&page=<?= $page + 1 ?>" class="a_btn">下一頁</a>
            <?php endif; ?>
        </div>
        <br>
        <div class="btn">
            <a href="customer.php" class="mbtn">回到主頁</a>
            <a href="search.php" class="mbtn">前往搜尋</a>
            <a href="booking.php" class="mbtn">前往訂房</a>
        </div>
    </div>
</body>
</html>
