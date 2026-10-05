<?php
    session_start();
    if (!isset($_SESSION['login_session']) || $_SESSION['role'] !== 'customer') {
        header("Location: ../login.php");
        exit;}
    $id = $_SESSION['login_session'];

    include("../hotel_open.inc");

    // 獲得該顧客資料
    $sql = "SELECT * FROM Customer WHERE CustomerID = $id";
    $sql_vip = "SELECT * FROM CustomerWithVIP WHERE CustomerID = $id";
    $result = mysqli_query($link, $sql);
    $result_vip = mysqli_query($link, $sql_vip);
    $row = mysqli_fetch_assoc($result);
    $row_vip = mysqli_fetch_assoc($result_vip);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>凝萃文旅 - 顧客儀表板</title>
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

        table {
            font-size: 16px;
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
        
        .welcome-message {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .label-cell {
            text-align: justify;
            width: 120px;
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
        <div class="welcome-message">
            <h2>歡迎, <?php echo $row['CustomerName']; ?>!</h2>
            <br>
            <table>
                <tr>
                    <td class="label-cell">姓名</td>
                    <td><?php echo $row['CustomerName']; ?></td>
                </tr>
                <tr>
                    <td class="label-cell">編號</td>
                    <td><?php echo $row['CustomerID']; ?></td>
                </tr>  
                <tr>
                    <td class="label-cell">密碼</td>
                    <td><?php echo $row['CustomerPassword']; ?></td>
                    <td><a href="edit.php" class="mbtn">修改密碼</a></td>
                </tr>
                <tr>
                    <td class="label-cell">VIP等級</td>
                    <td><?php echo $row_vip['VIPName']; ?></td>
                </tr>
                <tr>
                    <td class="label-cell">曾經入住次數</td>
                    <td><?php echo $row['CustomerTimes']; ?></td>
                </tr>
            </table>
        </div>
        <div class="btn">
            <a href="search.php" class="mbtn">前往搜尋</a>
            <a href="booking.php" class="mbtn">前往訂房</a>
            <a href="bill.php" class="mbtn">查看帳單</a>
        </div>
    </div>
<?php include("../hotel_close.inc"); ?>
</body>
</html>