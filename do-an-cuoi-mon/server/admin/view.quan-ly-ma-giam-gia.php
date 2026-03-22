<?php
require_once __DIR__ . "/../models/coupon.php";

$couponObj = new Coupon();
$coupons = $couponObj->getAll();
?>

<div class="space-y-6">
    <!-- Header: Title and Add Button -->
    <div class="flex justify-between items-center">
        <div>
            <!-- This part is managed by dashboard.php -->
        </div>
        <a href="tao-ma-giam-gia.php" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-black text-white rounded-xl font-semibold hover:bg-yellow-600 transition-all">
            <span class="material-symbols-rounded">add_circle</span>
            Thêm Mã Mới
        </a>
    </div>

    <!-- Coupon Table -->
    <?php if (empty($coupons)) : ?>
        <div class="p-12 bg-white rounded-xl shadow-sm border border-gray-100 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-6">
                <span class="material-symbols-rounded text-4xl text-gray-300">coupon</span>
            </div>
            <p class="text-gray-500 text-lg">Chưa có mã giảm giá nào được tạo.</p>
        </div>
    <?php else : ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-900 font-bold text-xs uppercase tracking-wider font-sans">
                        <tr class="border-b border-gray-200">
                            <th class="p-5">Mã Code</th>
                            <th class="p-5">Mô tả</th>
                            <th class="p-5">Loại & Giá trị</th>
                            <th class="p-5">Đơn Tối Thiểu</th>
                            <th class="p-5">Ngày hết hạn</th>
                            <th class="p-5">Trạng thái</th>
                            <th class="p-5 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($coupons as $coupon) : ?>
                            <tr class="hover:bg-yellow-50/50 transition-colors group">
                                <td class="p-5 font-medium text-yellow-600 font-mono text-sm"><?= htmlspecialchars($coupon['code']) ?></td>
                                <td class="p-5 text-sm text-gray-600 max-w-xs truncate" title="<?= htmlspecialchars($coupon['description']) ?>">
                                    <?= htmlspecialchars($coupon['description']) ?>
                                </td>
                                <td class="p-5">
                                    <p class="font-bold text-gray-900">
                                        <?= $coupon['discount_type'] === 'percent' ? $coupon['discount_value'] . '%' : number_format($coupon['discount_value'], 0, ',', '.') . '₫' ?>
                                    </p>
                                    <p class="text-xs text-gray-500"><?= $coupon['discount_type'] === 'percent' ? 'Phần trăm' : 'Giá cố định' ?></p>
                                </td>
                                <td class="p-5 text-sm text-gray-800 font-medium">
                                    <?= number_format($coupon['min_order_value'], 0, ',', '.') ?>₫
                                </td>
                                <td class="p-5 text-sm text-gray-500">
                                    <?= $coupon['expiry_date'] ? date('d/m/Y', strtotime($coupon['expiry_date'])) : 'Không hết hạn' ?>
                                </td>
                                <td class="p-5">
                                    <?php
                                        $isActive = $coupon['is_active'] && (!$coupon['expiry_date'] || strtotime($coupon['expiry_date']) > time());
                                        $statusClass = $isActive ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100';
                                        $statusLabel = $isActive ? 'Hiệu lực' : 'Hết hạn';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="p-5 text-center">
                                    <a href="sua-ma-giam-gia.php?id=<?= $coupon['id'] ?>" 
                                       class="w-9 h-9 inline-flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-black transition-all"
                                       title="Chỉnh sửa">
                                        <span class="material-symbols-rounded text-xl">edit</span>
                                    </a>
                                    <a href="xoa-ma-giam-gia.php?id=<?= $coupon['id'] ?>" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này không?');"
                                       class="w-9 h-9 inline-flex items-center justify-center rounded-full hover:bg-red-50 text-gray-400 hover:text-red-600 transition-all"
                                       title="Xóa">
                                        <span class="material-symbols-rounded text-xl">delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
