<?php
require_once __DIR__ . "/../models/bill.php";

$billObj = new Bill();

// Lấy danh sách các đơn hàng đang chờ thanh toán (pending_payment)
$pendingOrders = $billObj->getOrdersByStatus('pending_payment', 'vietqr');
?>

<div>
    <?php if (empty($pendingOrders)) : ?>
        <div class="p-12 bg-white rounded-xl shadow-sm border border-gray-100 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-50 mb-6">
                <span class="material-symbols-rounded text-4xl text-green-400">task_alt</span>
            </div>
            <p class="text-gray-600 text-lg font-playfair italic">Không có đơn hàng nào chờ xác nhận.</p>
        </div>
    <?php else : ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-900 font-bold text-xs uppercase tracking-wider font-sans">
                        <tr class="border-b border-gray-200">
                            <th class="p-4">Mã đơn</th>
                            <th class="p-4">Khách hàng</th>
                            <th class="p-4">Tổng tiền</th>
                            <th class="p-4">Phương thức</th>
                            <th class="p-4">Ngày tạo</th>
                            <th class="p-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($pendingOrders as $order) : ?>
                            <tr class="hover:bg-yellow-50/50 transition-colors">
                                <td class="p-4 font-medium text-yellow-600 font-mono">#<?= $order['bill_id'] ?></td>
                                <td class="p-4 font-semibold text-gray-800"><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td class="p-4 font-bold text-gray-800"><?= number_format($order['total_price'],0,',','.') ?>₫</td>
                                <td class="p-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold border bg-cyan-50 text-cyan-700 border-cyan-100">
                                        <?= strtoupper($order['payment_method']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-gray-500 text-sm">
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <form method="POST" action="">
                                            <input type="hidden" name="bill_id" value="<?= $order['bill_id'] ?>">
                                            <button type="submit" name="action" value="approve"
                                                class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 shadow-sm hover:shadow-md">
                                                <span class="material-symbols-rounded text-base">task_alt</span>
                                                Xác nhận
                                            </button>
                                        </form>
                                        <a href="order_detail.php?bill_id=<?= $order['bill_id'] ?>" 
                                            class="p-1.5 inline-flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all"
                                            title="Xem chi tiết">
                                            <span class="material-symbols-rounded text-xl">visibility</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
