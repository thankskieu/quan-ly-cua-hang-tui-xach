<?php
include_once './phan-quyen.php';
require_once "../models/shipment.php";
require_once "../models/bill.php";

$trackingCode = $_GET['code'] ?? '';
$shipment = null;
$logs = [];
$orderItems = [];
$error = '';

if ($trackingCode) {
    $shipmentObj = new Shipment();
    $shipment = $shipmentObj->getShipmentByTrackingNumber($trackingCode);
    
    if ($shipment) {
        $logs = $shipmentObj->getTrackingLogs($shipment['id']);
        
        // Fetch order items
        $billObj = new Bill();
        $orderItems = $billObj->getItemsByBillId($shipment['bill_id']);
    } else {
        $error = "Không tìm thấy vận đơn với mã: " . htmlspecialchars($trackingCode);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Tra cứu vận đơn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; }
    </style>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Tra cứu hành trình đơn hàng</h1>
            <a href="dashboard.php" class="text-blue-600 hover:underline">Quay lại Dashboard</a>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <form method="GET" class="flex gap-4">
                <input type="text" name="code" value="<?= htmlspecialchars($trackingCode) ?>" 
                       placeholder="Nhập mã vận đơn (VD: TRK...)" 
                       class="flex-1 p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg shadow-blue-200">
                    Tra cứu
                </button>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-200 text-center font-medium animate-pulse">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($shipment): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- LEFT COLUMN: TRACKING INFO -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Tracking Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-blue-100 text-xs font-medium uppercase tracking-wider mb-1">Mã vận đơn</p>
                                    <p class="text-2xl font-mono font-bold tracking-wide"><?= htmlspecialchars($shipment['tracking_number']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-blue-100 text-xs font-medium uppercase tracking-wider mb-1">Đơn vị vận chuyển</p>
                                    <div class="bg-white/20 px-3 py-1 rounded-lg backdrop-blur-sm inline-block">
                                        <p class="text-sm font-bold"><?= htmlspecialchars($shipment['carrier_name']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-8 pb-6 border-b border-dashed border-gray-200">
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">Trạng thái</p>
                                    <span class="px-3 py-1 rounded-full text-sm font-bold uppercase tracking-wide
                                        <?= match($shipment['status']) {
                                            'delivered' => 'bg-green-100 text-green-700',
                                            'shipping' => 'bg-blue-100 text-blue-700',
                                            'returned' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        } ?>">
                                        <?= $shipment['status'] ?>
                                    </span>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-500 text-sm mb-1">Ngày tạo</p>
                                    <p class="font-medium text-gray-800"><?= date('d/m/Y H:i', strtotime($shipment['created_at'])) ?></p>
                                </div>
                            </div>

                            <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                                <span class="material-symbols-rounded text-blue-600">timeline</span>
                                Chi tiết hành trình
                            </h3>
                            <div class="relative border-l-2 border-gray-200 ml-3 space-y-8 pl-8 pb-2">
                                <?php if (empty($logs)): ?>
                                    <p class="text-gray-500 italic text-sm">Chưa có cập nhật nào về hành trình.</p>
                                <?php else: ?>
                                    <?php foreach ($logs as $index => $log): ?>
                                        <div class="relative group">
                                            <div class="absolute -left-[41px] top-1.5 h-5 w-5 rounded-full border-4 border-white transition-colors duration-300
                                                <?= $index === 0 ? 'bg-blue-600 ring-4 ring-blue-50' : 'bg-gray-300 group-hover:bg-gray-400' ?>"></div>
                                            <p class="text-xs text-gray-500 mb-1 font-mono"><?= date('H:i - d/m/Y', strtotime($log['updated_at'])) ?></p>
                                            <h4 class="text-base font-bold text-gray-800"><?= htmlspecialchars($log['status_text']) ?></h4>
                                            <?php if ($log['location']): ?>
                                                <p class="text-sm text-gray-600 mt-0.5 flex items-center gap-1">
                                                    <span class="material-symbols-rounded text-sm text-gray-400">location_on</span>
                                                    <?= htmlspecialchars($log['location']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: ORDER INFO -->
                <div class="space-y-6">
                    <!-- Customer Info -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2">
                            <span class="material-symbols-rounded text-gray-500">person</span>
                            Thông tin người nhận
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs">Họ tên</p>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($shipment['customer_name'] ?? 'Khách lẻ') ?></p>
                            </div>
                            <?php if (!empty($shipment['customer_email'])): ?>
                            <div>
                                <p class="text-gray-500 text-xs">Email</p>
                                <!-- Mask email for privacy -->
                                <p class="font-medium text-gray-700"><?= htmlspecialchars($shipment['customer_email']) ?></p>
                            </div>
                            <?php endif; ?>
                            <div>
                                <p class="text-gray-500 text-xs">Địa chỉ giao hàng</p>
                                <p class="font-medium text-gray-700"><?= htmlspecialchars($shipment['address']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2">
                            <span class="material-symbols-rounded text-gray-500">shopping_bag</span>
                            Chi tiết đơn hàng
                        </h3>
                        <div class="space-y-4 max-h-[400px] overflow-y-auto pr-1 custom-scrollbar">
                            <?php foreach ($orderItems as $item): ?>
                                <div class="flex gap-3">
                                    <div class="w-16 h-16 rounded-lg overflow-hidden border border-gray-100 flex-shrink-0">
                                        <img src="./<?= htmlspecialchars($item['image']) ?>" 
                                             class="w-full h-full object-cover" alt="Product">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 line-clamp-2" title="<?= htmlspecialchars($item['name_product']) ?>">
                                            <?= htmlspecialchars($item['name_product']) ?>
                                        </p>
                                        <div class="flex justify-between items-end mt-1">
                                            <p class="text-xs text-gray-500">SL: <?= $item['quantity'] ?></p>
                                            <p class="text-sm font-bold text-rose-600"><?= number_format($item['price'], 0, ',', '.') ?>₫</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-gray-600 font-medium">Tổng tiền</span>
                            <span class="text-xl font-bold text-rose-600"><?= number_format($shipment['total_price'], 0, ',', '.') ?>₫</span>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</body>
</html>
