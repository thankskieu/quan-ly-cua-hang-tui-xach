<?php
require_once "../models/shipment.php";

$shipmentObj = new Shipment();

// Pagination setup
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Get total shipments count
$totalShipmentsCount = $shipmentObj->getTotalShipmentsCount();
$totalPages = ceil($totalShipmentsCount / $itemsPerPage);

// Fetch paginated shipments
$shipments = $shipmentObj->getAllShipments($itemsPerPage, $offset);
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Quản lý vận đơn</h2>
        <a href="tracking.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-sm flex items-center gap-2">
            <span class="material-symbols-rounded">search</span>
            Tra cứu vận đơn
        </a>
    </div>

    <?php if (empty($shipments)) : ?>
        <div class="p-8 bg-white rounded-2xl shadow-sm text-center border border-gray-100">
            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" alt="Empty" class="w-16 h-16 mx-auto mb-4 opacity-50">
            <p class="text-gray-500 font-medium">Chưa có vận đơn nào được tạo.</p>
        </div>
    <?php else : ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                            <th class="p-4">Mã vận đơn</th>
                            <th class="p-4">Đơn hàng</th>
                            <th class="p-4">Khách hàng</th>
                            <th class="p-4">Đơn vị VC</th>
                            <th class="p-4">Trạng thái</th>
                            <th class="p-4">Ngày tạo</th>
                            <th class="p-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($shipments as $shipment) : ?>
                            <tr class="hover:bg-blue-50/50 transition duration-150">
                                <td class="p-4 font-mono font-bold text-blue-600">
                                    <?= htmlspecialchars($shipment['tracking_number']) ?>
                                </td>
                                <td class="p-4">
                                    <a href="order_detail.php?bill_id=<?= $shipment['bill_id'] ?>" class="text-gray-900 font-medium hover:text-blue-600 hover:underline">
                                        #<?= $shipment['bill_id'] ?>
                                    </a>
                                   <td class="p-4 text-gray-700">
                                    <?= htmlspecialchars($shipment['customer_name'] ?? 'Khách lẻ') ?>
                                </td>
                                <td class="p-4 text-gray-600">
                                    <?= htmlspecialchars($shipment['carrier_name']) ?>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                                        <?= match($shipment['status']) {
                                            'delivered' => 'bg-green-100 text-green-700',
                                            'shipping' => 'bg-blue-100 text-blue-700',
                                            'returned' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        } ?>">
                                        <?= $shipment['status'] ?>
                                    </span>
                                </td>
                                <td class="p-4 text-gray-500 text-sm">
                                    <?= date('d/m/Y H:i', strtotime($shipment['created_at'])) ?>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="tracking.php?code=<?= $shipment['tracking_number'] ?>" 
                                           class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem hành trình">
                                           <span class="material-symbols-rounded">visibility</span>
                                        </a>
                                        <a href="order_detail.php?bill_id=<?= $shipment['bill_id'] ?>" 
                                           class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Cập nhật">
                                            <span class="material-symbols-rounded">edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Controls -->
        <nav class="flex justify-center items-center gap-2 mt-8">
            <?php if ($currentPage > 1): ?>
                <a href="?tab=van-don&page=<?= $currentPage - 1 ?>" class="flex items-center justify-center px-4 h-10 me-3 text-base font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700">
                    <span class="material-symbols-rounded text-lg">chevron_left</span>
                    Trước
                </a>
            <?php endif; ?>

            <ul class="flex items-center -space-x-px h-10 text-base">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li>
                        <a href="?tab=van-don&page=<?= $i ?>" class="flex items-center justify-center px-4 h-10 leading-tight <?= $i == $currentPage ? 'text-blue-600 bg-blue-50 border border-blue-300 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?tab=van-don&page=<?= $currentPage + 1 ?>" class="flex items-center justify-center px-4 h-10 ms-3 text-base font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700">
                    Tiếp
                    <span class="material-symbols-rounded text-lg">chevron_right</span>
                </a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>

<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
