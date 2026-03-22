<?php include_once './phan-quyen.php' ?>

<?php
require_once '../models/bill.php';
require_once '../models/shipment.php';

// ✅ Khởi tạo Bill object
$billObj = new Bill();
$shipmentObj = new Shipment();

// ✅ Lấy bill_id từ URL hoặc POST
$billId = $_GET['bill_id'] ?? $_POST['bill_id'] ?? null;
if (!$billId) {
    die('❌ Không có ID đơn hàng.');
}

$billId = intval($billId);

// ✅ Lấy thông tin đơn hàng
// Đã sửa lỗi: Gọi hàm getById() thay cho getOrderById()
$order = $billObj->getOrderById($billId);
if (!$order) {
    die('❌ Không tìm thấy đơn hàng.');
}

// ✅ Lấy danh sách sản phẩm của đơn hàng
$items = $billObj->getItemsByBillId($billId);

// ✅ Lấy thông tin vận chuyển (nếu có)
$shipment = $shipmentObj->getShipmentByBillId($billId);
$trackingLogs = $shipment ? $shipmentObj->getTrackingLogs($shipment['id']) : [];

// ✅ Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve') {
        if ($billObj->updateStatus($billId, 'confirmed')) {
            header("Location: order_detail.php?bill_id=$billId&msg=order_confirmed");
            exit;
        } else {
            $error = "Lỗi: Không thể xác nhận đơn hàng. Vui lòng thử lại.";
        }
    } elseif ($action === 'cancel') {
        $billObj->updateStatus($billId, 'cancelled');
        header("Location: dashboard.php?tab=don-hang&status=cancelled");
        exit;
    } elseif ($action === 'create_shipment') {
        $carrier = $_POST['carrier_name'] ?? 'Giao Hàng Nhanh'; // Default carrier
        $trackingCode = $shipmentObj->createShipment($billId, $carrier);
        if ($trackingCode) {
            header("Location: order_detail.php?bill_id=$billId");
            exit;
        } else {
            $error = "Lỗi khi tạo vận đơn.";
        }
    } elseif ($action === 'update_tracking') {
        $shipmentId = $_POST['shipment_id'];
        $statusText = $_POST['status_text'];
        $location = $_POST['location'];
        $newStatus = $_POST['new_status'];
        
        $shipmentObj->addTrackingLog($shipmentId, $statusText, $location);
        if ($newStatus && $newStatus !== $shipment['status']) {
            $shipmentObj->updateShipmentStatus($shipmentId, $newStatus);
        }
        header("Location: order_detail.php?bill_id=$billId");
        exit;
    } elseif ($action === 'verify_payment') {
        if ($billObj->verifyPayment($billId)) {
            header("Location: order_detail.php?bill_id=$billId&msg=payment_verified");
            exit;
        } else {
            $error = "Lỗi: Không thể xác nhận thanh toán. Vui lòng thử lại.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?= $billId ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl">

        <div class="flex items-center justify-between mb-8 border-b pb-4">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-pink-500" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path d="M7.5 18a.75.75 0 00.75.75h9a.75.75 0 00.75-.75V8.25H7.5V18z" />
                    <path
                        d="M18.75 1.5a.75.75 0 00-.75.75V3h-1.5V2.25a.75.75 0 00-1.5 0V3h-9V2.25a.75.75 0 00-1.5 0V3H4.5A2.25 2.25 0 002.25 5.25v12a2.25 2.25 0 002.25 2.25h12a2.25 2.25 0 002.25-2.25V5.25A2.25 2.25 0 0018.75 3V2.25a.75.75 0 00-.75-.75zM4.5 4.5h15a.75.75 0 01.75.75V6a.75.75 0 01-.75.75H4.5A.75.75 0 013.75 6V5.25A.75.75 0 014.5 4.5z" />
                </svg>
                Chi tiết đơn hàng #<?= htmlspecialchars($billId) ?>
            </h1>

            <a href="dashboard.php?tab=don-hang"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 14.707a1 1 0 01-1.414 0L3.586 10a1 1 0 010-1.414l4.707-4.707a1 1 0 011.414 1.414L6.414 9H16a1 1 0 110 2H6.414l3.293 3.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Quay lại
            </a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'order_confirmed'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Thành công!</strong>
                <span class="block sm:inline">Đơn hàng đã được xác nhận.</span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'payment_verified'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Thành công!</strong>
                <span class="block sm:inline">Thanh toán đã được xác minh.</span>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-gray-50 p-6 rounded-xl shadow-inner mb-8">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Thông tin đơn hàng</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <p>
                    <strong class="flex items-center gap-2 text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-500" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.255.484l-.407.284a.75.75 0 01-.625.132l-.25-.054a.75.75 0 01-.527-.275l-4.502-5.46a.75.75 0 00-1.127 0l-4.502 5.46a.75.75 0 01-.527.275l-.25.054a.75.75 0 01-.625-.132l-.407-.284a.75.75 0 01-.255-.484z"
                                clip-rule="evenodd" />
                        </svg>
                        Người mua:
                    </strong>
                    <?= htmlspecialchars($order['username'] ?? '') ?>
                </p>
                <p class="md:col-span-2">
                    <strong class="flex items-center gap-2 text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-500" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12 2.25c5.385 0 9.75 4.365 9.75 9.75s-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12 6.615 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z"
                                clip-rule="evenodd" />
                        </svg>
                        Ngày tạo:
                    </strong>
                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                </p>
                <p class="md:col-span-2">
                    <strong class="flex items-center gap-2 text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-500" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s-4.365 9.75-9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm0 15a.75.75 0 00-.75.75v.75a.75.75 0 001.5 0v-.75a.75.75 0 00-.75-.75zM12 6a.75.75 0 00-.75.75v6a.75.75 0 001.5 0v-6a.75.75 0 00-.75-.75z"
                                clip-rule="evenodd" />
                        </svg>
                        Địa chỉ:
                    </strong>
                    <?= htmlspecialchars($order['address']) ?>
                </p>
            </div>
            <p class="text-2xl font-bold text-right mt-4">
                Tổng tiền: <span class="text-pink-600"><?= number_format($order['total_price'], 0, ',', '.') ?>₫</span>
            </p>
        </div>



        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Sản phẩm trong đơn hàng</h2>
            <?php if (empty($items)): ?>
                <p class="text-gray-500">Đơn hàng này không có sản phẩm.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($items as $item): ?>
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl shadow-sm hover:bg-white transition duration-200 border border-transparent hover:border-blue-200">
                            <a href="xem-san-pham.php?id=<?= $item['product_id'] ?>" class="block flex-shrink-0" title="Xem chi tiết sản phẩm">
                                <img src="./<?= htmlspecialchars($item['image']) ?>" alt="ảnh"
                                    class="w-20 h-20 object-cover rounded-lg shadow hover:opacity-90 transition">
                            </a>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">
                                    <a href="xem-san-pham.php?id=<?= $item['product_id'] ?>" class="hover:text-blue-600 hover:underline">
                                        <?= htmlspecialchars($item['name_product']) ?>
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600">Số lượng: <?= htmlspecialchars($item['quantity']) ?></p>
                                <p class="text-sm text-pink-600 font-bold mt-1">
                                    <?= number_format($item['price'], 0, ',', '.') ?>₫
                                </p>
                            </div>
                            <div class="text-lg font-bold text-right">
                                <span class="text-gray-700">Thành tiền:</span>
                                <br>
                                <span
                                    class="text-pink-600"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>₫</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-8 border-t pt-8">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Vận chuyển & Giao hàng</h2>
            
            <?php if (!$shipment): ?>
                <?php if ($order['status'] === 'confirmed'): ?>
                    <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                        <h3 class="font-bold text-blue-800 mb-2">Bàn giao vận chuyển</h3>
                        <p class="text-sm text-blue-600 mb-4">Đơn hàng đã được duyệt. Tạo vận đơn để bắt đầu giao hàng.</p>
                        <form method="POST" class="flex gap-4">
                            <input type="hidden" name="bill_id" value="<?= $billId ?>">
                            <input type="hidden" name="action" value="create_shipment">
                            <select name="carrier_name" class="p-2 border rounded-lg flex-1">
                                <option value="Giao Hàng Nhanh">Giao Hàng Nhanh</option>
                                <option value="Giao Hàng Tiết Kiệm">Giao Hàng Tiết Kiệm</option>
                                <option value="Viettel Post">Viettel Post</option>
                                <option value="J&T Express">J&T Express</option>
                            </select>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                                Tạo vận đơn
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">Cần duyệt đơn hàng trước khi tạo vận đơn.</p>
                <?php endif; ?>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Thông tin vận đơn -->
                    <div class="bg-white p-6 rounded-xl border shadow-sm">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Thông tin vận chuyển</h3>
                        <div class="space-y-3">
                            <p><span class="text-gray-500">Mã vận đơn:</span> <span class="font-mono font-bold text-blue-600"><?= $shipment['tracking_number'] ?></span></p>
                            <p><span class="text-gray-500">Đơn vị:</span> <span class="font-medium"><?= $shipment['carrier_name'] ?></span></p>
                            <p><span class="text-gray-500">Trạng thái:</span> 
                                <span class="px-2 py-1 rounded text-xs font-bold 
                                    <?= match($shipment['status']) {
                                        'created' => 'bg-gray-200 text-gray-700',
                                        'shipping' => 'bg-yellow-100 text-yellow-700',
                                        'delivered' => 'bg-green-100 text-green-700',
                                        'returned' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100'
                                    } ?>">
                                    <?= strtoupper($shipment['status']) ?>
                                </span>
                            </p>
                            <p><span class="text-gray-500">Ngày tạo:</span> <?= date('d/m/Y H:i', strtotime($shipment['created_at'])) ?></p>
                        </div>
                    </div>

                    <!-- Cập nhật trạng thái -->
                    <div class="bg-gray-50 p-6 rounded-xl border">
                        <h3 class="font-bold text-gray-800 mb-4">Cập nhật hành trình</h3>
                        <form method="POST" class="space-y-3">
                            <input type="hidden" name="bill_id" value="<?= $billId ?>">
                            <input type="hidden" name="action" value="update_tracking">
                            <input type="hidden" name="shipment_id" value="<?= $shipment['id'] ?>">
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Trạng thái mới</label>
                                <input type="text" name="status_text" required placeholder="Ví dụ: Đã đến kho TP.HCM" class="w-full p-2 border rounded text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Vị trí hiện tại</label>
                                <input type="text" name="location" placeholder="Ví dụ: Kho Quận 7" class="w-full p-2 border rounded text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Cập nhật trạng thái đơn</label>
                                <select name="new_status" class="w-full p-2 border rounded text-sm">
                                    <option value="shipping" <?= $shipment['status'] == 'shipping' ? 'selected' : '' ?>>Đang giao hàng (Shipping)</option>
                                    <option value="delivered" <?= $shipment['status'] == 'delivered' ? 'selected' : '' ?>>Đã giao (Delivered)</option>
                                    <option value="returned" <?= $shipment['status'] == 'returned' ? 'selected' : '' ?>>Hoàn trả (Returned)</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition">
                                Cập nhật
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Timeline Log -->
                <div class="mt-6">
                    <h3 class="font-bold text-gray-800 mb-4">Lịch sử hành trình</h3>
                    <div class="relative border-l-2 border-gray-200 ml-3 space-y-6 pl-6 pb-2">
                        <?php foreach ($trackingLogs as $log): ?>
                            <div class="relative">
                                <div class="absolute -left-[31px] bg-blue-500 h-4 w-4 rounded-full border-2 border-white"></div>
                                <p class="text-sm text-gray-500 mb-1"><?= date('d/m/Y H:i', strtotime($log['updated_at'])) ?></p>
                                <h4 class="font-bold text-gray-800"><?= htmlspecialchars($log['status_text']) ?></h4>
                                <?php if ($log['location']): ?>
                                    <p class="text-sm text-gray-600">Tại: <?= htmlspecialchars($log['location']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="flex space-x-4">


            <?php if ($order['status'] === 'pending'): ?>
                <form method="POST">
                    <input type="hidden" name="bill_id" value="<?= $billId ?>">
                    <button type="submit" name="action" value="approve"
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl transition font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M19.968 9.243a.75.75 0 01-1.071 0L10.5 17.63l-4.464-4.464a.75.75 0 111.06-1.06l3.93 3.93 7.436-7.436a.75.75 0 011.07 0zM12 2.25c5.385 0 9.75 4.365 9.75 9.75s-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12 6.615 2.25 12 2.25z"
                                clip-rule="evenodd" />
                        </svg>
                        Xác nhận
                    </button>
                </form>
                <form method="POST">
                    <input type="hidden" name="bill_id" value="<?= $billId ?>">
                    <button type="submit" name="action" value="cancel"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl transition font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75S21.75 17.385 21.75 12 17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.68 1.68a.75.75 0 101.06 1.06l1.68-1.68 1.68 1.68a.75.75 0 101.06-1.06L13.06 12l1.68-1.68a.75.75 0 10-1.06-1.06l-1.68 1.68-1.68-1.68z"
                                clip-rule="evenodd" />
                        </svg>
                        Hủy đơn
                    </button>
                </form>
            <?php elseif ($order['payment_method'] === 'vietqr' && $order['payment_status'] !== 'verified' && $order['status'] === 'confirmed'): ?>
                <form method="POST">
                    <input type="hidden" name="bill_id" value="<?= $billId ?>">
                    <button type="submit" name="action" value="verify_payment"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl transition font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.968 9.243a.75.75 0 01-1.071 0L10.5 17.63l-4.464-4.464a.75.75 0 111.06-1.06l3.93 3.93 7.436-7.436a.75.75 0 011.07 0zM12 2.25c5.385 0 9.75 4.365 9.75 9.75s-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12 6.615 2.25 12 2.25z" />
                        </svg>
                        Xác nhận thanh toán
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>