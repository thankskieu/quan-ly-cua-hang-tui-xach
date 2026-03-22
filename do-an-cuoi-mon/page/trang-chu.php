<?php
// Bắt đầu session nếu chưa active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../server/models/products.php";
require_once __DIR__ . "/../server/models/user.php";
require_once __DIR__ . "/../server/models/cart.php";

$productObj = new Products();
$userObj = new User();
$account = $userObj->getAccount();
$loggedIn = $account['success'];
$user = $loggedIn ? $account['user'] : null;

// Lấy danh sách ID sản phẩm trong wishlist của user
$wishlistIds = [];
if ($loggedIn) {
    require_once __DIR__ . "/../server/database/database.php"; // Đảm bảo đã include Database
    $db = new Database();
    $conn = $db->connect();
    $stmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $wishlistIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Lấy số lượng giỏ hàng
$cartCount = 0;
if ($loggedIn) {
    $cartObj = new Cart();
    $cartData = $cartObj->getTotalItems();
    $cartCount = $cartData['success'] ? $cartData['total_items'] : 0;
}

// Lấy New Arrivals & Best Sellers
$newArrivals = $productObj->getNewArrivals(5);
$bestSellers = $productObj->getBestSellers(4);

// Lấy 5 category đầu tiên từ database
$allCategories = $productObj->getAllCategories();
$categories = array_slice($allCategories, 0, 5);

// Lấy sản phẩm cho từng category (5 sản phẩm mỗi category)
$categoryProducts = [];
foreach ($categories as $cat_assoc) {
    $cat_name = $cat_assoc['name'];
    $categoryProducts[$cat_name] = $productObj->getProductsByCategory($cat_name, 5) ?? [];
}

// Banner images - Fixed paths with BASE_URL
$banners = [
    BASE_URL . "/server/admin/uploads/banner1.jpg",
    BASE_URL . "/server/admin/uploads/banner2.jpg",
    BASE_URL . "/server/admin/uploads/banner3.jpg"
];
?>

<style>
/* ==== CSS GỐC ==== */
/* Removed body styling as it's handled in index.php */
.font-playfair { font-family: 'Playfair Display', serif; }
.page-content { padding:60px 20px; }
.page-content section { margin-bottom:100px; }

/* Section Title - Left Aligned */
.section-title { font-family: 'Playfair Display', serif; font-size:2.5rem; font-weight:700; margin-bottom:40px; text-align:left; color:#111; position:relative; }
.section-title::after { content: ''; display:block; width:60px; height:3px; background:#ca8a04; margin:15px 0 0 0; }

/* Product Card */
.product-card { background:#fff; border-radius:16px; overflow:hidden; transition: all 0.3s ease; position:relative; cursor:pointer; display:flex; flex-direction:column; text-decoration:none; color:inherit; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6; }
.product-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); border-color: #e5e7eb; }
.product-card img { width:100%; height:300px; object-fit:cover; transition: all 0.5s ease; } /* Giảm chiều cao ảnh để vừa 5 cột */

/* Badge Styles */
.badge-container { position: absolute; top: 12px; left: 12px; z-index: 10; display: flex; flex-direction: column; gap: 6px; }
.badge { font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.badge-sale { background: #ef4444; }
.badge-new { background: #3b82f6; }
.badge-hot { background: #eab308; display: flex; align-items: center; gap: 2px; }

/* Product Info */
.product-info { padding: 16px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; } /* Giảm padding */
.product-card h3 { font-size: 1rem; font-weight: 600; color: #1f2937; margin-bottom: 8px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; } 
.product-card .price-box { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: auto; }
.product-card .price { font-weight: 700; font-size: 1.125rem; color: #111; } 
.product-card .old-price { color: #9ca3af; font-size: 0.85rem; text-decoration: line-through; }

/* Hover Button */
.card-action { position: absolute; bottom: 0; left: 0; right: 0; padding: 15px; transform: translateY(100%); transition: transform 0.3s ease; background: linear-gradient(to top, rgba(255,255,255,0.95), rgba(255,255,255,0)); }
.product-card:hover .card-action { transform: translateY(0); }
.btn-view-detail { display: block; width: 100%; padding: 10px; background: #111; color: #fff; text-align: center; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; border-radius: 8px; transition: background 0.2s; letter-spacing: 0.5px; }
.btn-view-detail:hover { background: #ca8a04; }

.grid-custom { gap:20px; display:grid; grid-template-columns: repeat(2,1fr); }
@media(min-width:768px) { .grid-custom { grid-template-columns: repeat(3,1fr); } }
@media(min-width:1024px) { .grid-custom { grid-template-columns: repeat(4,1fr); } }
@media(min-width:1280px) { .grid-custom { grid-template-columns: repeat(5,1fr); } }

.view-all-btn { display:inline-block; margin-top:40px; padding:14px 40px; border:2px solid #111; border-radius:30px; font-weight:700; color:#111; text-transform:uppercase; transition:all 0.3s ease; font-size: 0.9rem; letter-spacing: 1px; }
.view-all-btn:hover { background:#111; color:#fff; }

/* Swiper Hero */
.swiper { width:100%; height:85vh; max-height: 800px; position:relative; z-index:1; }
.swiper-slide { position: relative; }
.swiper-slide img { width:100%; height:100%; object-fit:cover; }
.swiper-slide::after { content:''; position:absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.25); }
.hero-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10; text-align: center; color: white; width: 90%; max-width: 800px; }
.hero-title { font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 700; margin-bottom: 1rem; opacity: 0; transform: translateY(30px); transition: all 0.8s ease 0.3s; line-height: 1.2; text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
.hero-subtitle { font-size: 1.1rem; margin-bottom: 2rem; opacity: 0; transform: translateY(30px); transition: all 0.8s ease 0.5s; font-weight: 300; letter-spacing: 1px; text-shadow: 0 2px 5px rgba(0,0,0,0.3); }
.hero-btn { display: inline-block; padding: 14px 40px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; border-radius: 30px; opacity: 0; transform: translateY(30px); transition: all 0.8s ease 0.7s; }

/* Animation trigger class */
.swiper-slide-active .hero-title,
.swiper-slide-active .hero-subtitle,
.swiper-slide-active .hero-btn,
.swiper-slide-active .animate-fadeInUp { opacity: 1; transform: translateY(0); }

@media(max-width: 768px) {
    .swiper { height: 60vh; }
    .hero-title { font-size: 2rem; }
}
</style>

<!-- HERO BANNER -->
<section class="swiper mySwiper relative group">
    <div class="swiper-wrapper">
        <?php foreach($banners as $index => $img): ?>
            <div class="swiper-slide relative">
                <!-- Overlay tối hơn (bg-black/50) để làm nổi bật chữ -->
                <div class="absolute inset-0 bg-black/50 z-10"></div>
                <img src="<?= $img ?>" alt="Banner" class="w-full h-full object-cover">
                <div class="hero-content absolute inset-0 flex flex-col items-center justify-center text-center z-20 text-white px-4">
                    <span class="text-yellow-400 font-bold tracking-[4px] uppercase text-sm mb-4 animate-fadeInUp" style="animation-delay: 0.2s;">Spring Collection 2025</span>
                    <h2 class="hero-title text-4xl md:text-6xl lg:text-7xl font-playfair font-bold mb-6 leading-tight animate-fadeInUp" style="animation-delay: 0.4s;">
                        Vẻ Đẹp <br/> <span class="text-yellow-400">Thuần Khiết</span>
                    </h2>
                    <p class="hero-subtitle text-gray-100 text-lg md:text-xl mb-8 max-w-2xl font-light animate-fadeInUp" style="animation-delay: 0.6s;">
                        Khám phá những thiết kế túi xách mới nhất, kết hợp hoàn hảo giữa phong cách cổ điển và hơi thở hiện đại.
                    </p>
                    <div class="flex gap-4 animate-fadeInUp" style="animation-delay: 0.8s;">
                        <!-- Nút chính: Màu vàng, nổi bật -->
                        <a href="./page/san-pham.php" class="hero-btn bg-yellow-500 text-white hover:bg-yellow-600 shadow-lg hover:shadow-yellow-500/50 border-2 border-yellow-500">
                            Mua Ngay
                        </a>
                        <!-- Nút phụ: Trong suốt, viền trắng -->
                        <a href="#new-arrivals" class="hero-btn bg-transparent text-white border-2 border-white hover:bg-white hover:text-black">
                            Xem Bộ Sưu Tập
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Navigation Buttons -->
    <div class="swiper-button-next !text-white/50 hover:!text-white transition-colors after:!text-2xl"></div>
    <div class="swiper-button-prev !text-white/50 hover:!text-white transition-colors after:!text-2xl"></div>
    <div class="swiper-pagination !bottom-10"></div>
</section>

<!-- Hero Banner End -->

<!-- Main Content -->
<div class="page-content container mx-auto px-4 md:px-0">

    <!-- 1. NEW ARRIVALS -->
    <?php if(!empty($newArrivals)): ?>
    <section id="new-arrivals" class="scroll-mt-24">
        <div class="flex items-end justify-between mb-10 border-b border-gray-200 pb-4">
            <div>
                <h4 class="text-yellow-600 font-bold uppercase tracking-widest text-sm mb-2">Mới Nhất</h4>
                <h2 class="text-3xl md:text-4xl font-playfair font-bold text-gray-900">Hàng Mới Về</h2>
            </div>
            <a href="./page/san-pham.php?sort=newest" class="hidden md:flex items-center gap-2 text-gray-500 hover:text-black transition-colors font-medium">
                Xem tất cả <span class="material-symbols-rounded">arrow_right_alt</span>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
            <?php foreach($newArrivals as $p): ?>
                <?php 
                    $images = !empty($p['images']) ? json_decode($p['images'], true) : [];
                    $mainImage = $p['image'];
                    $hoverImage = $images[1] ?? $mainImage;
                    $discount = intval($p['discount']);
                    $finalPrice = $p['price'] * (1 - $discount/100);
                ?>
                <a href="<?= BASE_URL ?>/product_detail.php?id=<?= $p['id'] ?>" class="product-card group">
                    <!-- Badges -->
                    <div class="badge-container">
                        <?php if($discount > 0): ?>
                            <span class="badge badge-sale">-<?= $discount ?>%</span>
                        <?php endif; ?>
                        <span class="badge badge-new">NEW</span>
                    </div>

                    <?php 
                        $isLiked = in_array($p['id'], $wishlistIds);
                        $btnClass = $isLiked 
                            ? "text-red-500 opacity-100 translate-x-0" 
                            : "text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0";
                    ?>
                    <!-- Wishlist Button -->
                    <button onclick="event.preventDefault(); addToWishlist(<?= $p['id'] ?>, this)" class="absolute top-3 right-3 z-20 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:scale-110 transition-all duration-300 <?= $btnClass ?>">
                        <span class="material-symbols-rounded text-xl">favorite</span>
                    </button>

                    <!-- Image -->
                    <div class="relative overflow-hidden bg-gray-100 h-[360px]">
                        <img src="<?= BASE_URL ?>/server/admin/<?= htmlspecialchars($mainImage) ?>" 
                             alt="<?= htmlspecialchars($p['name_product']) ?>" 
                             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 opacity-100 group-hover:opacity-0"
                             onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image';">
                        
                        <img src="<?= BASE_URL ?>/server/admin/<?= htmlspecialchars($hoverImage) ?>" 
                             alt="<?= htmlspecialchars($p['name_product']) ?>" 
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 transform scale-105 opacity-0 group-hover:opacity-100 group-hover:scale-110"
                             onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image';">
                        
                        <!-- Action on Hover -->
                        <div class="card-action">
                            <span class="btn-view-detail">Xem chi tiết</span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="product-info">
                        <div>
                            <div class="text-xs text-gray-500 mb-2 uppercase tracking-wide font-medium"><?= htmlspecialchars($p['category']) ?></div>
                            <h3 class="group-hover:text-yellow-600 transition-colors"><?= htmlspecialchars($p['name_product']) ?></h3>
                        </div>
                        <div class="price-box">
                            <span class="price"><?= number_format($finalPrice, 0, ',', '.') ?>₫</span>
                            <?php if($discount > 0): ?>
                                <span class="old-price"><?= number_format($p['price'], 0, ',', '.') ?>₫</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-12 text-center md:hidden">
            <a href="./page/san-pham.php?sort=newest" class="inline-flex items-center gap-2 px-6 py-3 border border-gray-900 rounded-full font-medium hover:bg-black hover:text-white transition-all">
                Xem tất cả <span class="material-symbols-rounded">arrow_right_alt</span>
            </a>
        </div>
    </section>
    <?php endif; ?>

    <!-- 2. BEST SELLERS -->
    <?php if(!empty($bestSellers)): ?>
    <section class="mt-20">
        <div class="text-center mb-12">
            <h4 class="text-yellow-600 font-bold uppercase tracking-widest text-sm mb-2">Sản Phẩm Hot</h4>
            <h2 class="text-3xl md:text-4xl font-playfair font-bold text-gray-900 relative inline-block">
                Bán Chạy Nhất
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-20 h-1 bg-yellow-400 mt-2"></span>
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach($bestSellers as $p): ?>
                <?php 
                    $images = !empty($p['images']) ? json_decode($p['images'], true) : [];
                    $mainImage = $p['image'];
                    $hoverImage = $images[1] ?? $mainImage;
                    $discount = intval($p['discount']);
                    $finalPrice = $p['price'] * (1 - $discount/100);
                ?>
                <a href="<?= BASE_URL ?>/product_detail.php?id=<?= $p['id'] ?>" class="product-card group">
                     <!-- Badges -->
                     <div class="badge-container">
                        <?php if($discount > 0): ?>
                            <span class="badge badge-sale">-<?= $discount ?>%</span>
                        <?php endif; ?>
                        <span class="badge badge-hot">
                            <span class="material-symbols-rounded text-[14px]">local_fire_department</span> HOT
                        </span>
                    </div>

                    <?php 
                        $isLiked = in_array($p['id'], $wishlistIds);
                        $btnClass = $isLiked 
                            ? "text-red-500 opacity-100 translate-x-0" 
                            : "text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0";
                    ?>
                    <!-- Wishlist Button -->
                    <button onclick="event.preventDefault(); addToWishlist(<?= $p['id'] ?>, this)" class="absolute top-3 right-3 z-20 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:scale-110 transition-all duration-300 <?= $btnClass ?>">
                        <span class="material-symbols-rounded text-xl">favorite</span>
                    </button>

                    <!-- Image -->
                    <div class="relative overflow-hidden bg-gray-100 h-[360px]">
                        <img src="<?= BASE_URL ?>/server/admin/<?= htmlspecialchars($mainImage) ?>" 
                             alt="<?= htmlspecialchars($p['name_product']) ?>" 
                             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 opacity-100 group-hover:opacity-0"
                             onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image';">
                             
                        <img src="<?= BASE_URL ?>/server/admin/<?= htmlspecialchars($hoverImage) ?>" 
                             alt="<?= htmlspecialchars($p['name_product']) ?>" 
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 transform scale-105 opacity-0 group-hover:opacity-100 group-hover:scale-110"
                             onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image';">
                        
                         <!-- Action on Hover -->
                         <div class="card-action">
                            <span class="btn-view-detail">Xem chi tiết</span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="product-info">
                        <div>
                            <div class="text-xs text-gray-500 mb-2 uppercase tracking-wide font-medium"><?= htmlspecialchars($p['category']) ?></div>
                            <h3 class="group-hover:text-yellow-600 transition-colors"><?= htmlspecialchars($p['name_product']) ?></h3>
                        </div>
                        <div class="price-box">
                            <span class="price"><?= number_format($finalPrice, 0, ',', '.') ?>₫</span>
                            <?php if($discount > 0): ?>
                                <span class="old-price"><?= number_format($p['price'], 0, ',', '.') ?>₫</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- 3. CATEGORIES -->
    <?php foreach ($categories as $cat_assoc): 
        $cat_name = $cat_assoc['name'];
        $cat_slug = $cat_assoc['slug'];
    ?>
        <section>
            <h2 class="section-title"><?= htmlspecialchars($cat_name) ?></h2>
            <?php if(!empty($categoryProducts[$cat_name])): ?>
                <div class="grid-custom">
                    <?php foreach($categoryProducts[$cat_name] as $p): ?>
                        <?php 
                            $images = !empty($p['images']) ? json_decode($p['images'], true) : [];
                            $mainImage = $p['image'];
                            $hoverImage = $images[1] ?? $mainImage;
                            $discount = intval($p['discount']);
                            $finalPrice = $p['price'] * (1 - $discount/100);
                        ?>
                        <!-- Unified Product Card Structure -->
                        <a href="<?= BASE_URL ?>/product_detail.php?id=<?= $p['id'] ?>" class="product-card group">
                            <!-- Badges -->
                            <div class="badge-container">
                                <?php if($discount > 0): ?>
                                    <span class="badge badge-sale">-<?= $discount ?>%</span>
                                <?php endif; ?>
                            </div>

                            <?php 
                                $isLiked = in_array($p['id'], $wishlistIds);
                                $btnClass = $isLiked 
                                    ? "text-red-500 opacity-100 translate-x-0" 
                                    : "text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0";
                            ?>
                            <!-- Wishlist Button -->
                            <button onclick="event.preventDefault(); addToWishlist(<?= $p['id'] ?>, this)" class="absolute top-3 right-3 z-20 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:scale-110 transition-all duration-300 <?= $btnClass ?>">
                                <span class="material-symbols-rounded text-xl">favorite</span>
                            </button>

                            <div class="relative overflow-hidden bg-gray-100 h-[360px]">
                                <img src="<?= BASE_URL ?>/server/admin/<?= htmlspecialchars($mainImage) ?>" 
                                     alt="<?= htmlspecialchars($p['name_product']) ?>" 
                                     class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 opacity-100 group-hover:opacity-0"
                                     onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image';">
                                
                                <img src="<?= BASE_URL ?>/server/admin/<?= htmlspecialchars($hoverImage) ?>" 
                                     alt="<?= htmlspecialchars($p['name_product']) ?>" 
                                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 transform scale-105 opacity-0 group-hover:opacity-100 group-hover:scale-110"
                                     onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image';">
                                
                                <!-- Action on Hover -->
                                <div class="card-action">
                                    <span class="btn-view-detail">Xem chi tiết</span>
                                </div>
                            </div>

                            <div class="product-info">
                                <div>
                                    <h3 class="group-hover:text-yellow-600 transition-colors"><?= htmlspecialchars($p['name_product']) ?></h3>
                                </div>
                                <div class="price-box">
                                    <span class="price"><?= number_format($finalPrice, 0, ',', '.') ?>₫</span>
                                    <?php if($discount > 0): ?>
                                        <span class="old-price"><?= number_format($p['price'], 0, ',', '.') ?>₫</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="text-center">
                    <a href="./page/san-pham.php?category=<?= urlencode($cat_slug) ?>" class="view-all-btn">Xem BST <?= htmlspecialchars($cat_name) ?></a>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500 mt-6 italic">Chưa có sản phẩm cho mục <?= htmlspecialchars($cat_name) ?>.</p>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</div>

    <!-- ABOUT SECTION -->
    <section class="py-20 bg-gray-50 mt-20">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center gap-12 max-w-6xl mx-auto">
                <!-- Image Side -->
                <div class="w-full md:w-1/2 relative group">
                    <div class="absolute -inset-4 border-2 border-yellow-500 rounded-tr-[50px] rounded-bl-[50px] opacity-20 group-hover:scale-105 transition-transform duration-500"></div>
                    <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?q=80&w=2069&auto=format&fit=crop" 
                         alt="About CHAUTFIFTH" 
                         class="w-full h-[400px] object-cover rounded-tr-[50px] rounded-bl-[50px] shadow-2xl relative z-10">
                    <div class="absolute -bottom-6 -right-6 bg-black text-white p-6 rounded-tr-3xl rounded-bl-3xl z-20 shadow-xl hidden md:block">
                        <p class="font-playfair text-3xl font-bold text-yellow-500">2025</p>
                        <p class="text-xs tracking-widest uppercase">New Collection</p>
                    </div>
                </div>

                <!-- Text Side -->
                <div class="w-full md:w-1/2 text-center md:text-left">
                    <h4 class="text-yellow-600 font-bold uppercase tracking-[4px] text-sm mb-4">Về Chúng Tôi</h4>
                    <h2 class="text-4xl md:text-5xl font-playfair font-bold text-gray-900 mb-6 leading-tight">
                        Tinh Hoa <br/> <span class="italic text-yellow-600">Thời Trang Hiện Đại</span>
                    </h2>
                    <p class="text-gray-500 mb-6 leading-relaxed text-lg font-light">
                        CHAUTFIFTH không chỉ là một thương hiệu túi xách, đó là tuyên ngôn của phong cách sống. Chúng tôi kết hợp sự tinh tế trong thiết kế cổ điển với hơi thở phóng khoáng của thời đại mới.
                    </p>
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="flex flex-col gap-2">
                            <span class="material-symbols-rounded text-4xl text-gray-800">diamond</span>
                            <h5 class="font-bold text-gray-900">Chất Lượng Đỉnh Cao</h5>
                            <p class="text-sm text-gray-500">Da thật 100%, đường may thủ công tỉ mỉ.</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="material-symbols-rounded text-4xl text-gray-800">palette</span>
                            <h5 class="font-bold text-gray-900">Thiết Kế Độc Bản</h5>
                            <p class="text-sm text-gray-500">Mỗi bộ sưu tập là một câu chuyện riêng biệt.</p>
                        </div>
                    </div>
                    <a href="./page/gioi-thieu.php" class="inline-block border-b-2 border-black pb-1 text-black font-bold uppercase tracking-wide hover:text-yellow-600 hover:border-yellow-600 transition-colors">
                        Khám Phá Câu Chuyện
                    </a>
                </div>
            </div>
        </div>
    </section>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-24 right-0 z-[9999] p-4 flex flex-col gap-3 pointer-events-none"></div>

<script>
/**
 * Hiển thị thông báo Toast (Giao diện đồng bộ Brand)
 * @param {string} message - Nội dung thông báo
 * @param {string} type - 'success' | 'error' | 'info'
 */
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    
    // Icon & Color configs
    const configs = {
        success: {
            icon: 'check_circle',
            classes: 'bg-black text-white border-l-4 border-yellow-500'
        },
        error: {
            icon: 'error',
            classes: 'bg-white text-gray-800 border-l-4 border-red-500 shadow-xl border border-gray-100'
        },
        info: {
            icon: 'info',
            classes: 'bg-gray-800 text-white border-l-4 border-blue-400'
        }
    };
    
    const config = configs[type] || configs.success;

    // Create Toast Element
    const toast = document.createElement('div');
    toast.className = `${config.classes} flex items-center gap-3 px-6 py-4 rounded shadow-2xl transform translate-x-full transition-all duration-300 ease-out pointer-events-auto min-w-[300px]`;
    
    toast.innerHTML = `
        <span class="material-symbols-rounded text-2xl ${type === 'success' ? 'text-yellow-500' : (type === 'error' ? 'text-red-500' : 'text-blue-400')}">
            ${config.icon}
        </span>
        <div class="flex-1 font-medium text-sm font-sans tracking-wide">
            ${message}
        </div>
        <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100 transition">
            <span class="material-symbols-rounded">close</span>
        </button>
    `;

    // Append & Animate
    container.appendChild(toast);
    
    // Trigger animation (next frame)
    requestAnimationFrame(() => {
        toast.classList.remove('translate-x-full');
    });

    // Auto remove
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function addToWishlist(productId, btn) {
    fetch('<?= BASE_URL ?>/wishlist_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=add&product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            
            // Toggle Visual State
            if (btn) {
                // Kiểm tra xem đang ở trạng thái nào dựa trên class màu
                const isLiked = btn.classList.contains('text-red-500');
                
                if (isLiked) {
                    // Đang thích -> Bỏ thích: Về màu xám, ẩn đi (chờ hover mới hiện)
                    btn.classList.remove('text-red-500', 'opacity-100', 'translate-x-0');
                    btn.classList.add('text-gray-400', 'opacity-0', 'translate-x-2');
                    
                    // Phục hồi các class hover (nếu cần thiết để nó hoạt động lại như nút thường)
                    // Lưu ý: Các class group-hover:... đã có sẵn trong HTML tĩnh,
                    // chỉ cần add lại các class trạng thái ban đầu.
                } else {
                    // Chưa thích -> Thích: Màu đỏ, Hiện rõ
                    btn.classList.remove('text-gray-400', 'opacity-0', 'translate-x-2');
                    btn.classList.add('text-red-500', 'opacity-100', 'translate-x-0');
                }
            }
            
        } else {
            if(data.message === 'Vui lòng đăng nhập!') {
                 showToast('Vui lòng đăng nhập để lưu yêu thích!', 'info');
            } else {
                // Trường hợp đã có -> Vẫn cho hiện màu đỏ (đồng bộ trạng thái)
                if(data.message.includes('đã có')) {
                     showToast(data.message, 'info');
                     if (btn) {
                        btn.classList.remove('text-gray-400', 'opacity-0', 'translate-x-2');
                        btn.classList.add('text-red-500', 'opacity-100', 'translate-x-0');
                     }
                } else {
                    showToast(data.message, 'error');
                }
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi kết nối, vui lòng thử lại.', 'error');
    });
}
</script>