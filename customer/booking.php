<?php
    session_start();
    if (!isset($_SESSION['login_session']) || $_SESSION['role'] !== 'customer') {
        header("Location: ../login.php");
        exit;}
    $id = $_SESSION['login_session'];

    include("../hotel_open.inc");

    function getAvailableRooms($link, $roomType, $checkin, $checkout) {
        // 查詢該房型總房間數
        $totalQuery = "SELECT COUNT(*) as total FROM Room WHERE RoomType = '$roomType'";
        $result = mysqli_query($link, $totalQuery);
        $row = mysqli_fetch_assoc($result);
        $total = $row['total'];

        // 查詢該時間段已被預訂的數量
        $reservedQuery = "SELECT SUM(Quantity) AS booked FROM Reservation 
            WHERE RoomType = '$roomType' AND ((CheckInDate < '$checkout') AND (CheckOutDate > '$checkin'))";
        $result = mysqli_query($link, $reservedQuery);
        $row = mysqli_fetch_assoc($result);
        $booked = $row['booked'] ?? 0;

        return $total - $booked;
    }
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>訂房</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Microsoft JhengHei', Arial, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }

        button {
            padding: 3px 10px;
            background-color: rgb(29, 101, 184);
            color: white;
            border-radius: 3px;
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
            background-color: white;
            display: flex;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .btn {
            text-align: center;
        }

        .mbtn {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .left { 
            flex: 1; 
        }

        .right { 
            flex: 1; 
            padding-left: 20px; 
        }

        .room-img { 
            width: 100%; 
            border-radius: 10px; 
        }

        .form-group { 
            margin-bottom: 15px; 
        }

    </style>
</head>
<body>
    <?php include 'customer_common_header.php'; ?>

    
    <div class="content">
        <div class="left">
            <img id="roomImage" src="room_single.png" class="room-img">
        </div>

        <div class="right">
        <form method="post">
            <div class="form-group">
                <label>入住時間：</label><input type="date" name="checkin" required>
            </div>
            <div class="form-group">
                <label>退房時間：</label><input type="date" name="checkout" required>
            </div>
            <div class="form-group">
                <label>入住人數：</label><input type="number" name="people" min="1" required>
            </div>
            <div class="form-group">
                <label>房型：</label>
                <select name="room_type" id="roomType" onchange="updateImage()" required>
                    <option value="single">單人房</option>
                    <option value="double">雙人房</option>
                    <option value="triple">三人房</option>
                    <option value="quad">四人房</option>
                </select>
            </div>
            <div class="form-group">
                <label>房間數：</label>
                <input type="number" name="quantity" min="1" required>
            </div>
            <div class="form-group">
                <label>附加服務：</label>
                <input type="checkbox" name="service[]" value="1"> 早餐
                <input type="checkbox" name="service[]" value="2"> 晚餐
                <input type="checkbox" name="service[]" value="3"> SPA
                <input type="checkbox" name="service[]" value="4"> 泳池
                <input type="checkbox" name="service[]" value="5"> 下午茶
            </div>
            <button type="submit">提交訂房</button>
        </form>
        <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $checkin = $_POST['checkin'];
                $checkout = $_POST['checkout'];
                $people = intval($_POST['people']);
                $quantity = intval($_POST['quantity']);
                $room_type = $_POST['room_type'];
                $customer_id = $id;
                $paid = 0;
                $room_ntype = 0;

                if($checkout <= $checkin){
                    echo "<p style='color:red;'>訂房失敗，入住日期晚於或等於退房日期</p>";} 
                else {
                    $rooms_remaining=getAvailableRooms($link, $room_type, $checkin, $checkout);
                    if($room_type=='single'){$room_ntype=1;}
                    elseif($room_type=='double'){$room_ntype=2;}
                    elseif($room_type=='triple'){$room_ntype=3;}
                    else{$room_ntype=4;}
                    
                    if($quantity <= $rooms_remaining && $people <= $quantity * $room_ntype){
                        // 寫入 Reservation 
                        $sql_insert = "INSERT INTO Reservation (CustomerID, CheckInDate, CheckOutDate, RoomType, Quantity, People, Paid)
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = mysqli_prepare($link, $sql_insert);
                        mysqli_stmt_bind_param($stmt, "issssii", $customer_id, $checkin, $checkout, $room_type, $quantity, $people, $paid);
                        mysqli_stmt_execute($stmt);
                        $reservation_id = mysqli_insert_id($link);

                        // 分配空房號 (最小未被使用房號)
                        $room_query = "SELECT RoomID FROM Room WHERE RoomType = '$room_type' AND RoomID NOT IN (
                            SELECT rr.RoomID FROM Reservation_Room rr
                            JOIN Reservation r ON rr.ReservationID = r.ReservationID
                            WHERE r.RoomType = '$room_type' AND ((r.CheckInDate < '$checkout') AND (r.CheckOutDate > '$checkin'))) 
                            ORDER BY RoomID ASC LIMIT $quantity";

                        // 寫入 Reservation_Room
                        $result = mysqli_query($link, $room_query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $room_id = $row['RoomID'];
                            $sql_room = "INSERT INTO Reservation_Room (ReservationID, RoomType, RoomID) VALUES ($reservation_id, '$room_type', '$room_id')";
                            mysqli_query($link, $sql_room);}

                        // 寫入 Reservation_Service
                        if (!empty($_POST['service'])) {
                            $services = $_POST['service'] ?? [];
                            if (!is_array($services)) $services = [$services];
                            foreach ($services as $service_id) {
                                $sql_service = "INSERT INTO Reservation_Service (ReservationID, ServiceID) VALUES ($reservation_id, '$service_id')";
                                mysqli_query($link, $sql_service);}
                        }

                        // 在 Customer 將入住次數加一
                        $sql_update = "UPDATE Customer SET CustomerTimes = CustomerTimes + 1 WHERE CustomerID = $customer_id";
                        mysqli_query($link, $sql_update);

                        echo "<p style='color:red;'>訂房成功，訂單編號：$reservation_id</p>";
                    }
                    elseif($quantity > $rooms_remaining && $people <= $quantity * $room_ntype){
                        echo "<p style='color:red;'>訂房失敗，剩餘房間數不足</p>";}
                    elseif($quantity <= $rooms_remaining && $people > $quantity * $room_ntype){
                        echo "<p style='color:red;'>訂房失敗，房間數過少</p>";}
                    else{
                        echo "<p style='color:red;'>訂房失敗，剩餘房間數不足且房間數過少</p>";}
                }
            }
        ?>
        </div>
    </div>

    <div class="btn">
            <a href="customer.php" class="mbtn">回到主頁</a>
            <a href="search.php" class="mbtn">前往搜尋</a>
            <a href="bill.php" class="mbtn">查看帳單</a>
    </div>
    <script>
        function updateImage() {
            const type = document.getElementById('roomType').value;
            const img = document.getElementById('roomImage');
            if (type == 'single') img.src = 'room_single.png';
            else if (type == 'double') img.src = 'room_double.png';
            else if (type == 'triple') img.src = 'room_triple.png';
            else if (type == 'quad') img.src = 'room_quad.png';
        }
    </script>
</body>
</html>