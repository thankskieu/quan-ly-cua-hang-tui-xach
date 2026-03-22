<?php include_once './phan-quyen.php'?>

<?php
require_once "../models/products.php";

$product = new Products();

// Lấy ID sản phẩm từ URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID sản phẩm không hợp lệ.");
}

$id = intval($_GET['id']);
$item = $product->getById($id);

if (!$item) {
    die("Sản phẩm không tồn tại.");
}

// Xóa file ảnh nếu có
if (!empty($item['image']) && file_exists("../" . $item['image'])) {
    unlink("../" . $item['image']);
}

// Xóa sản phẩm khỏi database
$result = $product->delete($id);

// Chuyển về trang danh sách
header("Location: dashboard.php");
exit;
?>
