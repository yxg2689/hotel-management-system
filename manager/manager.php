<?php
session_start();
if (!isset($_SESSION['login_session']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>凝萃文旅 - 管理系統</title>
    <style>
        /* 整體背景色（可自行調整） */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color:rgb(157, 153, 153); /* 淺灰色背景 */
        }

        /* 頁首橫幅（圖片） */
        .header {
            position: relative;
            background: url('https://www.yuanyu.tw/camera/blog/wp-content/uploads/2021/12/OLYMPUS%E6%98%9F%E7%A9%BA%E5%B0%8D%E7%84%A6_peter_06-1024x768.jpg') center center/cover no-repeat;
            color: rgb(243, 229, 255);
            padding: 80px 20px;
            text-align: center;
        }

        .header::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4); /* 遮罩 */
            z-index: 0;
        }

        .header h1 {
            position: relative;
            z-index: 1;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }


        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        /* 歡迎語 */
        .header h2 {
            color:rgb(228, 200, 255);
        }


        

        /* 功能卡片容器 */
        .admin-features {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px; /* 卡片之間的距離 */
            padding: 20px;
        }

        /* 功能卡片（按鈕式） */
        .feature-card {
            background-color: white;
            width: 90%;
            max-width: 500px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .feature-card:hover {
            background-color: #d8eafd; /* hover 顏色 */
            transform: translateY(-3px);
        }

        .feature-card h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #007bff;
        }

        .feature-card p {
            color: #666;
            margin-top: 10px;
        }

        /* 登出按鈕 */
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color:rgb(80, 10, 69);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #e60000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>凝萃文旅 - 管理系統</h1>
        <br/><br/>
        <h2>歡迎, 管理員!</h2>
        <p>您已成功登入飯店管理系統。</p>
    </div>

    
    <div class="admin-features">
        <div class="feature-card" onclick="location.href='customer_management.php'">
            <h3>顧客資料管理</h3>
            <p>查詢與管理顧客資料</p>
        </div>
        <div class="feature-card" onclick="location.href='reports.php'">
            <h3>營業報表</h3>
            <p>查看飯店營運統計數據</p>
        </div>
    </div>

    <a href="../logout.php" class="logout-btn">登出</a>
</body>
</html>
