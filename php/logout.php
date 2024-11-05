<?php
session_start();
session_unset(); // Xóa tất cả các biến session
session_destroy(); // Hủy session
header("Location: ../html/login.html"); // Chuyển hướng về trang đăng nhập
exit();
?>
