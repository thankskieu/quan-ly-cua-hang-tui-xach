<?php
session_start();
require_once __DIR__ . "/server/models/cart.php";
require_once __DIR__ . "/server/models/user.php";

$cartModel = new Cart();
$userModel = new User();

$status = null;
$message = '';
$product_name = '';
$quantity = 0;
$product_price = 0;

// 🛒 Khi người dùng gửi form "Thêm vào giỏ hàng"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check login
    $account = $userModel->getAccount();
    if (!$account['success']) {
        $status = 'error';
        $message = 'Vui lòng đăng nhập để thực hiện hành động này.';
        // If not logged in, return JSON error for AJAX requests
        if (isset($_POST['action'])) { // Assume AJAX if action is set
            header('Content-Type: application/json'); echo json_encode(['success' => false, 'message' => $message]); exit;
        }
    } else {
        $action = $_POST['action'] ?? 'add_to_cart';
        $userId = (int)$account['user']['id'];

        if ($action === 'update_qty') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

            if ($id > 0 && $quantity >= 0) { // Quantity can be 0 to remove item
                $result = $cartModel->updateQuantity($id, max(0, $quantity)); // Ensure quantity is not negative
                header('Content-Type: application/json'); echo json_encode($result); exit;
            } else {
                header('Content-Type: application/json'); echo json_encode(['success' => false, 'message' => 'Dữ liệu cập nhật không hợp lệ.']); exit;
            }
        } elseif ($action === 'remove') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            if ($id > 0) {
                $result = $cartModel->removeItem($id);
                header('Content-Type: application/json'); echo json_encode($result); exit;
            } else {
                header('Content-Type: application/json'); echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ.']); exit;
            }
        } elseif ($action === 'add_to_cart') { // Existing add to cart logic
            // ✅ Lấy dữ liệu, ép kiểu và trim
            $product_id     = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
            $product_name   = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
            $product_price  = isset($_POST['product_price']) ? floatval($_POST['product_price']) : 0;
            $product_image  = isset($_POST['product_image']) ? trim($_POST['product_image']) : '';
            $quantity       = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
            // ✅ Kiểm tra dữ liệu hợp lệ
            if ($product_id > 0  && $product_price > 0 && $quantity > 0) {
        
                // ✅ Thêm kiểm tra chặt chẽ hơn: quantity là số nguyên dương
                if (!is_int($quantity) || $quantity <= 0) {
                    $status = 'error';
                    $message = 'Số lượng phải là số nguyên dương.';
                } else {
                    $result = $cartModel->addToCart($product_id, $quantity, $product_price);
        
                    $valid = isset($result['success']) ? (bool)$result['success'] : false;
                    $message = $result['message'] ?? 'Không rõ kết quả.';
        
                    $status = $valid ? 'success' : 'error';

                    // Handle Buy Now redirect
                    if ($status === 'success' && isset($_POST['buy_now']) && $_POST['buy_now'] === 'true') {
                        header("Location: checkout.php");
                        exit;
                    }
                }
        
            } else {
                $status = 'error';
                $message = 'Payload không hợp lệ hoặc thiếu dữ liệu.';
            }
        } else { // Fallback for unknown actions
            $status = 'error';
            $message = 'Hành động không hợp lệ.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm vào giỏ hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    body {
        background: #000;
        font-family: 'Poppins', sans-serif;
        letter-spacing: 0.4px;
    }
    .card {
        background: #0e0e0e;
        border: 1px solid rgba(255,255,255,0.08);
        padding: 48px;
        border-radius: 14px;
        width: 100%;
        max-width: 480px;
        animation: fadeUp .45s ease;
    }
    @keyframes fadeUp {
        from {opacity:0; transform: translateY(10px);}
        to {opacity:1; transform: translateY(0);}
    }
    .btn {
        padding: 14px 0;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        transition: .25s;
        border: 1px solid rgba(255,255,255,0.18);
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }
    .btn:hover {
        opacity: .85;
        transform: translateY(-1px);
    }
    .title {
        font-size: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.6px;
    }
    .desc {
        font-size: 13px;
        color: #b1b1b1;
        margin-top: 10px;
        line-height: 1.6;
    }
    .circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        margin: 0 auto 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
</style>
</head>

<body class="min-h-screen flex items-center justify-center text-white px-4">

<?php if ($status === 'success'): ?>
<div class="card text-center">

    <div class="circle border border-green-500/40 text-green-400">✓</div>

    <div class="title">Đã thêm vào giỏ</div>

    <div class="desc"><?= htmlspecialchars($message) ?></div>

    <div class="mt-8 space-y-3">
        <a href="cart.php" class="btn w-full bg-white text-black font-semibold">Xem giỏ hàng</a>
        <?php include "./backpage.php"; ?>
    </div>

</div>

<?php elseif ($status === 'error'): ?>
<div class="card text-center">

    <div class="circle border border-red-500/40 text-red-400">✕</div>

    <div class="title">Thao tác thất bại</div>

    <div class="desc"><?= htmlspecialchars($message) ?></div>

    <div class="mt-8 space-y-3">
        <?php include "./backpage.php"; ?>
    </div>

</div>
    <?php elseif ($status === 'error'): ?>
        <div class="w-full max-w-md p-8 rounded-3xl bg-white text-center shadow-xl animate-fadeInScale">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-red-500 mb-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Thêm vào giỏ hàng thất bại!</h2>
            <p class="text-lg text-gray-600 mb-6">Lỗi: <?= htmlspecialchars($message) ?></p>
            <?php include_once "./backpage.php"?>
        </div>
    <?php endif; ?>

</body>
</html>