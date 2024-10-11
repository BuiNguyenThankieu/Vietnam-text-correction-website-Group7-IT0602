<?php
include '../php/db_connection.php';
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
            echo "<script>
                    alert('Login successful!');
                    window.location.href = '../html/userlogined.html'; 
                  </script>";
            exit;
        } else {
            // Mật khẩu không đúng
            echo "<script>alert('Invalid password!');
            window.location.href = '../html/login.html';
            </script>";
             
        }
    } else {
        // Không tìm thấy tài khoản
        echo "<script>alert('No account found with that username or email!');</script>";
    }

    $stmt->close();
}
$conn->close();
?>
