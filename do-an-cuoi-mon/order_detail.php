<?php
require_once "server/models/user.php";
require_once "server/models/bill.php";
require_once "server/models/shipment.php";

// --- Authentication and Authorization ---
$userObj = new User();
$account = $userObj->getAccount();
if (!$account['success']) {
    header("Location: auth.php");
    exit;
}
$user = $account['user'];

// --- Get Bill ID and Security Check ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: orders.php");
    exit;
}
$bill_id = intval($_GET['id']);

$billObj = new Bill();
$shipmentObj = new Shipment();

// --- Handle Actions (like changing payment method) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_to_cod') {
    // Re-fetch order to double-check status before updating
    $tempOrder = $billObj->getOrderByIdAndUserId($bill_id, $user['id']);
    if ($tempOrder && strtolower($tempOrder['status']) === 'payment_failed') {
        if ($billObj->updateStatus($bill_id, 'pending')) {
            header("Location: order_detail.php?id=$bill_id&msg=changed_to_cod");
            exit;
        } else {
            // Optional: add an error message
            header("Location: order_detail.php?id=$bill_id&error=change_failed");
            exit;
        }
    }
}


// Fetch the specific order and verify it belongs to the logged-in user
$order = $billObj->getOrderByIdAndUserId($bill_id, $user['id']);

if (!$order) {
    // If order doesn't exist or doesn't belong to the user, redirect
    header("Location: orders.php?error=notfound");
    exit;
}

// --- Fetch Additional Details ---
// Items are already included in getOrderByIdAndUserId
$shipment = $shipmentObj->getShipmentByBillId($order['bill_id']);
if ($shipment) {
    $order['shipment'] = $shipment;
    $order['tracking_logs'] = $shipmentObj->getTrackingLogs($shipment['id']);
} else {
    $order['shipment'] = null;
    $order['tracking_logs'] = [];
}

// --- Map Statuses for Display ---
$status = strtolower($order['status']);
$statusClass = match ($status) {
    'processing', 'shipping', 'delivered', 'approved' => 'bg-green-100 text-green-800',
    'pending' => 'bg-yellow-100 text-yellow-800',
    'cancelled', 'cancel', 'payment_failed' => 'bg-red-100 text-red-800',
    default => 'bg-gray-100 text-gray-800'
};
$statusText = match ($status) {
    'processing' => 'Đang xử lý',
    'shipping' => 'Đang giao hàng',
    'delivered' => 'Đã giao thành công',
    'approved' => 'Đã duyệt',
    'pending' => 'Chờ thanh toán (COD)',
    'cancelled', 'cancel' => 'Đã hủy',
    'payment_failed' => 'Thanh toán thất bại',
    default => ucfirst($status)
};
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết Đơn hàng #<?= $order['bill_id'] ?> • CHAUTFIFTH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fefcf8; }
        h1, h2, h3, .font-playfair { font-family: 'Playfair Display', serif; }
        .timeline-item:last-child .timeline-line { display: none; }
    </style>
</head>

