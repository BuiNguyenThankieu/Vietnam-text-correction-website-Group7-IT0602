<?php
include 'db_connection.php';

$id = $_GET['id'];

// Xóa người dùng dựa trên ID
$sql = "DELETE FROM users WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "User deleted successfully";
} else {
    echo "Error deleting user: " . $conn->error;
}

$conn->close();

// Quay lại trang admin sau khi xóa
header("Location: ../html/admin.html");
?>
