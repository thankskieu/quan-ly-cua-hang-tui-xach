<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/server/models/products.php";

echo "<!DOCTYPE html><html><head><title>Updating Stock</title>";
echo "<style>body { font-family: sans-serif; background-color: #f4f4f9; color: #333; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; } .container { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; } h1 { color: #5a67d8; } .success { color: #38a169; } .error { color: #e53e3e; } a { color: #5a67d8; text-decoration: none; font-weight: bold; } a:hover { text-decoration: underline; }</style>";
echo "</head><body><div class='container'>";

try {
    $productsModel = new Products();
    $result = $productsModel->updateAllStockRandomly();

    if ($result) {
        echo "<h1 class='success'>Thành công!</h1>";
        echo "<p>Tất cả sản phẩm đã được cập nhật số lượng tồn kho thành một số ngẫu nhiên từ 50 đến 200.</p>";
        echo "<p>Bây giờ bạn có thể tiếp tục kiểm tra quy trình thanh toán.</p>";
    } else {
        echo "<h1 class='error'>Thất bại!</h1>";
        echo "<p>Không thể cập nhật tồn kho. Vui lòng kiểm tra lại cấu trúc bảng `products` và quyền truy cập cơ sở dữ liệu.</p>";
    }

} catch (Exception $e) {
    echo "<h1 class='error'>Lỗi nghiêm trọng!</h1>";
    echo "<p>Đã có lỗi xảy ra: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<br/><p><a href='index.php'>Quay về trang chủ</a></p>";
echo "</div></body></html>";
?>
