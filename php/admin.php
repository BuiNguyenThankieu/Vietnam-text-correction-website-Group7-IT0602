<?php
include 'db_connection.php';

// Xử lý khi có yêu cầu xóa người dùng
if (isset($_GET['delete_id'])) {
    $user_id = $_GET['delete_id'];

    // Xóa người dùng theo ID
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: ../html/admin.html"); // Sau khi xóa, quay lại trang admin
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }

    $stmt->close();
}

// Xử lý khi có yêu cầu chỉnh sửa người dùng
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $user_id = $_POST['edit_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // Cập nhật thông tin người dùng
    $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $email, $user_id);

    if ($stmt->execute()) {
        header("Location: ../html/admin.html"); // Sau khi cập nhật, quay lại trang admin
        exit();
    } else {
        echo "Error updating user: " . $stmt->error;
    }

    $stmt->close();
}

// Load danh sách người dùng và in ra bảng
$sql = "SELECT id, username, email FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>
                <button class='edit-btn' onclick=\"editUser(" . $row['id'] . ")\">Edit</button>
                <button class='delete-btn' onclick=\"deleteUser(" . $row['id'] . ")\">Delete</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No users found</td></tr>";
}

$conn->close();
?>
