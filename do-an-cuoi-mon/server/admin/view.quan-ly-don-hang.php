<?php
require_once __DIR__ . "/../models/bill.php";

$billObj = new Bill();

// Pagination setup
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Get total orders count
$totalOrdersCount = $billObj->getTotalOrdersCount();
$totalPages = ceil($totalOrdersCount / $itemsPerPage);

// Fetch paginated orders
$orders = $billObj->getAllOrders($itemsPerPage, $offset); 
?>

<div>
    <?php if (empty($orders)) : ?>
        <div class="p-12 bg-white rounded-xl shadow-sm border border-gray-100 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-6">
                <span class="material-symbols-rounded text-4xl text-gray-300">receipt_long</span>
            </div>
            <p class="text-gray-500 text-lg font-playfair italic">Chưa có đơn hàng nào.</p>
        </div>
    <?php else : ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-900 font-bold text-xs uppercase tracking-wider font-sans">
                        <tr class="border-b border-gray-200">
                            <th class="p-5">Mã đơn</th>
                            <th class="p-5">Khách hàng</th>
                            <th class="p-5">Tổng tiền</th>
                            <th class="p-5">Trạng thái</th>
                            <th class="p-5">Địa chỉ</th>
                            <th class="p-5">Ngày tạo</th>
                            <th class="p-5 text-center">Chi tiết</th>
                            <th class="p-5 text-center">Xuất Hóa Đơn</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($orders as $order) : ?>
                            <tr class="hover:bg-yellow-50/50 transition-colors group">
                                <td class="p-5 font-medium text-yellow-600 font-mono">#<?= $order['bill_id'] ?></td>
                                <td class="p-5">
                                    <p class="font-semibold text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></p>
                                </td>
                                <td class="p-5 font-bold text-gray-900 font-playfair text-lg"><?= number_format($order['total_price'],0,',','.') ?>₫</td>
                                <td class="p-5" style="min-width: 250px;">
                                    <form method="POST" action="dashboard.php?tab=don-hang" class="flex items-center gap-2">
                                        <input type="hidden" name="action" value="quick_update_status">
                                        <input type="hidden" name="bill_id" value="<?= $order['bill_id'] ?>">
                                        <select name="new_status" class="p-2 border rounded-lg text-sm w-full">
                                            <?php 
                                            $statuses = [
                                                'pending' => 'Chờ xác nhận',
                                                'pending_payment' => 'Chờ thanh toán QR',
                                                'confirmed' => 'Đã xác nhận',
                                                'processing' => 'Đang xử lý',
                                                'shipping' => 'Đang giao',
                                                'delivered' => 'Đã giao',
                                                'cancelled' => 'Đã hủy',
                                                'returned' => 'Đã hoàn trả'
                                            ];
                                            foreach ($statuses as $value => $label): ?>
                                                <option value="<?= $value ?>" <?= $order['status'] === $value ? 'selected' : '' ?>>
                                                    <?= $label ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" title="Lưu" class="p-2 bg-gray-200 hover:bg-green-600 hover:text-white rounded-lg transition-colors">
                                            <span class="material-symbols-rounded">save</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="p-5 text-gray-500 text-sm max-w-xs truncate font-sans" title="<?= htmlspecialchars($order['address']) ?>">
                                    <?= htmlspecialchars($order['address']) ?>
                                </td>
                                <td class="p-5 text-gray-500 text-sm font-sans">
                                    <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                                    <span class="text-xs text-gray-400 ml-1"><?= date('H:i', strtotime($order['created_at'])) ?></span>
                                </td>
                                <td class="p-5 text-center">
                                    <a href="order_detail.php?bill_id=<?= $order['bill_id'] ?>" 
                                       class="w-9 h-9 inline-flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-black transition-all"
                                       title="Xem chi tiết">
                                        <span class="material-symbols-rounded text-xl">visibility</span>
                                    </a>
                                </td>
                                <td class="p-5 text-center">
                                    <a href="generate_invoice.php?bill_id=<?= $order['bill_id'] ?>" 
                                       class="w-9 h-9 inline-flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-black transition-all"
                                       title="Xuất hóa đơn">
                                        <span class="material-symbols-rounded text-xl">description</span>
                                    </a>
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
                <a href="?tab=don-hang&page=<?= $currentPage - 1 ?>" class="flex items-center justify-center px-4 h-10 me-3 text-base font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700">
                    <span class="material-symbols-rounded text-lg">chevron_left</span>
                    Trước
                </a>
            <?php endif; ?>

            <ul class="flex items-center -space-x-px h-10 text-base">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li>
                        <a href="?tab=don-hang&page=<?= $i ?>" class="flex items-center justify-center px-4 h-10 leading-tight <?= $i == $currentPage ? 'text-yellow-600 bg-yellow-50 border border-yellow-300 hover:bg-yellow-100 hover:text-yellow-700' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?tab=don-hang&page=<?= $currentPage + 1 ?>" class="flex items-center justify-center px-4 h-10 ms-3 text-base font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700">
                    Tiếp
                    <span class="material-symbols-rounded text-lg">chevron_right</span>
                </a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>
