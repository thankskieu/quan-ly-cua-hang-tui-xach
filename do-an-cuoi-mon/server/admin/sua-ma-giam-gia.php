<?php
include_once './phan-quyen.php';
require_once "../models/coupon.php";

$couponObj = new Coupon();
$errors = [];
$success = '';

// Check for ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php?tab=ma-giam-gia");
    exit;
}
$id = intval($_GET['id']);
$coupon = $couponObj->getById($id);

// If coupon doesn't exist, redirect
if (!$coupon) {
    header("Location: dashboard.php?tab=ma-giam-gia");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $discount_type = $_POST['discount_type'] ?? 'fixed';
    $discount_value = floatval($_POST['discount_value'] ?? 0);
    $min_order_value = floatval($_POST['min_order_value'] ?? 0);
    $expiry_date = $_POST['expiry_date'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($code)) $errors[] = "Mã code không được để trống.";
    if ($discount_value <= 0) $errors[] = "Giá trị giảm giá phải lớn hơn 0.";

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

        if ($couponObj->update($id, $data)) {
            header("Location: dashboard.php?tab=ma-giam-gia&success=2");
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi cập nhật. Vui lòng thử lại.";
        }
    }
    // Update coupon array with submitted data to show in form
    $coupon = array_merge($coupon, $_POST);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Mã Giảm Giá</title>
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
    <h1 class="text-3xl font-bold text-gray-800 font-playfair mb-8">Chỉnh Sửa Mã Giảm Giá</h1>

    <?php if (!empty($errors)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
            ...
        </div>
    <?php endif; ?>

    <form action="sua-ma-giam-gia.php?id=<?= $id ?>" method="POST" class="space-y-6">
        
        <div>
            <label for="code" class="block text-sm font-bold text-gray-700 mb-2">Mã Code (*)</label>
            <input type="text" id="code" name="code" required class="block w-full px-4 py-3 border border-gray-300 rounded-lg" value="<?= htmlspecialchars($coupon['code']) ?>">
        </div>

        <div>
            <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Mô tả</label>
            <textarea id="description" name="description" rows="3" class="block w-full px-4 py-3 border border-gray-300 rounded-lg"><?= htmlspecialchars($coupon['description']) ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="discount_type" class="block text-sm font-bold text-gray-700 mb-2">Loại giảm giá</label>
                <select id="discount_type" name="discount_type" class="block w-full px-4 py-3 border bg-white border-gray-300 rounded-lg">
                    <option value="fixed" <?= ($coupon['discount_type'] === 'fixed') ? 'selected' : '' ?>>Giá trị cố định (VNĐ)</option>
                    <option value="percent" <?= ($coupon['discount_type'] === 'percent') ? 'selected' : '' ?>>Phần trăm (%)</option>
                </select>
            </div>
            <div>
                <label for="discount_value" class="block text-sm font-bold text-gray-700 mb-2">Giá trị giảm (*)</label>
                <input type="number" step="0.01" id="discount_value" name="discount_value" required class="block w-full px-4 py-3 border border-gray-300 rounded-lg" value="<?= htmlspecialchars($coupon['discount_value']) ?>">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="min_order_value" class="block text-sm font-bold text-gray-700 mb-2">Giá trị đơn hàng tối thiểu</label>
                <input type="number" step="0.01" id="min_order_value" name="min_order_value" class="block w-full px-4 py-3 border border-gray-300 rounded-lg" value="<?= htmlspecialchars($coupon['min_order_value']) ?>">
            </div>
            <div>
                <label for="expiry_date" class="block text-sm font-bold text-gray-700 mb-2">Ngày hết hạn</label>
                <input type="datetime-local" id="expiry_date" name="expiry_date" class="block w-full px-4 py-3 border border-gray-300 rounded-lg" value="<?= !empty($coupon['expiry_date']) ? date('Y-m-d\TH:i', strtotime($coupon['expiry_date'])) : '' ?>">
            </div>
        </div>
        
        <div class="flex items-center">
            <input id="is_active" name="is_active" type="checkbox" class="h-5 w-5 text-yellow-600 border-gray-300 rounded" <?= ($coupon['is_active']) ? 'checked' : '' ?>>
            <label for="is_active" class="ml-3 block text-sm font-medium text-gray-900">Kích hoạt</label>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="dashboard.php?tab=ma-giam-gia" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition">Hủy</a>
            <button type="submit" class="px-8 py-3 bg-black text-white rounded-lg font-semibold hover:bg-yellow-600 transition">Cập Nhật</button>
        </div>
    </form>
</div>

</body>
</html>
