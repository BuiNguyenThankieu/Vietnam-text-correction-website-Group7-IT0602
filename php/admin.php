<?php
// Kết nối đến cơ sở dữ liệu
include 'db_connection.php';

// Xử lý khi có yêu cầu xóa user
if (isset($_GET['delete_id'])) {
    $userId = $_GET['delete_id'];

    // Thực hiện xóa user
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        header("Location: admin.php"); // Reload lại trang sau khi xóa
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
    $stmt->close();
}

// Xử lý khi có yêu cầu chỉnh sửa user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $userId = $_POST['edit_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Kiểm tra điều kiện username và email trước khi lưu
    $usernameRegex = '/^(?!\d+$)[a-zA-Z0-9]+$/';
    $emailRegex = '/^[a-zA-Z0-9._%+-]+@gmail\.com$/';

    if (preg_match($usernameRegex, $username) && preg_match($emailRegex, $email)) {
        // Thực hiện cập nhật user
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $email, $userId);

        if ($stmt->execute()) {
            header("Location: admin.php"); // Reload lại trang sau khi lưu
            exit();
        } else {
            echo "Error updating user: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Invalid username or email format!";
    }
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* Modal styles */
.modal {
    display: none;
    /* Mặc định ẩn modal */
    position: fixed;
    z-index: 1000;
    /* Đảm bảo modal ở trên các phần tử khác */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    /* Kích hoạt cuộn khi nội dung quá dài */
    background-color: rgba(0, 0, 0, 0.6);
    /* Hiệu ứng mờ nền */
}

.modal-content {
    background-color: #fff;
    margin: 10% auto;
    /* Đặt modal cách đều ở giữa màn hình */
    padding: 20px 30px;
    border-radius: 10px;
    /* Thêm bo tròn */
    width: 80%;
    max-width: 400px;
    /* Giới hạn chiều rộng tối đa */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    /* Hiệu ứng đổ bóng */
}

.modal-header h2 {
    margin: 0;
    padding-bottom: 15px;
    font-size: 22px;
    font-weight: bold;
    border-bottom: 2px solid #eee;
    color: #333;
}

.modal-body {
    margin-top: 20px;
}

.modal-body label {
    display: block;
    margin-bottom: 5px;
    font-size: 16px;
    color: #333;
}

.modal-body input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
}

.modal-footer {
    text-align: right;
    padding-top: 10px;
    border-top: 1px solid #ddd;
}

.save-btn, .cancel-btn {
    padding: 10px 20px;
    margin-left: 10px;
    font-size: 14px;
    background-color: #ff612d;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.save-btn:hover {
    background-color: #ff4b0d;
}

.cancel-btn {
    background-color: #ccc;
}

.cancel-btn:hover {
    background-color: #aaa;
}

.error-message {
    color: red;
    font-size: 12px;
    margin-bottom: 10px;
}

/* Hiển thị modal */
.show-modal {
    display: block;
}

    </style>
</head>

<body>

    <!-- Vùng chứa phần tiêu đề và nút -->
    <div class="header-top">
        <h1>Vietnam Text Correction Website</h1>
        <div class="btn-group">
            <button onclick="window.location.href='../html/index.html'">Logout</button>
        </div>
    </div>

    <!-- Nội dung chính của trang -->
    <div class="container">
        <div class="input-section">
            <h2>Account Management</h2>

            <div class="table-section">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Hiển thị danh sách người dùng từ PHP -->
                        <?php if (!empty($users)) : ?>
                            <?php foreach ($users as $user) : ?>
                                <tr data-id="<?php echo $user['id']; ?>">
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <button class='delete-btn' onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                                        <button class='edit-btn' onclick="editUser(<?php echo $user['id']; ?>)">Edit</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            <button class="delete-all-btn">Delete All</button>
        </div>
    </div>

    <!-- Modal chỉnh sửa người dùng -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit User</h2>
            </div>
            <div class="modal-body">
                <form method="POST" action="admin.php">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <div class="error-message" id="username-error"></div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <div class="error-message" id="email-error"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="save-btn">Save</button>
                <button type="button" class="cancel-btn" onclick="hideEditForm()">Cancel</button>
            </div>
            </form>
        </div>
    </div>

    <script>
        // Hàm xóa người dùng
        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = 'admin.php?delete_id=' + id;
            }
        }

        // Hiển thị modal chỉnh sửa
        function editUser(id) {
            var row = document.querySelector('tr[data-id="' + id + '"]');
            var username = row.querySelector('td:nth-child(1)').textContent;
            var email = row.querySelector('td:nth-child(2)').textContent;

            document.getElementById('edit_id').value = id;
            document.getElementById('username').value = username;
            document.getElementById('email').value = email;

            document.getElementById('edit-modal').classList.add('show-modal');
        }

        // Ẩn modal chỉnh sửa
        function hideEditForm() {
            document.getElementById('edit-modal').classList.remove('show-modal');
        }

        // Kiểm tra username và email trước khi gửi form
        document.querySelector('form').addEventListener('submit', function(event) {
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;

            var usernameRegex = /^(?!\d+$)[a-zA-Z0-9]+$/;
            if (!usernameRegex.test(username)) {
                document.getElementById('username-error').textContent = 'Username must contain only letters or both letters and numbers, but cannot be just numbers.';
                event.preventDefault();
                return false;
            } else {
                document.getElementById('username-error').textContent = '';
            }

            var emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
            if (!emailRegex.test(email)) {
                document.getElementById('email-error').textContent = 'Email must be a valid Gmail address ending with @gmail.com.';
                event.preventDefault();
                return false;
            } else {
                document.getElementById('email-error').textContent = '';
            }
        });
    </script>
</body>

</html>
