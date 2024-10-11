<?php
include 'db_connection.php'; // Kiểm tra lại đường dẫn

// Bật hiển thị lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ form
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Kiểm tra nếu các trường đều có dữ liệu
    if (empty($username) || empty($email) || empty($password)) {
        echo "<script>alert('All fields are required!');</script>";
        exit;
    }

    // Kiểm tra nếu username chỉ có số (không chấp nhận)
    if (preg_match('/^[0-9]+$/', $username)) {
        echo "<script>alert('Username cannot be only numbers!');
        window.location.href = '../html/signup.html';
        </script>";
        exit;
    }

    // Kiểm tra nếu tài khoản đã tồn tại
    $check_user = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_user);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Tài khoản đã tồn tại
        echo "<script>alert('Username or Email already exists!')
        window.location.href = '../html/signup.html';
        ;</script>";
    } else {
        // Mã hóa mật khẩu và lưu vào cơ sở dữ liệu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            // Đăng ký thành công
            echo "<script>
                    alert('Registration successful! Please log in.');
                    window.location.href = '../html/login.html'; //
                  </script>";
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}
$conn->close();
?>