<body class="min-h-screen flex flex-col">

    <?php include_once "./component/header.php"; ?>

    <main class="container mx-auto p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-4">
                <a href="orders.php" class="text-yellow-700 hover:text-yellow-900 font-medium flex items-center gap-2 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                    Quay lại danh sách đơn hàng
                </a>
                <div class="flex items-center gap-3">
                    <?php 
                        // WORKAROUND: The main bill status sometimes fails to update to 'payment_failed'.
                        // Rely on the more reliable payment_status ('rejected') to show the buttons.
                        $is_bank_transfer_for_button = in_array(strtolower($order['status']), ['pending_payment', 'payment_failed', 'processing']) || (isset($order['payment_status']));
                        if ($is_bank_transfer_for_button && isset($order['payment_status']) && $order['payment_status'] === 'rejected'): 
                    ?>
                        <a href="payment_proof.php?bill_id=<?= $order['bill_id'] ?>" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow-sm hover:bg-green-700 transition">
                            Thanh toán lại
                        </a>
                        <form method="POST" action="order_detail.php?id=<?= $order['bill_id'] ?>" onsubmit="return confirm('Bạn có chắc muốn đổi phương thức thanh toán sang COD không?');">
                            <input type="hidden" name="action" value="change_to_cod">
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                                Đổi sang COD
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'changed_to_cod'): ?>
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg relative mb-4" role="alert">
                    <strong class="font-bold">Thành công!</strong>
                    <span class="block sm:inline">Đã đổi phương thức thanh toán của đơn hàng thành COD.</span>
                </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <!-- Order Header -->
                <div class="p-6 bg-gray-50/50 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold font-playfair text-gray-900">Chi tiết Đơn hàng</h1>
                            <p class="text-gray-500 mt-1">Mã đơn: <span class="font-semibold text-gray-700">#<?= $order['bill_id'] ?></span></p>
                        </div>
                        <div class="text-right">
                             <p class="text-sm text-gray-500">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                             <div class="mt-1 px-3 py-1 text-sm font-bold rounded-full inline-block <?= $statusClass ?>">
                                <?= $statusText ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Items -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Các sản phẩm trong đơn</h3>
                        <div class="space-y-4">
                            <?php foreach ($order['items'] as $item): ?>
                            <div class="flex items-center gap-4 p-4 bg-white border border-gray-100 rounded-xl">
                                <img src="./server/admin/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name_product']) ?>" class="w-20 h-20 object-cover rounded-lg shadow-sm">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($item['name_product']) ?></p>
                                    <p class="text-sm text-gray-500">Số lượng: <?= $item['quantity'] ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900"><?= number_format($item['price'] * $item['quantity']) ?>₫</p>
                                    <p class="text-sm text-gray-500"> (<?= number_format($item['price']) ?>₫/sp)</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                         <!-- Totals -->
                        <div class="mt-6 pt-4 border-t-2 border-dashed space-y-1.5 text-gray-600">
                             <?php 
                                $shipping_fee = $order['shipment']['shipping_fee'] ?? 0;
                                // "Tổng tiền hàng" is the subtotal (raw price of products)
                                $product_total = $order['subtotal']; 
                             ?>
                             <div class="flex justify-between items-center">
                                <span>Tổng tiền hàng:</span> 
                                <span><?= number_format($product_total) ?>₫</span>
                             </div>
                             <div class="flex justify-between items-center">
                                <span>Phí vận chuyển:</span> 
                                <span><?php echo ($shipping_fee == 0) ? 'Freeship' : number_format($shipping_fee).'₫'; ?></span>
                            </div>
                            <?php if ($order['discount_amount'] > 0): ?>
                            <div class="flex justify-between items-center text-red-600">
                                <span>Voucher giảm giá:</span> 
                                <span>-<?= number_format($order['discount_amount']) ?>₫</span>
                            </div>
                            <?php endif; ?>
                             <div class="flex justify-between items-center font-bold text-xl text-gray-900 mt-2 pt-2 border-t border-dashed">
                                <span>Tổng thanh toán:</span> 
                                <span class="text-rose-600"><?= number_format($order['total_price']) ?>₫</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Tracking & Info -->
                    <div>
                        <!-- Shipping Info -->
                        <div class="bg-yellow-50/40 p-5 rounded-xl border border-yellow-200/60 mb-6">
                             <h3 class="text-lg font-bold text-yellow-900/80 mb-3">Thông tin nhận hàng</h3>
                             <div class="space-y-1 text-sm text-gray-700">
                                 <p><strong class="font-semibold">Người nhận:</strong> <?= htmlspecialchars($order['username']) ?></p>
                                 <p><strong class="font-semibold">Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
                                 <p><strong class="font-semibold">SĐT:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                             </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="bg-blue-50/40 p-5 rounded-xl border border-blue-200/60 mb-6">
                             <h3 class="text-lg font-bold text-blue-900/80 mb-3">Thông tin thanh toán</h3>
                             <div class="space-y-1.5 text-sm text-gray-700">
                                <?php
                                    // CORRECTED: Use the definitive payment_method from the database
                                    $is_bank_transfer = isset($order['payment_method']) && $order['payment_method'] === 'vietqr';

                                    if ($is_bank_transfer) {
                                        $payment_method_text = 'Chuyển khoản ngân hàng';
                                    } else {
                                        $payment_method_text = 'Thanh toán khi nhận hàng (COD)';
                                    }
                                ?>
                                 <p><strong class="font-semibold w-28 inline-block">P.thức:</strong> <?= $payment_method_text ?></p>
                                 
                                 <?php if ($order['coupon_code']): ?>
                                 <p><strong class="font-semibold w-28 inline-block">Voucher:</strong> <span class="font-mono text-xs bg-gray-200 text-gray-800 px-2 py-0.5 rounded"><?= htmlspecialchars($order['coupon_code']) ?></span></p>
                                 <?php endif; ?>

                                 <?php if ($is_bank_transfer): 
                                    $p_status = $order['payment_status'];
                                    $p_text = 'Chưa thanh toán';
                                    $p_class = 'text-gray-500';
                                    if ($p_status === 'pending_verification') {
                                        $p_text = 'Đang chờ xác minh';
                                        $p_class = 'text-yellow-600';
                                                } elseif ($p_status === 'verified') {
                                                    $p_text = 'Đã thanh toán';                                        $p_class = 'text-green-600';
                                    } elseif ($p_status === 'rejected') {
                                        $p_text = 'Bị từ chối';
                                        $p_class = 'text-red-600';
                                    }
                                 ?>
                                 <p><strong class="font-semibold w-28 inline-block">TT Thanh toán:</strong> <span class="font-bold <?= $p_class ?>"><?= $p_text ?></span></p>
                                 <?php endif; ?>
                             </div>
                        </div>
                        
                        <!-- Tracking -->
                        <?php if ($order['shipment'] && !empty($order['tracking_logs'])): ?>
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Hành trình đơn hàng</h3>
                            <div class="relative pl-6">
                                <?php foreach ($order['tracking_logs'] as $log): ?>
                                <div class="timeline-item pb-6">
                                    <div class="absolute -left-1.5 mt-1 w-3 h-3 bg-blue-500 rounded-full border-2 border-white"></div>
                                    <div class="timeline-line absolute left-0 h-full w-0.5 bg-gray-200"></div>
                                    <p class="text-xs text-gray-500 mb-0.5"><?= date('d/m/Y H:i', strtotime($log['updated_at'])) ?></p>
                                    <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($log['status_text']) ?></p>
                                    <?php if ($log['location']): ?>
                                        <p class="text-xs text-gray-600">Tại: <?= htmlspecialchars($log['location']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                             <div class="bg-gray-50 p-5 rounded-xl border text-center">
                                 <h3 class="text-lg font-bold text-gray-800 mb-2">Chưa có hành trình</h3>
                                 <p class="text-sm text-gray-500">Thông tin vận chuyển sẽ được cập nhật khi đơn hàng được xử lý.</p>
                             </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include_once "./component/footer.php"; ?>

</body>
</html>
