<?php
include_once './phan-quyen.php';
require_once "../models/coupon.php";

$couponObj = new Coupon();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $discount_type = $_POST['discount_type'] ?? 'fixed';
    $discount_value = floatval($_POST['discount_value'] ?? 0);
    $min_order_value = floatval($_POST['min_order_value'] ?? 0);
    $expiry_date = $_POST['expiry_date'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Basic validation
    if (empty($code)) $errors[] = "Mã code không được để trống.";
    if ($discount_value <= 0) $errors[] = "Giá trị giảm giá phải lớn hơn 0.";
    if ($discount_type === 'percent' && $discount_value > 100) $errors[] = "Phần trăm giảm giá không thể lớn hơn 100.";

    if (empty($errors)) {
        $data = [
            'code' => $code,
            'description' => $description,
            'discount_type' => $discount_type,
            'discount_value' => $discount_value,
            'min_order_value' => $min_order_value,
            'expiry_date' => $expiry_date,
            'is_active' => $is_active,
        ];

        if ($couponObj->create($data)) {
            // Redirect to the list page after successful creation
            header("Location: dashboard.php?tab=ma-giam-gia&success=1");
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi tạo mã. Vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Mã Giảm Giá Mới</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .font-playfair { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="p-8">

<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl p-8">
    <h1 class="text-3xl font-bold text-gray-800 font-playfair mb-8">Tạo Mã Giảm Giá Mới</h1>

    <?php if (!empty($errors)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
            <strong class="font-bold">Lỗi!</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li>- <?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="tao-ma-giam-gia.php" method="POST" class="space-y-6">
        
        <div>
            <label for="code" class="block text-sm font-bold text-gray-700 mb-2">Mã Code (*)</label>
            <input type="text" id="code" name="code" required class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500" value="<?= htmlspecialchars($_POST['code'] ?? '') ?>">
        </div>

        <div>
            <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Mô tả</label>
            <textarea id="description" name="description" rows="3" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="discount_type" class="block text-sm font-bold text-gray-700 mb-2">Loại giảm giá</label>
                <select id="discount_type" name="discount_type" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="fixed" <?= (($_POST['discount_type'] ?? '') === 'fixed') ? 'selected' : '' ?>>Giá trị cố định (VNĐ)</option>
                    <option value="percent" <?= (($_POST['discount_type'] ?? '') === 'percent') ? 'selected' : '' ?>>Phần trăm (%)</option>
                </select>
            </div>
            <div>
                <label for="discount_value" class="block text-sm font-bold text-gray-700 mb-2">Giá trị giảm (*)</label>
                <input type="number" step="0.01" id="discount_value" name="discount_value" required class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500" value="<?= htmlspecialchars($_POST['discount_value'] ?? '') ?>">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="min_order_value" class="block text-sm font-bold text-gray-700 mb-2">Giá trị đơn hàng tối thiểu</label>
                <input type="number" step="0.01" id="min_order_value" name="min_order_value" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500" value="<?= htmlspecialchars($_POST['min_order_value'] ?? '0') ?>">
            </div>
            <div>
                <label for="expiry_date" class="block text-sm font-bold text-gray-700 mb-2">Ngày hết hạn (để trống nếu không có)</label>
                <input type="datetime-local" id="expiry_date" name="expiry_date" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500" value="<?= htmlspecialchars($_POST['expiry_date'] ?? '') ?>">
            </div>
        </div>
        
        <div class="flex items-center">
            <input id="is_active" name="is_active" type="checkbox" class="h-5 w-5 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500" checked>
            <label for="is_active" class="ml-3 block text-sm font-medium text-gray-900">Kích hoạt mã giảm giá này</label>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="dashboard.php?tab=ma-giam-gia" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition">
                Hủy
            </a>
            <button type="submit" class="px-8 py-3 bg-black text-white rounded-lg font-semibold hover:bg-yellow-600 transition">
                Tạo Mã
            </button>
        </div>

    </form>
</div>

</body>
</html>
