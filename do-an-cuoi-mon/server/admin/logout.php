<?php
// Xóa cookie 'admin_token' bằng cách đặt nó về một chuỗi rỗng và đặt thời gian hết hạn trong quá khứ
setcookie("admin_token", "", time() - 3600, "/");

// Điều hướng người dùng trở lại trang đăng nhập (hoặc trang chính)
header("Location: /CDPHP/do-an-cuoi-mon/server/admin");
exit();
?>