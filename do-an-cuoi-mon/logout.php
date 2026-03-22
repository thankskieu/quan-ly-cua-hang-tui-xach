<?php
// Bắt đầu session để có thể truy cập
session_start();

// Xóa session user
unset($_SESSION['user']);

// Hủy toàn bộ session
session_destroy();

// Xóa cookie "remember me"
setcookie("token", "", time() - 3600, "/");

// Chuyển hướng về trang chủ
header("Location: index.php");
exit(); // Thêm exit để đảm bảo script dừng lại sau khi chuyển hướng
?>