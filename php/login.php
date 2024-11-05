<?php
include 'db_connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();}

$error_message = ""; // Khởi tạo biến để lưu thông báo lỗi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Kiểm tra nếu tài khoản là admin
    if ($username === "admin" && $password === "admin123") {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = "admin";
        header("Location: admin.php");
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
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            header("Location: ../php/userlogined.php"); // Điều hướng đến trang người dùng
            exit();
        } else {
            $error_message = "Invalid password!";
        }
    } else {
        $error_message = "No account found with that username or email!";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login&signup.css">
</head>
<body>
    <div class="header-top">
        <h1>Vietnam Text Correction Website</h1>
    </div>
    <div class="wrapper">
        <div class="card-switch">
            <div class="flip-card__front">
                <div class="title">Log in</div>
                <form class="flip-card__form" action="" method="POST">
                    <input class="flip-card__input" name="username" placeholder="Username or Email" type="text" required>
                    <input class="flip-card__input" name="password" placeholder="Password" type="password" required>
                    <button class="flip-card__btn" type="submit">Let's go!</button>
                </form>
                <div class="link-container">
                    <a href="signup.html">Sign up</a> | <a href="index.html">Continue as Guest</a>
                </div>
                <!-- Hiển thị thông báo lỗi -->
                <?php if (!empty($error_message)): ?>
                    <p style="color:red;"><?php echo $error_message; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
