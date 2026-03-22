<?php
// Sử dụng session_start() ngay từ đầu nếu nó chưa được gọi ở đâu khác
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Đường dẫn tùy vào cấu trúc của bạn
require_once "../models/admin.php";

// Khởi tạo class Admin
$adminObj = new Admin();

// Gọi hàm getAdmin và lưu kết quả
$adminAccount = $adminObj->getAdmin();

// Kiểm tra trạng thái đăng nhập
if (!$adminAccount['success']) {
    // Nếu không phải admin, điều hướng về trang index.php
    header("Location: /CDPHP/do-an-cuoi-mon/index.php");
    exit();
}
// Nếu là admin, không làm gì cả, script sẽ tiếp tục chạy
// và hiển thị nội dung trang tiếp theo (nếu có)
?>