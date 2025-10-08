<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) == 1) {
        // Login berhasil
        $_SESSION['username'] = $username;

        // Redirect ke index.php
        header("Location: index.php");
        exit(); // Penting untuk menghentikan eksekusi script
    } else {
        // Login gagal
        echo "<script>alert('Username atau password salah'); window.location='login.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">
    <!-- BOXICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <title>Login</title>
</head>
<body>
    <div class="wrapper">
        <div class="form-header">
            <div class="titles">
                <div class="title-login">Login</div>
            </div>
        </div>
        <form action="#" class="login-form" autocomplete="off">
            <div class="input-box">
                <input type="text" class="input-field" id="log-username" required>
                <label for="log-username" class="label">Username/NISN</label>
                <i class='bx bx-envelope icon'></i>
            </div>
            <div class="input-box">
                <input type="password" class="input-field" id="log-pass" required>
                <label for="log-pass" class="label">Password</label>
                <i class='bx bx-lock-alt icon'></i>
            </div>
            <div class="form-cols">
                <div class="col-1">
                    <input type="checkbox" id="agree">
                    <label for="agree"> Remember Me</label>
                </div>
                <div class="col-2"></div>
            </div>
            <div class="input-box">
                <button type="submit" class="btn-submit" id="SignInBtn">Sign In <i class='bx bx-log-in'></i></button>
            </div>
        </form>
    <script src="js/script.js"></script>
</body>
</html>

