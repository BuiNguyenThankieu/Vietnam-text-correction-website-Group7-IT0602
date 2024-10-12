<?php
include 'db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Kiểm tra nếu tài khoản là admin
    if ($username === "admin" && $password === "admin123") {
        // Thiết lập phiên đăng nhập cho admin
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = "admin";
        header("Location: ../html/admin.html"); // Điều hướng đến trang admin
        exit();
    }

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
            // Đăng nhập thành công cho người dùng thông thường
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            header("Location: ../html/userlogined.html"); // Điều hướng đến trang người dùng
            exit();
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
