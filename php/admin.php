<?php
include 'db_connection.php'; // Kết nối đến cơ sở dữ liệu

// Xử lý khi có yêu cầu xóa user
if (isset($_GET['delete_id'])) {
    $userId = $_GET['delete_id'];

    // Truy vấn để xóa user
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        // Chuyển hướng lại trang admin sau khi xóa
        header("Location: ../html/admin.html");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }

    $stmt->close();
}

// Truy vấn danh sách người dùng
$sql = "SELECT id, username, email FROM users";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>

<script>
    // Lưu danh sách người dùng từ PHP vào biến JavaScript
    const users = <?php echo json_encode($users); ?>;

    // Chèn dữ liệu người dùng vào bảng HTML trong admin.html
    document.addEventListener('DOMContentLoaded', () => {
        const tableBody = document.querySelector('tbody');

        if (users.length > 0) {
            users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td><button class='delete-btn' onclick="deleteUser(${user.id})">Delete</button></td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan="3">No users found</td></tr>';
        }
    });

    function deleteUser(id) {
        if (confirm('Are you sure you want to delete this user?')) {
            window.location.href = '../php/admin.php?delete_id=' + id;
        }
    }
</script>
