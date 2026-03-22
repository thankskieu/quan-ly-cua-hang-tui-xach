<?php include_once './phan-quyen.php'?>

<?php
require_once "../models/products.php";

$product = new Products();
$message = "";

// Lấy ID sản phẩm từ URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID sản phẩm không hợp lệ.");
}

$id = intval($_GET['id']);
$item = $product->getById($id);

if (!$item) {
    die("Sản phẩm không tồn tại.");
}

    // Xử lý submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name_product'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $stock = intval($_POST['stock']); // Lấy stock từ form
    $description = $_POST['description'];
    $category = $_POST['category'] ?? $item['category']; // lấy category mới hoặc giữ cũ

    // Thư mục uploads
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Xử lý upload ảnh mới
    $imagePath = $item['image']; // giữ ảnh cũ
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $safeName = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $_FILES['image']['name']);
        $imageName = time() . '_' . $safeName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
            $imagePath = 'uploads/' . $imageName;
        }
    }

    // Truyền đủ 8 tham số (thêm stock)
    $result = $product->update($id, $name, $price, $discount, $stock, $imagePath, $description, $category);
    $message = $result['message'];

    if ($result['success']) {
        $item = $product->getById($id); // cập nhật dữ liệu mới
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h1 class="text-3xl font-extrabold mb-8 text-center text-gray-800 flex items-center justify-center gap-2">
            Sửa sản phẩm
        </h1>

        <?php if(!empty($message)): ?>
            <div class="mb-6 p-4 rounded-lg <?= strpos($message, 'thành công') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> flex items-center gap-2 font-medium">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">

            <div class="flex flex-col">
                <label for="name_product" class="text-gray-700 font-medium mb-1">Tên sản phẩm</label>
                <input type="text" id="name_product" name="name_product" value="<?= htmlspecialchars($item['name_product']) ?>"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <label for="price" class="text-gray-700 font-medium mb-1">Giá bán</label>
                    <input type="number" id="price" name="price" value="<?= htmlspecialchars($item['price']) ?>"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition" required>
                </div>
                <div class="flex flex-col">
                    <label for="stock" class="text-gray-700 font-medium mb-1">Tồn kho</label>
                    <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($item['stock']) ?>"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition" required>
                    <p class="text-xs text-gray-500 mt-1">Nhập -1 nếu không giới hạn</p>
                </div>
            </div>

            <div class="flex flex-col">
                <label for="discount" class="text-gray-700 font-medium mb-1">Giảm giá (%)</label>
                <input type="number" id="discount" name="discount" value="<?= htmlspecialchars($item['discount']) ?>"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition">
            </div>
            <div class="flex flex-col">
                <label for="description" class="text-gray-700 font-medium mb-1">Mô tả sản phẩm</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"><?= htmlspecialchars($item['description']) ?></textarea>
            </div>

            <div class="flex flex-col">
                <label for="category" class="text-gray-700 font-medium mb-1">Danh mục</label>
                <select id="category" name="category" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition">
                    <option value="Matcha" <?= $item['category']=='Matcha' ? 'selected' : '' ?>>Matcha</option>
                    <option value="Trà Sữa" <?= $item['category']=='Trà Sữa' ? 'selected' : '' ?>>Trà Sữa</option>
                    <option value="Cafe" <?= $item['category']=='Cafe' ? 'selected' : '' ?>>Cafe</option>
                    <option value="Khác" <?= $item['category']=='Khác' ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-gray-700 font-medium mb-2">Ảnh sản phẩm hiện tại</label>
                <?php if (!empty($item['image'])) : ?>
                    <div class="w-48 h-48 rounded-lg shadow-md overflow-hidden mb-4">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name_product']) ?>" class="w-full h-full object-cover">
                    </div>
                <?php else : ?>
                    <div class="w-48 h-48 rounded-lg bg-gray-200 flex items-center justify-center text-gray-400 mb-4">Không có ảnh</div>
                <?php endif; ?>

                <label for="image" class="text-gray-700 font-medium mb-1">Thay đổi ảnh mới</label>
                <input type="file" id="image" name="image" accept="image/*" 
                    class="w-full border border-gray-300 rounded-lg p-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition">
            </div>

            <div class="flex gap-4 mt-2">
                <a href="./dashboard.php"
                    class="flex-1 text-center bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400 transition">Trở về</a>
                <button type="submit" 
                    class="flex-1 bg-emerald-500 text-white py-3 rounded-lg font-semibold hover:bg-emerald-600 transition">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
