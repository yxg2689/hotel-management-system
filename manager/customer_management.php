<?php
session_start();
if (!isset($_SESSION['login_session']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;}

include("../hotel_open.inc");

$sql = "SELECT * FROM customerwithvip ORDER BY CustomerID ASC";
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search = mysqli_real_escape_string($link, $_GET['search']);
    $sql = "
        SELECT * FROM customerwithvip
        WHERE CustomerName LIKE '%$search%' OR CustomerID LIKE '%$search%'
        ORDER BY CustomerID ASC
    ";
} else {
    $sql = "SELECT * FROM customerwithvip ORDER BY CustomerID ASC";
}

// 查詢顧客資料
$result = mysqli_query($link,$sql);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>顧客資料管理</title>
    <style>
        body {
            background-color:rgb(232, 232, 232);
        }
        table {
            width: 90%;
            border-collapse: collapse;
            margin-top: 20px;
    
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
            padding: 20px 30px;
            margin: 20px auto;
            transition: box-shadow 0.3s ease;
        }
        th, td {
            border-radius: 5px;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color:rgb(34, 109, 159);
            color: rgb(255, 255, 255);
        }
        tr:nth-child(even) {
            background-color:rgb(249, 249, 249);
        }
        tr:nth-child(odd) {
            background-color:rgb(219, 219, 219);
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
    
    <div class="content">
        <h2>顧客資料管理</h2>
        
        <form class="search-form" method="GET">
            <input type="text" name="search" placeholder="搜尋顧客姓名或會員編號">
            <button type="submit">搜尋</button>
        </form>
        
        <table>
            <thead>
                <tr>
                    <th>會員編號</th>
                    <th>姓名</th>
                    <th>訂房次數</th>
                    <th>VIP等級</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result) {
                while ($customer = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $customer['CustomerID'] ?></td>
                    <td><?= htmlspecialchars($customer['CustomerName']) ?></td>
                    <td><?= $customer['CustomerTimes'] ?></td>
                    <td><?= $customer['VIPlevel'] . "       " . $customer['VIPName']?></td>
                </tr>
                <?php
                    }
                }include("../hotel_close.inc");
                ?>
            </tbody>
        </table>
    </div>
    <div class="btn">
        <a href="manager.php" class="mbtn">回到主頁</a>
    </div>

</body>
</html>