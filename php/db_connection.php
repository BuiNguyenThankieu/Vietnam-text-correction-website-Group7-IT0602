<?php
$servername = "localhost";
$username = "root"; // Tài khoản MySQL mặc định cho XAMPP
$password = ""; // Mật khẩu trống cho XAMPP
$dbname = "text_correction_db"; // Tên database của bạn

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
