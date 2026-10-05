<?php
    session_start();
    if (!isset($_SESSION['login_session']) || $_SESSION['role'] !== 'customer') {
        header("Location: ../login.php");
        exit;}
    $id = $_SESSION['login_session'];
    
    include("../hotel_open.inc");
    // 獲得該顧客資料
    $sql = "SELECT * FROM Customer WHERE CustomerID = $id";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
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
        
        .container {
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

        .ubtn {
            text-align: left;
        }

    </style>
</head>
<body>

    <?php include 'customer_common_header.php'; ?>

    
    <div class="content">
        <div class="container">
            <h2><?php echo $row['CustomerName']; ?></h2>
            <form action="edit.php?id=<?php echo $id ?>" method="post">
            <table align="center">
                <tr>
                    <td class="label-cell">姓名</td>
                    <td><?php echo $row['CustomerName']; ?></td>
                </tr>
                <tr>
                    <td class="label-cell">編號</td>
                    <td><?php echo $row['CustomerID']; ?></td>
                </tr>  
                <tr>
                    <td class="label-cell">Email</td>
                    <td><?php echo $row['Email']; ?></td>
                </tr> 
                <tr>
                    <td class="label-cell">密碼</td>
                    <td><input type="text" name="CustomerPassword" class="ubtn" value="
                    <?php echo $row['CustomerPassword'] ?>"/></td>
                    <td colspan="2" align="right">
                    <input type="submit" value="更新連絡資料"/></td>
                </tr>
            </table>
            </form>
        </div>
        <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                $CustomerPassword = $_POST["CustomerPassword"];
                $sql_update = "UPDATE Customer SET CustomerPassword = '$CustomerPassword' WHERE CustomerID = $id";
                mysqli_query($link, $sql_update);
                header("Location: customer.php");
            }
        ?>
        <div class="btn">
            <a href="customer.php" class="mbtn">返回個人主頁</a>
        </div>
    </div>
<?php include("../hotel_close.inc"); ?>
</body>
</html>