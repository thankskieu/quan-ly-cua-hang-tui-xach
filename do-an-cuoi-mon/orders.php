<?php
require_once "server/models/user.php";
require_once "server/models/bill.php";
require_once "server/models/shipment.php";

$userObj = new User();
$account = $userObj->getAccount();
if (!$account['success']) {
    header("Location: auth.php");
    exit;
}
$user = $account['user'];

$billObj = new Bill();
$shipmentObj = new Shipment();

$orders = $billObj->getUserOrders($user['id']); 

// Attach shipment info
foreach ($orders as &$order) {
    $shipment = $shipmentObj->getShipmentByBillId($order['bill_id']);
    if ($shipment) {
        $order['shipment'] = $shipment;
        $order['tracking_logs'] = $shipmentObj->getTrackingLogs($shipment['id']);
    } else {
        $order['shipment'] = null;
        $order['tracking_logs'] = [];
    }
}
unset($order); // break reference

// Xử lý hủy đơn từ phía người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $cancelBillId = $_POST['bill_id'];
    
    // Lấy lý do hủy
    $reason = $_POST['reason'] ?? '';
    if ($reason === 'other') {
        $reason = $_POST['other_reason'] ?? 'Lý do khác';
    }
    
    // Gọi hàm cancelOrderUser từ model Bill
    // Lưu ý: Hiện tại hàm cancelOrderUser chưa lưu lý do vào DB (chưa có cột). 
    // Nếu muốn lưu, cần update DB và model. Ở đây ta cứ truyền vào updateStatus nếu cần, hoặc log lại.
    if ($billObj->cancelOrderUser($cancelBillId, $user['id'])) {
        // Reload trang để cập nhật trạng thái
        header("Location: orders.php?msg=cancelled");
        exit;
    } else {
        echo "<script>alert('Lỗi: Không thể hủy đơn hàng này (có thể đã được duyệt hoặc không tồn tại).');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đơn hàng của bạn • CHAUTFIFTH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@500;600&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />
    <style>
        body {
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial;
            background: #fefcf8;
            color: #111;
            margin: 0;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #3c3c3c;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5rem;
        }

        .order-card {
            background: #fffdfa;
            border-radius: 2rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            padding: 30px;
            transition: transform .3s, box-shadow .3s;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .order-id {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: #b68d40;
        }

        .order-date {
            color: #888;
            font-size: 0.9rem;
        }

        .order-info {
            margin-bottom: 10px;
        }

        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-cancel {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-continue {
            display: inline-block;
            margin-top: 20px;
            background: #b68d40;
            color: #fff;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-continue:hover {
            background: #e0c16a;
            color: #111;
        }

        .toggle-details {
            background: #b68d40;
            color: #fff;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .toggle-details:hover {
            background: #e0c16a;
            color: #111;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-content {
            background: #fffdfa;
            padding: 40px;
            border-radius: 2.5rem;
            width: 100%;
            max-width: 650px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.3);
            position: relative;
            transform: translateY(0);
            animation: modalFadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #888;
            transition: color 0.3s;
        }

        .close-modal:hover {
            color: #111;
        }

        .modal-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 15px;
            background: #fdf9f5;
            border-radius: 1rem;
        }

        .modal-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">

    <?php include_once "./component/header.php"; ?>

    <main class="container mx-auto p-6 flex-grow">
        <h2>Đơn hàng đã mua</h2>

        <?php if (empty($orders)): ?>
            <div class="flex flex-col items-center justify-center p-10 bg-white rounded-2xl shadow-lg">
                <p class="text-center text-gray-500 text-xl font-medium">Bạn chưa có đơn hàng nào.</p>
                <a href="product.php" class="btn-continue">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <div class="flex flex-col space-y-6 w-full">
                <?php foreach ($orders as $bill):
                    $status = strtolower($bill['status']);
                    $paymentMethod = strtolower($bill['payment_method'] ?? 'cod');

                    $statusText = '';
                    $statusClass = '';

                    if ($status === 'confirmed') {
                        $statusClass = 'status-approved';
                        if ($paymentMethod === 'vietqr') {
                            $statusText = 'Đã thanh toán';
                        } else {
                            $statusText = 'Đã xác nhận';
                        }
                    } elseif ($status === 'pending_payment') {
                        $statusClass = 'status-pending';
                        $statusText = 'Chờ thanh toán QR';
                    } elseif ($status === 'processing') {
                        $statusClass = 'status-approved';
                        $statusText = 'Đang xử lý (COD)';
                    } elseif ($status === 'shipping') {
                        $statusClass = 'status-approved';
                        $statusText = 'Đang giao hàng';
                    } elseif ($status === 'delivered' || $status === 'success') {
                        $statusClass = 'status-approved';
                        $statusText = 'Đã giao thành công';
                    } elseif ($status === 'cancelled' || $status === 'cancel') {
                        $statusClass = 'status-cancel';
                        $statusText = 'Đã hủy';
                    } elseif ($status === 'pending') {
                         $statusClass = 'status-pending';
                         $statusText = 'Chờ xác nhận';
                    } else {
                        $statusClass = 'status-pending';
                        $statusText = ucfirst($status);
                    }
                    ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span class="order-id">Mã đơn: #<?= $bill['bill_id'] ?></span>
                            <span class="order-date">Ngày đặt: <?= date('d/m/Y H:i', strtotime($bill['created_at'])) ?></span>
                        </div>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                            <div class="text-lg">
                                Tổng tiền: <strong
                                    class="text-rose-500 font-bold"><?= number_format($bill['total_price'], 0, ',', '.') ?>₫</strong>
                            </div>
                            <span class="status <?= $statusClass ?>"><?= $statusText ?></span>
                            <?php if ($status === 'pending'): ?>
                                <button onclick="cancelOrder(<?= $bill['bill_id'] ?>)" 
                                        class="ml-auto sm:ml-4 text-sm bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded-lg hover:bg-red-100 transition font-medium">
                                    Hủy đơn
                                </button>
                            <?php endif; ?>
                            <?php if ($status === 'payment_failed'): ?>
                                <a href="payment_proof.php?bill_id=<?= $bill['bill_id'] ?>" class="ml-auto sm:ml-4 text-sm bg-green-100 text-green-700 border border-green-300 px-3 py-1.5 rounded-lg hover:bg-green-200 transition font-semibold">
                                    Thanh toán lại
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="text-gray-600 mb-4">
                            <strong>Địa chỉ nhận hàng:</strong> <?= htmlspecialchars($bill['address']) ?>
                        </div>

                        <?php if (!empty($bill['shipment'])): ?>
                            <div class="mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-bold text-blue-800">Vận chuyển: <?= $bill['shipment']['carrier_name'] ?></span>
                                    <span class="text-xs font-mono bg-white px-2 py-0.5 rounded border border-blue-200"><?= $bill['shipment']['tracking_number'] ?></span>
                                </div>
                                <div class="text-sm text-blue-600">
                                    Trạng thái: 
                                    <span class="font-semibold">
                                        <?= match($bill['shipment']['status']) {
                                            'created' => 'Đã tạo vận đơn',
                                            'shipping' => 'Đang giao hàng',
                                            'delivered' => 'Đã giao thành công',
                                            'returned' => 'Đã hoàn trả',
                                            default => $bill['shipment']['status']
                                        } ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <a href="order_detail.php?id=<?= $bill['bill_id'] ?>" class="toggle-details">Xem chi tiết & Hành trình</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Modal hủy đơn hàng -->
    <div id="cancelModal" class="modal fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
        <div class="modal-content w-full max-w-md mx-4 bg-white rounded-[2rem] p-8 shadow-2xl transform transition-all relative">
            <button id="closeCancelModal" class="close-modal absolute top-5 right-5 text-gray-400 hover:text-gray-800 text-2xl transition-colors">&times;</button>
            <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center font-serif">Hủy đơn hàng</h3>
            
            <p class="text-gray-600 text-center mb-6">Vui lòng chọn lý do hủy đơn hàng của bạn. Hành động này không thể hoàn tác.</p>
            
            <form id="cancelForm" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="cancel">
                <input type="hidden" name="bill_id" id="cancelBillId">
                
                <div class="space-y-2">
                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="reason" value="Đặt nhầm sản phẩm" class="w-5 h-5 text-rose-500" checked>
                        <span>Đặt nhầm sản phẩm</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="reason" value="Muốn thay đổi địa chỉ/SĐT" class="w-5 h-5 text-rose-500">
                        <span>Muốn thay đổi địa chỉ/SĐT</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="reason" value="Tìm thấy giá rẻ hơn" class="w-5 h-5 text-rose-500">
                        <span>Tìm thấy giá rẻ hơn</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="reason" value="Đổi ý, không muốn mua nữa" class="w-5 h-5 text-rose-500">
                        <span>Đổi ý, không muốn mua nữa</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="reason" value="other" class="w-5 h-5 text-rose-500">
                        <span>Lý do khác:</span>
                    </label>
                    <textarea name="other_reason" id="otherReasonInput" rows="2" class="w-full border rounded p-2 hidden" placeholder="Nhập lý do của bạn..."></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-4 pt-4 border-t">
                    <button type="button" id="btnAbortCancel" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Đóng</button>
                    <button type="submit" class="px-4 py-2 bg-rose-500 text-white font-bold rounded-lg hover:bg-rose-600">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>

    <?php include_once "./component/footer.php"; ?>

    <script>
        // Modal Hủy Đơn
        const cancelModal = document.getElementById('cancelModal');
        const closeCancelModal = document.getElementById('closeCancelModal');
        const btnAbortCancel = document.getElementById('btnAbortCancel');
        const cancelBillIdInput = document.getElementById('cancelBillId');
        const cancelForm = document.getElementById('cancelForm');
        const otherReasonInput = document.getElementById('otherReasonInput');
        const reasonRadios = document.querySelectorAll('input[name="reason"]');

        // Make global for onclick attribute
        window.cancelOrder = function(billId) {
            if (cancelBillIdInput) cancelBillIdInput.value = billId;
            if (cancelModal) cancelModal.style.display = 'flex';
        }

        function hideCancelModal() {
            if (cancelModal) cancelModal.style.display = 'none';
        }

        if (closeCancelModal) closeCancelModal.addEventListener('click', hideCancelModal);
        if (btnAbortCancel) btnAbortCancel.addEventListener('click', hideCancelModal);
        if (cancelModal) cancelModal.addEventListener('click', (e) => { if (e.target === cancelModal) hideCancelModal(); });

        reasonRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (otherReasonInput) {
                    if (e.target.value === 'other') {
                        otherReasonInput.classList.remove('hidden');
                        otherReasonInput.required = true;
                    } else {
                        otherReasonInput.classList.add('hidden');
                        otherReasonInput.required = false;
                    }
                }
            });
        });
        
        // ... cancelForm logic ...
    </script>

</body>

</html>