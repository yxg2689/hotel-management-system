<?php
    session_start();
    if (!isset($_SESSION['login_session']) || $_SESSION['role'] !== 'customer') {
        header("Location: ../login.php");
        exit;}
    $id = $_SESSION['login_session'];

    include("../hotel_open.inc");

    function getAvailableRooms($link, $roomType, $checkin, $checkout) {
        // 查詢該房型總房間數
        $totalQuery = "SELECT COUNT(*) AS total FROM Room WHERE RoomType = '$roomType'";
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
    <title>凝萃文旅 - 搜尋</title>
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
            text-align: center;
        }

        .left { 
            flex: 1; 
        }

        .right { 
            flex: 1;
            text-align: left;
        }

        .form-group { 
            margin-bottom: 15px; 
        }

        .search_container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
            display: flex;
        }

        .rtype_input {
            width: 50px;
        }

        .room_img { 
            width: 80%; 
            border-radius: 10px; 
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
    </style>
</head>
<body>

    <?php include 'customer_common_header.php'; ?>

    
    <div class="content">
        <form method="post">
        <div class="container">
            <div class="form-group">
                入住時間：<input type="date" name="checkin" required>  
                退房時間：<input type="date" name="checkout" required></br>
            </div>
            <div class="form-group">
                <input type="number" name="single" min="0" class="rtype_input">間單人房  
                <input type="number" name="double" min="0" class="rtype_input">間雙人房  
                <input type="number" name="triple" min="0" class="rtype_input">間三人房  
                <input type="number" name="quad" min="0" class="rtype_input">間四人房 </br>
            </div>
            <button type="submit">搜尋</button>
        </div>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $checkin = $_POST['checkin'];
            $checkout = $_POST['checkout'];
            $roomTypes = ['single' => '單人房','double' => '雙人房','triple' => '三人房','quad' => '四人房'];

            if($checkout <= $checkin){
                    echo "<p style='color:red; text-align:center;'>訂房失敗，入住日期晚於或等於退房日期</p><br>";} 
            else {
                foreach ($roomTypes as $key => $label) {
                    $count = isset($_POST[$key]) ? intval($_POST[$key]) : 0;
                    if ($count > 0) {
                        $available = getAvailableRooms($link, $key, $checkin, $checkout);
                        if ($available >= $count){
                            $room_status = "<label>剩餘房間數：{$available}</label>";}
                        else{
                            $room_status = "<label style='color:red;'>剩餘房間數不足（剩餘{$available}間）</label>";}
                        echo "<div class='search_container'>
                                <div class='left'>
                                    <img src='room_{$key}.png' class='room_img'>
                                </div>
                                <div class='right'>
                                    <h2>{$label}</h2>
                                    <div class='form-group'><br>
                                        $room_status
                                    </div>
                                </div>
                            </div>";
                    }
                }
            }
        }
        ?>
        <div class="btn">
            <a href="customer.php" class="mbtn">回到主頁</a>
            <a href="booking.php" class="mbtn">前往訂房</a>
            <a href="bill.php" class="mbtn">查看帳單</a>
        </div>
    </div>
</body>
</html>