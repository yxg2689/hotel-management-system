<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("hotel_open.inc");
    

    // 顧客登入邏輯（如有）...

    if (isset($_POST["guest-email"]) && isset($_POST["guest-password"])) {
        $guest_email = $_POST["guest-email"];
        $guest_password = $_POST["guest-password"];

        $sql_guest = "SELECT * FROM Customer WHERE Email='$guest_email' AND CustomerPassword='$guest_password'";
        $result_guest = mysqli_query($link,$sql_guest);
        $row = mysqli_fetch_assoc($result_guest);

        if (mysqli_num_rows($result_guest) == 1) {
            $_SESSION['login_session'] = $row['CustomerID'];
            $_SESSION['role'] = 'customer';
            
            header("Location: customer/customer.php");
            exit();
        } else {
            header("Location: login.php?error=1");
            exit();
        }
        include("hotel_close.inc");

    }

    elseif (isset($_POST["admin-username"]) && isset($_POST["admin-password"])) {
        $admin_username = $_POST["admin-username"];
        $admin_password = $_POST["admin-password"];

        $sql_admin = "SELECT * FROM Manager WHERE ManagerID='$admin_username' AND ManagerPassword='$admin_password'";
        $result_admin = mysqli_query($link,$sql_admin);

        if (mysqli_num_rows($result_admin) == 1) {
            $_SESSION['login_session'] = $admin_username;
            $_SESSION['role'] = 'admin';
            header("Location: manager/manager.php");
            exit();
        } else {
            header("Location: login.php?error=1");
            exit();
        }
        include("hotel_close.inc");

    }
}

?>
