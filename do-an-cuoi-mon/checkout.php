<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . "/server/models/user.php";
require_once __DIR__ . "/server/models/bill.php";

$userObj = new User();
$billObj = new Bill();

$account = $userObj->getAccount();
if (!$account['success']) {
    header("Location: auth.php");
    exit;
}
$user = $account['user'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}

$selectedIds = $_POST['selected_items'] ?? '';
$couponCode = $_POST['coupon_code'] ?? '';
$discountAmount = floatval($_POST['discount_amount'] ?? 0);

if (empty($selectedIds)) {
    $_SESSION['flash_message'] = 'Vui lòng chọn sản phẩm để thanh toán.';
    header("Location: cart.php");
    exit;
}

$cartIdArray = array_filter(array_map('intval', explode(',', $selectedIds)));
$items = $billObj->getCartItems($cartIdArray, $user['id']);

if (empty($items)) {
    $_SESSION['flash_message'] = 'Sản phẩm bạn chọn không hợp lệ hoặc đã bị xóa.';
    header("Location: cart.php");
    exit;
}

$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shippingFee = ($subtotal > 500000) ? 0 : 30000;
$total = $subtotal + $shippingFee - $discountAmount;
if ($total < 0) $total = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán • CHAUTFIFTH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #f9fafb; color: #111; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        .payment-option.selected { border-color: #f59e0b; background-color: #fffbeb; }

        /* Loader overlay: mặc định ẩn, chỉ show khi submit */
        #loader-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            z-index: 9999;
            flex-direction: column;
            gap: 1rem;
            color: #111;
            font-weight: 500;
        }
        #loader-overlay.show { display: flex; }

        .spinner {
            width: 56px;
            height: 56px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #f59e0b;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50">

<div id="loader-overlay">
    <div class="spinner"></div>
    <p>Đang xử lý đơn hàng, vui lòng chờ...</p>
</div>

<header class="bg-white border-b border-gray-200 py-4">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <a href="index.php" class="text-2xl font-bold font-playfair tracking-tight text-gray-900">CHAUTFIFTH</a>
        <div class="flex items-center gap-2 text-gray-500 text-sm">
            <span class="material-symbols-rounded text-green-600">lock</span>
            <span>Thanh toán bảo mật</span>
        </div>
    </div>
</header>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Lỗi!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['flash_message']) ?></span>
        </div>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <form id="checkout-form" action="bill_action.php" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16">
        <input type="hidden" name="cart_ids" value="<?= htmlspecialchars($selectedIds) ?>">
        <input type="hidden" name="total_amount" value="<?= (float)$total ?>">
        <input type="hidden" name="discount_amount" value="<?= (float)$discountAmount ?>">
        <input type="hidden" name="coupon_code" value="<?= htmlspecialchars($couponCode) ?>">
        <input type="hidden" name="shipping_fee" value="<?= (float)$shippingFee ?>">


        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white p-5 md:p-6 rounded-lg border border-gray-200">
                <h2 class="text-lg font-bold mb-4 text-gray-800">Thông tin giao hàng</h2>
                <div class="space-y-4">
                    <div>
                        <label for="fullname" class="block text-sm font-medium mb-1 text-gray-700">Họ và tên</label>
                        <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($user['username']) ?>"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-yellow-500 focus:ring-yellow-500 outline-none transition" required>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-1 text-gray-700">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                               placeholder="VD: 0909123456"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-yellow-500 focus:ring-yellow-500 outline-none transition"
                               required pattern="[0-9]{10}" maxlength="10">
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium mb-1 text-gray-700">Địa chỉ nhận hàng</label>
                        <textarea id="address" name="address" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-yellow-500 focus:ring-yellow-500 outline-none transition"
                                  required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 md:p-6 rounded-lg border border-gray-200">
                <h2 class="text-lg font-bold mb-4 text-gray-800">Phương thức thanh toán</h2>
                <div class="space-y-3" id="payment-methods">
                    <label class="payment-option flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer transition">
                        <input type="radio" name="payment_method" value="cod" class="hidden" checked>
                        <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center transition-all">
                            <div class="w-2.5 h-2.5 bg-yellow-500 rounded-full scale-0 transition-transform"></div>
                        </div>
                        <span class="material-symbols-rounded text-gray-600">local_shipping</span>
                        <div class="flex-1">
                            <span class="font-semibold text-gray-800">Thanh toán khi nhận hàng (COD)</span>
                            <p class="text-xs text-gray-500">Thanh toán tiền mặt khi shipper giao hàng.</p>
                        </div>
                    </label>

                    <label class="payment-option flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer transition">
                        <input type="radio" name="payment_method" value="vietqr" class="hidden">
                        <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center transition-all">
                            <div class="w-2.5 h-2.5 bg-yellow-500 rounded-full scale-0 transition-transform"></div>
                        </div>
                        <span class="material-symbols-rounded text-gray-600">qr_code_2</span>
                        <div class="flex-1">
                            <span class="font-semibold text-gray-800">Chuyển khoản qua VietQR</span>
                            <p class="text-xs text-gray-500">Quét mã QR bằng ứng dụng ngân hàng của bạn.</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="bg-white p-5 md:p-6 rounded-lg border border-gray-200 sticky top-8">
                <h3 class="font-bold text-lg mb-4 text-gray-800">Tóm tắt đơn hàng</h3>

                <div class="space-y-3 max-h-[250px] overflow-y-auto mb-4 pr-2 custom-scrollbar">
                    <?php foreach ($items as $item): ?>
                        <div class="flex gap-4 items-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-md overflow-hidden relative border">
                                <img src="./server/admin/<?= htmlspecialchars($item['image']) ?>"
                                     alt="<?= htmlspecialchars($item['name_product']) ?>"
                                     class="w-full h-full object-cover">
                                <span class="absolute top-0 right-0 bg-gray-600 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-bl-md font-bold">
                                    <?= (int)$item['quantity'] ?>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-800 truncate"><?= htmlspecialchars($item['name_product']) ?></h4>
                                <p class="text-sm text-gray-500"><?= number_format($item['price'], 0, ',', '.') ?>₫</p>
                            </div>
                            <div class="font-semibold text-sm text-gray-800">
                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>₫
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="space-y-2 py-4 border-t border-gray-200 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Tạm tính</span>
                        <span class="font-medium"><?= number_format($subtotal, 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Phí vận chuyển</span>
                        <span class="font-medium"><?= $shippingFee == 0 ? 'Miễn phí' : number_format($shippingFee, 0, ',', '.') . '₫' ?></span>
                    </div>
                    <?php if ($discountAmount > 0): ?>
                        <div class="flex justify-between text-green-600 font-medium">
                            <span>Giảm giá (<?= htmlspecialchars($couponCode) ?>)</span>
                            <span>-<?= number_format($discountAmount, 0, ',', '.') ?>₫</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex justify-between items-center py-4 border-t-2 border-dashed">
                    <span class="font-bold text-base text-gray-900">Tổng cộng</span>
                    <span class="font-bold text-xl md:text-2xl text-yellow-600"><?= number_format($total, 0, ',', '.') ?>₫</span>
                </div>

                <button type="submit" id="submit-button"
                        class="w-full mt-4 py-3 bg-gray-900 text-white rounded-lg font-bold hover:bg-yellow-600 transition-all shadow-lg flex items-center justify-center gap-2">
                    <span id="button-text">HOÀN TẤT ĐẶT HÀNG</span>
                </button>

                <p class="text-xs text-gray-500 mt-4 text-center">
                    Bằng việc nhấn nút, bạn đồng ý với <a href="#" class="underline">Chính sách & Điều khoản</a> của chúng tôi.
                </p>
            </div>
        </div>
    </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodsContainer = document.getElementById('payment-methods');
    const paymentOptions = paymentMethodsContainer.querySelectorAll('.payment-option');

    function updatePaymentSelection() {
        paymentOptions.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            const circle = option.querySelector('.w-5.h-5>div');
            if (radio.checked) {
                option.classList.add('selected');
                circle.classList.remove('scale-0');
            } else {
                option.classList.remove('selected');
                circle.classList.add('scale-0');
            }
        });
    }

    paymentOptions.forEach(option => {
        option.addEventListener('click', () => {
            option.querySelector('input[type="radio"]').checked = true;
            updatePaymentSelection();
        });
    });

    updatePaymentSelection();

    // Show loader on submit
    const form = document.getElementById('checkout-form');
    const overlay = document.getElementById('loader-overlay');
    form.addEventListener('submit', function() {
        overlay.classList.add('show');
    });
});
</script>

</body>
</html>
