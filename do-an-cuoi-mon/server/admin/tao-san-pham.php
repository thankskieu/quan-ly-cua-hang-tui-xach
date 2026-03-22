<?php include_once './phan-quyen.php'?>

<?php
require_once "../models/products.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name_product'];
    $price = $_POST['price'];
    $discount = $_POST['discount'] ?? 0;
    $description = $_POST['description'];
    $category = $_POST['category'] ?? 'Khác';

    // Thư mục uploads
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Xử lý upload ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $safeName = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $_FILES['image']['name']);
        $imageName = time() . '_' . $safeName;
        $imagePath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $imagePathDb = 'uploads/' . $imageName;
        } else {
            $imagePathDb = '';
        }
    } else {
        $imagePathDb = '';
    }

    $product = new Products();
    $result = $product->create($name, $price, $discount, $imagePathDb, $description, $category);
    $message = $result['message'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo sản phẩm</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h1 class="text-3xl font-extrabold mb-8 text-center text-gray-800 flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-8a1 1 0 002 0V7a1 1 0 10-2 0v3zm-3 3a1 1 0 100-2h6a1 1 0 100 2H6z" clip-rule="evenodd" />
            </svg>
            Tạo sản phẩm mới
        </h1>

        <?php if($message): ?>
            <div class="mb-6 p-4 rounded-lg <?= strpos($message, 'thành công') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> flex items-center gap-2 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">

            <div class="flex flex-col">
                <label for="name_product" class="text-gray-700 font-medium mb-1">Tên sản phẩm</label>
                <input type="text" id="name_product" name="name_product" placeholder="Nhập tên sản phẩm" 
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition" required>
            </div>
            
            <div class="flex flex-col">
                <label for="price" class="text-gray-700 font-medium mb-1">Giá sản phẩm</label>
                <input type="number" id="price" name="price" placeholder="Nhập giá sản phẩm" 
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition" required>
            </div>

            <div class="flex flex-col">
                <label for="discount" class="text-gray-700 font-medium mb-1">Giảm giá (%)</label>
                <input type="number" id="discount" name="discount" placeholder="Nhập phần trăm giảm giá (tùy chọn)" 
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition">
            </div>

            <div class="flex flex-col">
                <label for="category" class="text-gray-700 font-medium mb-1">Danh mục</label>
                <select id="category" name="category" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition" required>
                    <option value="Matcha">Matcha</option>
                    <option value="Trà Sữa">Trà Sữa</option>
                    <option value="Cafe">Cafe</option>
                    <option value="Khác">Khác</option>
                </select>
            </div>

            <div class="flex flex-col">
                <label for="image" class="text-gray-700 font-medium mb-1">Ảnh sản phẩm</label>
                <input type="file" id="image" name="image" accept="image/*" 
                    class="w-full border border-gray-300 rounded-lg p-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition">
            </div>

            <div class="flex flex-col">
                <label for="description" class="text-gray-700 font-medium mb-1">Mô tả sản phẩm</label>
                <textarea id="description" name="description" placeholder="Nhập mô tả sản phẩm" rows="4" 
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"></textarea>
            </div>

            <button type="submit" 
                class="w-full bg-emerald-500 text-white py-3 rounded-lg font-semibold hover:bg-emerald-600 transition flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l.607-.233a1 1 0 00.419-.922V10.707L2.247 13.59a1 1 0 001.213 1.585l.617-.237a1 1 0 00.419-.922V10a1 1 0 00-.28-.71L8.293 3.553a1 1 0 011.414 0l4.5 4.5a1 1 0 01-1.414 1.414L10 6.414V14a1 1 0 11-2 0V6.414L4.707 9.707A1 1 0 013.293 8.293L8.293 3.553a1 1 0 011.414 0L14.707 8.293a1 1 0 010 1.414L10.894 2.553z" />
                </svg>
                Tạo sản phẩm
            </button>

            <a href="./dashboard.php"
                class="w-full text-center bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400 transition flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0L3.586 10a1 1 0 010-1.414l4.707-4.707a1 1 0 011.414 1.414L6.414 9H16a1 1 0 110 2H6.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Trở về
            </a>
        </form>
    </div>
</div>

</body>
</html>
