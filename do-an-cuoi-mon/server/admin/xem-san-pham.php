<?php include_once './phan-quyen.php'?>

<?php
require_once "../models/products.php";

$product = new Products();

// Lấy ID sản phẩm từ URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID sản phẩm không hợp lệ.");
}

$id = intval($_GET['id']);
$item = $product->getById($id);

if (!$item) {
    die("Sản phẩm không tồn tại.");
}

// Tính giá sau giảm giá
$discountedPrice = $item['price'] - ($item['price'] * ($item['discount'] / 100));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem sản phẩm</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6">
            <h1 class="text-3xl font-extrabold mb-6 text-center text-gray-800"><?= htmlspecialchars($item['name_product']) ?></h1>

            <div class="flex flex-col items-center">
                <?php if (!empty($item['image'])) : ?>
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name_product']) ?>" class="w-full h-80 object-cover rounded-xl shadow-lg mb-6">
                <?php else : ?>
                    <div class="w-full h-80 bg-gray-200 rounded-xl shadow-lg flex items-center justify-center text-gray-400 mb-6">Không có ảnh</div>
                <?php endif; ?>

                <div class="w-full space-y-4">
                    <!-- Giá và giảm giá -->
                    <p class="text-lg text-gray-700 flex items-center gap-2">
                        <span class="text-emerald-500 font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V4m0 8v4m0 4.5h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        Giá: 
                        <?php if (!empty($item['discount']) && $item['discount'] > 0) : ?>
                            <span class="text-gray-500 line-through"><?= number_format($item['price'], 0, ',', '.') ?>₫</span>
                            <span class="text-emerald-600 font-extrabold text-xl ml-2"><?= number_format($discountedPrice, 0, ',', '.') ?>₫</span>
                            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">-<?= intval($item['discount']) ?>%</span>
                        <?php else: ?>
                            <span class="text-emerald-600 font-bold text-xl"><?= number_format($item['price'], 0, ',', '.') ?>₫</span>
                        <?php endif; ?>
                    </p>

                    <!-- Tồn kho -->
                    <p class="text-lg text-gray-700 flex items-center gap-2 mt-2">
                        <span class="text-gray-500 font-semibold">Tồn kho:</span>
                        <span class="text-red-600 font-bold"><?= intval($item['stock']) ?></span>
                    </p>

                    <!-- Mô tả sản phẩm -->
                    <div class="text-gray-700">
                        <p class="font-bold text-lg mb-2">Mô tả sản phẩm:</p>
                        <p class="bg-gray-50 p-4 rounded-lg"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                    </div>
                </div>

                <!-- Nút quay lại -->
                <a href="dashboard.php" class="mt-8 flex items-center gap-2 px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0L3.586 10a1 1 0 010-1.414l4.707-4.707a1 1 0 011.414 1.414L6.414 9H16a1 1 0 110 2H6.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Quay lại
                </a>
            </div>
        </div>
    </div>

</body>
</html>
