<?php
session_start();
$_SESSION['backpage'] = $_SERVER['REQUEST_URI'];
include_once "./server/models/products.php";

$productModel = new Products();

$search       = trim($_GET['search'] ?? '');
$minPrice     = floatval($_GET['min'] ?? 0);
$maxPrice     = floatval($_GET['max'] ?? 0);
$discountOnly = isset($_GET['discount']);

$allProducts = $productModel->getAll();

$products = array_filter($allProducts, function($p) use ($search, $minPrice, $maxPrice, $discountOnly) {
    $matchSearch     = $search === '' || stripos($p['name_product'], $search) !== false;
    $matchMin        = $minPrice <= 0 || $p['price'] >= $minPrice;
    $matchMax        = $maxPrice <= 0 || $p['price'] <= $maxPrice;
    $matchDiscount   = !$discountOnly || $p['discount'] > 0;
    return $matchSearch && $matchMin && $matchMax && $matchDiscount;
});
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>🛍️ Danh Sách Sản Phẩm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .gradient-text {
            background: linear-gradient(90deg, #2563eb, #9333ea);
            -webkit-background-clip: text;
            color: transparent;
        }
        .btn-glow:hover {
            box-shadow: 0 0 14px rgba(79, 70, 229, 0.4);
            transform: scale(1.03);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-purple-50 min-h-screen flex flex-col text-gray-800">

<?php include_once "./component/header.php"; ?>

<main class="container mx-auto px-4 sm:px-6 lg:px-10 py-12 flex-grow">
    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-center mb-12 gradient-text animate__animated animate__fadeInDown">
        ✨ Danh Sách Sản Phẩm
    </h1>

    <form method="GET" class="glass rounded-2xl shadow-xl p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-12" data-aos="fade-up">
        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
            <label class="block mb-1 font-semibold flex items-center gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
                Tìm kiếm
            </label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nhập tên sản phẩm..."
                   class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
        </div>

        <div>
            <label class="block mb-1 font-semibold flex items-center gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-3.31 0-6 2.69-6 6h12c0-3.31-2.69-6-6-6z" />
                </svg>
                Giá từ
            </label>
            <input type="number" name="min" min="0" step="1000" value="<?= $minPrice ?: '' ?>" placeholder="0 ₫"
                   class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-400">
        </div>

        <div>
            <label class="block mb-1 font-semibold flex items-center gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16M2 8h1m19 0h1" />
                </svg>
                Đến
            </label>
            <input type="number" name="max" min="0" step="1000" value="<?= $maxPrice ?: '' ?>" placeholder="Không giới hạn"
                   class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-400">
        </div>

        <div class="flex items-center gap-2 mt-2 lg:mt-8">
            <input type="checkbox" name="discount" <?= $discountOnly ? 'checked' : '' ?> class="w-5 h-5 accent-indigo-600">
            <label class="font-semibold flex items-center gap-1 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12l-8-8-8 8 8 8 8-8z" />
                </svg>
                Giảm giá
            </label>
        </div>

        <div class="flex justify-end items-end sm:col-span-2 lg:col-span-5 mt-4">
            <a href="product.php" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold transition mr-3">Đặt lại</a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold rounded-lg shadow transition btn-glow flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M4 8h16M5 12h14m-5 4h4" />
                </svg>
                Lọc
            </button>
        </div>
    </form>

    <?php if (empty($products)): ?>
        <p class="text-center text-gray-500 text-lg animate__animated animate__fadeIn">Không tìm thấy sản phẩm phù hợp.</p>
    <?php else: ?>
        <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 lg:gap-8">
            <?php foreach ($products as $p): ?>
                <div class="glass rounded-2xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1 animate__animated animate__fadeInUp flex flex-col" data-aos="zoom-in">
                    <div class="relative overflow-hidden rounded-t-2xl">
                        <img src="./server/admin/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name_product']) ?>" class="w-full h-52 sm:h-60 object-cover hover:scale-105 transition duration-300">
                        <?php if ($p['discount'] > 0): ?>
                            <span class="absolute top-3 left-3 bg-red-500 text-white text-xs px-2 py-1 rounded-lg shadow-lg">-<?= $p['discount'] ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-semibold text-lg text-gray-800 mb-2 line-clamp-2"><?= htmlspecialchars($p['name_product']) ?></h3>

                        <?php if ($p['discount'] > 0): ?>
                            <div class="mb-2">
                                <span class="text-indigo-600 font-bold text-lg mr-2">
                                    <?= number_format($p['price'] * (100 - $p['discount']) / 100, 0, ',', '.') ?> ₫
                                </span>
                                <span class="line-through text-gray-400 text-sm">
                                    <?= number_format($p['price'], 0, ',', '.') ?> ₫
                                </span>
                            </div>
                        <?php else: ?>
                            <p class="text-indigo-600 font-bold mb-2">
                                <?= number_format($p['price'], 0, ',', '.') ?> ₫
                            </p>
                        <?php endif; ?>

                        <div class="mt-auto flex flex-col gap-2">
                            <a href="product_detail.php?id=<?= $p['id'] ?>" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold transition btn-glow">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.458 12C2.732 7.943 6.523 5 12 5s9.268 2.943 10.542 7c-1.274 4.057-5.065 7-10.542 7S2.732 16.057 1.458 12z" />
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                Xem chi tiết
                            </a>

                            <form method="POST" action="cart_action.php">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($p['name_product']) ?>">
                                <input type="hidden" name="product_price" value="<?= $p['price'] * (100 - $p['discount']) / 100 ?>">
                                <input type="hidden" name="product_image" value="<?= htmlspecialchars($p['image']) ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_to_cart" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold transition btn-glow">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M9 21h.01M17 21h.01" />
                                    </svg>
                                    Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include_once "./component/footer.php"; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ duration: 800, once: true });</script>
</body>
</html>