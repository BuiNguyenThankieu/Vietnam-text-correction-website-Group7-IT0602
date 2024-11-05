<?php
$servername = "localhost";
$username = "root"; // Tài khoản MySQL mặc định cho XAMPP
$password = ""; // Mật khẩu trống cho XAMPP
$dbname = "text_correction_db"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    
}
session_start(); // Bắt đầu session
$_SESSION['username'] = $username; // $username là tên người dùng đăng nhập thành công
?>
