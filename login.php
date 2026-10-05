<?php
session_start();
$type = isset($_GET['type']) ? $_GET['type'] : 'guest';
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>凝萃文旅 - 登入</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Microsoft JhengHei', Arial, sans-serif; }
        body { background-color: #f5f5f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-container { background-color: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); width: 400px; max-width: 90%; }
        .login-container h2 { text-align: center; margin-bottom: 20px; color: #4CAF50; }
        .user-type { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .user-type-btn { padding: 10px 20px; background-color: #eee; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .user-type-btn.active { background-color: #4CAF50; color: white; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="text"], input[type="password"] { width: 100%; padding: 8px; }
        .login-btn { width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
        .error { color: red; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <section class="login-container">
        <h2>選擇登入身分</h2>
        <div class="user-type">
            <a href="login.php?type=guest" class="user-type-btn <?php echo ($type=='guest')?'active':''; ?>">顧客登入</a>
            <a href="login.php?type=admin" class="user-type-btn <?php echo ($type=='admin')?'active':''; ?>">管理員登入</a>
        </div>

        <?php if ($error): ?>
            <div class="error">帳號或密碼錯誤！</div>
        <?php endif; ?>

        <?php if ($type=='guest'): ?>
        <!-- 顧客登入表單 -->
        <form action="check.php" method="POST">
            <div class="form-group">
                <label for="guest-email">電子郵件</label>
                <input type="email" id="guest-email" name="guest-email" required>
            </div>
            <div class="form-group">
                <label for="guest-password">密碼</label>
                <input type="password" name="guest-password" required>
            </div>
            <button type="submit" class="login-btn">顧客登入</button>
        </form>
        <?php elseif ($type=='admin'): ?>
        <!-- 管理員登入表單 -->
        <form action="check.php" method="POST">
            <div class="form-group">
                <label for="admin-username">管理員帳號</label>
                <input type="text"  name="admin-username" required>
            </div>
            <div class="form-group">
                <label for="admin-password">密碼</label>
                <input type="password" id="admin-password" name="admin-password" required>
            </div>
            <button type="submit" class="login-btn">管理員登入</button>
        </form>
        <?php endif; ?>

        <a href="index.php" class="back-link">返回首頁</a>
    </section>
</body>
</html>
