<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/server/models/user.php";
require_once __DIR__ . "/server/models/bill.php";

$userModel = new User();
$billModel = new Bill();

// --- Auth check ---
$account = $userModel->getAccount();
if (!$account['success'] || !isset($account['user']['id'])) {
    header("Location: auth.php");
    exit;
}
$userId = (int)$account['user']['id'];

// --- Only POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}

// --- Get inputs ---
$cartIds        = $_POST['cart_ids'] ?? '';
$finalAmount    = isset($_POST['total_amount']) ? (float)$_POST['total_amount'] : 0;
$address        = trim($_POST['address'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$fullname       = trim($_POST['fullname'] ?? '');
$discountAmount = isset($_POST['discount_amount']) ? (float)$_POST['discount_amount'] : 0;
$paymentMethod  = $_POST['payment_method'] ?? 'cod';

// --- Validate ---
if (empty($cartIds) || $finalAmount < 0 || $address === '' || $phone === '' || $fullname === '') {
    $_SESSION['flash_message'] = 'Dữ liệu không hợp lệ. Vui lòng điền đầy đủ thông tin.';
    header("Location: checkout.php");
    exit;
}

$cartIdArray = array_values(array_filter(array_map('intval', explode(',', $cartIds))));
if (empty($cartIdArray)) {
    $_SESSION['flash_message'] = 'Giỏ hàng không hợp lệ.';
    header("Location: cart.php");
    exit;
}

// ✅ Tính lại subtotal & shipping từ DB cho chắc
try {
    $items = $billModel->getCartItems($cartIdArray, $userId);
    if (empty($items)) {
        $_SESSION['flash_message'] = 'Sản phẩm bạn chọn không hợp lệ hoặc đã bị xóa.';
        header("Location: cart.php");
        exit;
    }

    $subtotal = 0;
    foreach ($items as $it) {
        $subtotal += ((float)$it['price']) * ((int)$it['quantity']);
    }

    $shippingFee = ($subtotal > 500000) ? 0 : 30000;

    $recalculatedTotal = $subtotal + $shippingFee - $discountAmount;
    if ($recalculatedTotal < 0) $recalculatedTotal = 0;

    // Cho phép sai số 1đ
    if (abs($recalculatedTotal - $finalAmount) > 1) {
        $_SESSION['flash_message'] = 'Có lỗi xảy ra trong quá trình tính toán. Vui lòng thử lại.';
        header("Location: cart.php");
        exit;
    }
} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Lỗi khi kiểm tra giỏ hàng: ' . $e->getMessage();
    header("Location: cart.php");
    exit;
}

$couponId = $_SESSION['applied_coupon']['id'] ?? null;

// --- Build order data for Bill::processOrder() ---
$orderData = [
    'userId'          => $userId,
    'phone'           => $phone,
    'fullname'        => $fullname,
    'address'         => $address,
    'subtotal'        => $subtotal,
    'shipping_fee'    => $shippingFee,
    'discount_amount' => $discountAmount,
    'final_amount'    => $finalAmount,
    'coupon_id'       => $couponId,
    'cart_ids'        => $cartIdArray,
    'payment_method'  => $paymentMethod,
    'status'          => ($paymentMethod === 'cod') ? 'processing' : 'pending_payment'
];

try {
    $billId = $billModel->processOrder($orderData);

    // ✅ Nếu VietQR -> lưu thông tin QR để order_confirmation.php hiển thị
    if ($paymentMethod === 'vietqr') {
        // MB Bank
        $bankBin         = '970422';
        $bankAccount     = '0348854403';        // STK MB của bạn
        $bankAccountName = 'HOTHITHUYKIEU';      // Tên chủ TK
        $bankName        = 'MB Bank';
        // Nội dung chuyển khoản để đối soát
        $qrDescription = "CDPHP {$billId}";

        $_SESSION['qr_info'] = [
            'amount'       => (int)round($finalAmount),
            'account_no'   => $bankAccount,
            'account_name' => $bankAccountName,
            'bank_bin'     => $bankBin,
            'bank_name'    => $bankName,
            'description'  => $qrDescription,
        ];
    }

    unset($_SESSION['applied_coupon']);
    $_SESSION['last_order_id'] = $billId;

    header("Location: order_confirmation.php");
    exit;

} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Đã có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage();
    header("Location: checkout.php");
    exit;
}
