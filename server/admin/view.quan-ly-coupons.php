<?php
// This file is included from dashboard.php, so it has access to $conn, $message, $error, etc.

// Fetch all coupons to display
$coupons = $conn->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Handle Edit Request
$edit_coupon = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_coupon = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?php if ($message): ?><div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-md" role="alert"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-md" role="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<!-- Add/Edit Form -->
<div class="bg-white p-6 rounded-2xl shadow-lg mb-8 border border-gray-100">
    <h2 class="text-xl font-bold mb-4 text-gray-800"><?= $edit_coupon ? 'Sửa Mã giảm giá' : 'Thêm Mã giảm giá mới' ?></h2>
    <form action="dashboard.php?tab=coupons" method="POST">
        <?php if ($edit_coupon): ?>
            <input type="hidden" name="id" value="<?= $edit_coupon['id'] ?>">
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Code -->
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700">Mã (Code)</label>
                <input type="text" name="code" value="<?= htmlspecialchars($edit_coupon['code'] ?? '') ?>" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" required>
            </div>
            
            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Loại</label>
                <select name="type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <option value="percentage" <?= ($edit_coupon['type'] ?? '') == 'percentage' ? 'selected' : '' ?>>Phần trăm (%)</option>
                    <option value="fixed" <?= ($edit_coupon['type'] ?? '') == 'fixed' ? 'selected' : '' ?>>Số tiền cố định</option>
                </select>
            </div>

            <!-- Value -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Giá trị</label>
                <input type="number" name="value" step="0.01" value="<?= htmlspecialchars($edit_coupon['value'] ?? '') ?>" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" required>
            </div>

            <!-- Max Discount -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Giảm tối đa (cho %)</label>
                <input type="number" name="max_discount_amount" step="0.01" value="<?= htmlspecialchars($edit_coupon['max_discount_amount'] ?? '') ?>" placeholder="Để trống nếu không giới hạn" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>

            <!-- Min Order Value -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Đơn hàng tối thiểu</label>
                <input type="number" name="min_order_value" step="0.01" value="<?= htmlspecialchars($edit_coupon['min_order_value'] ?? '0') ?>" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>

            <!-- Max Uses -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Tổng lượt sử dụng</label>
                <input type="number" name="max_uses" value="<?= htmlspecialchars($edit_coupon['max_uses'] ?? '0') ?>" placeholder="0 = không giới hạn" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            
            <!-- Usage per User -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Lượt/Người dùng</label>
                <input type="number" name="usage_limit_per_user" value="<?= htmlspecialchars($edit_coupon['usage_limit_per_user'] ?? '1') ?>" placeholder="0 = không giới hạn" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                <input type="datetime-local" name="start_date" value="<?= $edit_coupon['start_date'] ? date('Y-m-d\TH:i', strtotime($edit_coupon['start_date'])) : '' ?>" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
                <input type="datetime-local" name="end_date" value="<?= $edit_coupon['end_date'] ? date('Y-m-d\TH:i', strtotime($edit_coupon['end_date'])) : '' ?>" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>

            <!-- Description -->
            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                <textarea name="description" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"><?= htmlspecialchars($edit_coupon['description'] ?? '') ?></textarea>
            </div>

            <!-- Toggles -->
            <div class="flex items-center gap-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500" <?= !isset($edit_coupon) || $edit_coupon['is_active'] ? 'checked' : '' ?>>
                    <span class="ml-2 text-sm text-gray-900">Kích hoạt</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_public" value="1" class="h-4 w-4 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500" <?= ($edit_coupon['is_public'] ?? 0) ? 'checked' : '' ?>>
                    <span class="ml-2 text-sm text-gray-900">Công khai (Săn mã)</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <button type="submit" name="save_coupon" class="bg-black hover:bg-yellow-700 text-white font-bold py-2.5 px-8 rounded-xl shadow-lg transition-all">
                <?= $edit_coupon ? 'Lưu thay đổi' : 'Thêm mới' ?>
            </button>
            <?php if ($edit_coupon): ?>
                <a href="dashboard.php?tab=coupons" class="text-gray-600 hover:text-gray-900 font-medium">Hủy</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Coupon List -->
<div class="bg-white p-6 rounded-2xl shadow-lg overflow-x-auto border border-gray-100">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Danh sách Mã giảm giá</h2>
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50/50">
            <tr>
                <th scope="col" class="px-4 py-3">Mã</th>
                <th scope="col" class="px-4 py-3">Loại</th>
                <th scope="col" class="px-4 py-3">Giá trị</th>
                <th scope="col" class="px-4 py-3">Đơn tối thiểu</th>
                <th scope="col" class="px-4 py-3">Sử dụng</th>
                <th scope="col" class="px-4 py-3">Trạng thái</th>
                <th scope="col" class="px-4 py-3">Hiệu lực</th>
                <th scope="col" class="px-4 py-3">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($coupons)): ?>
                <tr><td colspan="8" class="text-center py-8 text-gray-400">Chưa có mã giảm giá nào.</td></tr>
            <?php else: ?>
                <?php foreach ($coupons as $coupon): ?>
                    <tr class="bg-white border-b hover:bg-yellow-50/30">
                        <td class="px-4 py-4 font-medium text-gray-900"><?= htmlspecialchars($coupon['code']) ?></td>
                        <td class="px-4 py-4"><?= htmlspecialchars($coupon['type']) ?></td>
                        <td class="px-4 py-4 font-semibold"><?= $coupon['type'] == 'percentage' ? rtrim(rtrim($coupon['value'], '0'), '.').'%' : number_format($coupon['value']).'đ' ?></td>
                        <td class="px-4 py-4"><?= number_format($coupon['min_order_value']) ?>đ</td>
                        <td class="px-4 py-4"><?= $coupon['used_count'] ?> / <?= $coupon['max_uses'] == 0 ? '∞' : $coupon['max_uses'] ?></td>
                        <td class="px-4 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $coupon['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $coupon['is_active'] ? 'Hoạt động' : 'Tắt' ?>
                            </span>
                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $coupon['is_public'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $coupon['is_public'] ? 'Công khai' : 'Riêng tư' ?>
                            </span>
                        </td>
                        <td class="px-4 py-4 text-xs">
                            Từ: <?= $coupon['start_date'] ? date('d/m/y H:i', strtotime($coupon['start_date'])) : 'N/A' ?><br>
                            Đến: <?= $coupon['end_date'] ? date('d/m/y H:i', strtotime($coupon['end_date'])) : 'N/A' ?>
                        </td>
                        <td class="px-4 py-4 flex items-center gap-3">
                            <a href="?tab=coupons&edit=<?= $coupon['id'] ?>" class="font-medium text-blue-600 hover:underline">Sửa</a>
                            <a href="?tab=coupons&delete=<?= $coupon['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa mã này?')" class="font-medium text-red-600 hover:underline">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
