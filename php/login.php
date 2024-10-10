<?php
include 'db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Tìm người dùng theo username hoặc email
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Đăng nhập thành công
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            echo "Login successful!";
            header("Location: ../html/userlogined.html"); // Điều hướng sau khi đăng nhập thành công
            exit;
        } else {
            // Mật khẩu không đúng
            echo "Invalid password!";
        }
    } else {
        // Không tìm thấy tài khoản
        echo "No account found with that username or email!";
    }

    $stmt->close();
}
$conn->close();
?>
