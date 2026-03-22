<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$baseUrl = '/CDPHP/do-an-cuoi-mon';
$docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');

require_once $docRoot . $baseUrl . '/server/models/products.php';
require_once $docRoot . $baseUrl . '/server/models/user.php';
require_once $docRoot . $baseUrl . '/server/models/cart.php';

$productObj = new Products();
$userObj = new User();
$account = $userObj->getAccount();
$loggedIn = $account['success'] ?? false;
$user = $loggedIn ? $account['user'] : null;

// Lấy danh sách ID sản phẩm trong wishlist của user
$wishlistIds = [];
if ($loggedIn) {
    require_once __DIR__ . "/../server/database/database.php"; 
    $db = new Database();
    $conn = $db->connect();
    $stmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $wishlistIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// --- Filter Params ---
$search   = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$sort     = trim($_GET['sort'] ?? '');
$minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
$page     = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit    = 9; // Sản phẩm mỗi trang

// --- Get Data ---
$data = $productObj->filterProducts($search, $category, $sort, $minPrice, $maxPrice, $page, $limit);
$products = $data['products'];
$total = $data['total'];
$totalPages = $data['total_pages'];
$categories = $productObj->getAllCategories();

// --- Cart Count ---
$cartCount = 0;
if ($loggedIn) {
    $cartObj = new Cart();
    $cartData = $cartObj->getTotalItems();
    $cartCount = $cartData['success'] ? (int)$cartData['total_items'] : 0;
}

// --- Helper: Build URL for Pagination/Filter ---
function buildUrl($params = []) {
    $current = $_GET;
    $merged = array_merge($current, $params);
    return '?' . http_build_query($merged);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Cửa Hàng • CHAUTFIFTH</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
<style>
    body { font-family: 'Inter', sans-serif; background: #f9fafb; color: #111; }
    .font-playfair { font-family: 'Playfair Display', serif; }
    /* Checkbox Custom */
    .lux-checkbox { appearance: none; width: 18px; height: 18px; border: 1px solid #d1d5db; border-radius: 4px; position: relative; cursor: pointer; }
    .lux-checkbox:checked { background: #ca8a04; border-color: #ca8a04; }
    .lux-checkbox:checked::after { content: '✓'; position: absolute; color: white; font-size: 12px; left: 3px; top: -2px; }
</style>
</head>
<body>

<?php include $docRoot . $baseUrl . '/component/header.php'; ?>

<!-- Breadcrumb -->
<div class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-4 py-4 text-sm text-gray-500">
        <a href="index.php" class="hover:text-yellow-600">Trang chủ</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-medium">Sản phẩm</span>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- SIDEBAR FILTER (Mobile: Drawer / Desktop: Sticky) -->
        <aside class="w-full lg:w-1/4 flex-shrink-0">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg font-playfair">Bộ lọc tìm kiếm</h3>
                    <a href="san-pham.php" class="text-xs text-red-500 hover:underline">Xóa tất cả</a>
                </div>

                <form method="GET" id="filterForm">
                    <!-- Search -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2 text-gray-700">Từ khóa</label>
                        <div class="relative">
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm túi xách..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 outline-none text-sm transition">
                            <span class="material-symbols-rounded absolute left-3 top-2 text-gray-400 text-lg">search</span>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-3 text-gray-700">Danh mục</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="category" value="" class="lux-checkbox" onchange="this.form.submit()" <?= $category===''?'checked':'' ?> >
                                <span class="text-sm group-hover:text-yellow-600 transition <?= $category===''?'font-semibold text-yellow-600':'text-gray-600' ?>">Tất cả</span>
                            </label>
                            <?php foreach($categories as $c): 
                                $slug = $c['slug'] ?? $c;
                                $name = $c['name'] ?? $c;
                            ?>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="category" value="<?= htmlspecialchars($slug) ?>" class="lux-checkbox" onchange="this.form.submit()" <?= $category===$slug?'checked':'' ?> >
                                <span class="text-sm group-hover:text-yellow-600 transition <?= $category===$slug?'font-semibold text-yellow-600':'text-gray-600' ?>"><?= htmlspecialchars($name) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-3 text-gray-700">Khoảng giá</label>
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <input type="number" name="min_price" value="<?= $minPrice ?: '' ?>" placeholder="Min" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-yellow-500 outline-none">
                            <input type="number" name="max_price" value="<?= $maxPrice ?: '' ?>" placeholder="Max" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-yellow-500 outline-none">
                        </div>
                    </div>

                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                    
                    <button type="submit" class="w-full bg-black text-white font-medium py-2.5 rounded-lg hover:bg-yellow-600 transition shadow-lg">Áp dụng</button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1">
            <!-- Top Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-gray-600 text-sm mb-4 sm:mb-0">Hiển thị <span class="font-bold text-black"><?= count($products) ?></span> trên tổng số <span class="font-bold text-black"><?= $total ?></span> sản phẩm</p>
                
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-500 whitespace-nowrap">Sắp xếp:</label>
                    <select onchange="window.location.href=this.value" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:border-yellow-500 outline-none bg-white">
                        <option value="<?= buildUrl(['sort' => '']) ?>" <?= $sort===''?'selected':'' ?>>Mặc định</option>
                        <option value="<?= buildUrl(['sort' => 'price_asc']) ?>" <?= $sort==='price_asc'?'selected':'' ?>>Giá thấp đến cao</option>
                        <option value="<?= buildUrl(['sort' => 'price_desc']) ?>" <?= $sort==='price_desc'?'selected':'' ?>>Giá cao đến thấp</option>
                        <option value="<?= buildUrl(['sort' => 'name_asc']) ?>" <?= $sort==='name_asc'?'selected':'' ?>>Tên A-Z</option>
                    </select>
                </div>
            </div>

            <!-- Product Grid -->
            <?php if (!empty($products)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                    <?php foreach($products as $p): 
                        $images = !empty($p['images']) ? json_decode($p['images'], true) : [];
                        $mainImage = $p['image'] ?? ($images[0] ?? '');
                        $hoverImage = $images[1] ?? $mainImage;
                        $discount = (float)($p['discount'] ?? 0);
                        $price = (float)($p['price'] ?? 0);
                        $finalPrice = $price * (1 - $discount/100);
                    ?>
                    <div class="group bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 relative">
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 z-10 flex flex-col gap-2">
                            <?php if($discount > 0): ?>
                                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-md">-<?= intval($discount) ?>%</span>
                            <?php endif; ?>
                        </div>

                         <!-- Wishlist Button -->
                         <?php 
                            $isLiked = in_array($p['id'], $wishlistIds);
                            $btnClass = $isLiked 
                                ? "text-red-500 opacity-100 translate-x-0" 
                                : "text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0";
                        ?>
                         <button onclick="event.preventDefault(); addToWishlist(<?= $p['id'] ?>, this)" class="absolute top-3 right-3 z-10 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:scale-110 transition-all duration-300 <?= $btnClass ?>">
                            <span class="material-symbols-rounded text-lg">favorite</span>
                        </button>

                        <a href="<?= $baseUrl ?>/product_detail.php?id=<?= $p['id'] ?>" class="block relative h-72 overflow-hidden bg-gray-50">
                             <img src="<?= $baseUrl ?>/server/admin/<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($p['name_product']) ?>" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 opacity-100 group-hover:opacity-0">
                            <img src="<?= $baseUrl ?>/server/admin/<?= htmlspecialchars($hoverImage) ?>" alt="<?= htmlspecialchars($p['name_product']) ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 transform scale-105 opacity-0 group-hover:opacity-100 group-hover:scale-110">
                        </a>

                        <div class="p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1"><?= htmlspecialchars($p['category']) ?></p>
                            <h3 class="font-medium text-gray-900 mb-2 truncate group-hover:text-yellow-600 transition">
                                <a href="<?= $baseUrl ?>/product_detail.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name_product']) ?></a>
                            </h3>
                            <div class="flex items-baseline gap-2">
                                <span class="font-bold text-gray-900"><?= number_format($finalPrice, 0, ',', '.') ?>₫</span>
                                <?php if($discount > 0): ?>
                                    <span class="text-xs text-gray-400 line-through"><?= number_format($price, 0, ',', '.') ?>₫</span>
                                <?php endif; ?>
                            </div>

                             <!-- Add to Cart (Simple Form) -->
                             <form action="/CDPHP/do-an-cuoi-mon/cart_action.php" method="POST" class="mt-4">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($p['name_product']) ?>">
                                <input type="hidden" name="product_price" value="<?= $finalPrice ?>">
                                <input type="hidden" name="product_image" value="<?= htmlspecialchars($mainImage) ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-full py-2 border border-gray-200 rounded-lg text-sm font-semibold hover:bg-black hover:text-white hover:border-black transition-colors">
                                    Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="mt-10 flex justify-center gap-2">
                    <?php if ($page > 1): ?>
                        <a href="<?= buildUrl(['page' => $page - 1]) ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-100 transition text-gray-600">
                            <span class="material-symbols-rounded">chevron_left</span>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="<?= buildUrl(['page' => $i]) ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border transition font-medium <?= $i === $page ? 'bg-black text-white border-black' : 'border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= buildUrl(['page' => $page + 1]) ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-100 transition text-gray-600">
                            <span class="material-symbols-rounded">chevron_right</span>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <span class="material-symbols-rounded text-3xl">search_off</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Không tìm thấy sản phẩm</h3>
                    <p class="text-gray-500 mb-6">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm của bạn.</p>
                    <a href="san-pham.php" class="px-6 py-2 bg-yellow-600 text-white rounded-full font-medium hover:bg-yellow-700 transition">Xóa bộ lọc</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include $docRoot . $baseUrl . '/component/footer.php'; ?>

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
    fetch('<?= $baseUrl ?>/wishlist_action.php', {
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
</body>
</html>