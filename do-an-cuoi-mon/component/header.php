<?php
if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once __DIR__ . "/../server/models/user.php";
require_once __DIR__ . "/../server/models/cart.php";

$userObj = new User();
$account = $userObj->getAccount();
$loggedIn = $account['success'];
$user = $loggedIn ? $account['user'] : null;

// Lấy số lượng giỏ hàng
$cartCount = 0;
if ($loggedIn) {
    $cartObj = new Cart();
    $cartData = $cartObj->getTotalItems();
    $cartCount = $cartData['success'] ? $cartData['total_items'] : 0;
}

// Base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
if (!defined('BASE_URL')) {
    define('BASE_URL', '/CDPHP/do-an-cuoi-mon');
}

// Xác định trang hiện tại để highlight menu
$currentPage = basename($_SERVER['PHP_SELF']);

function isActive($pageName, $currentPage) {
    // Logic highlight linh hoạt hơn
    if ($currentPage === $pageName) {
        return 'text-yellow-600 font-bold relative after:content-[""] after:absolute after:-bottom-1 after:left-0 after:w-full after:h-0.5 after:bg-yellow-600';
    }
    // Highlight cha khi ở trang con
    if ($pageName === 'san-pham.php' && ($currentPage === 'product_detail.php' || $currentPage === 'tim-kiem.php')) {
        return 'text-yellow-600 font-bold relative after:content-[""] after:absolute after:-bottom-1 after:left-0 after:w-full after:h-0.5 after:bg-yellow-600';
    }
    return 'hover:text-yellow-600 text-gray-600 transition-colors relative after:content-[""] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-0.5 after:bg-yellow-600 hover:after:w-full after:transition-all after:duration-300';
}
?>

<!-- Import Font & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />

<header class="bg-white border-b border-gray-100 sticky top-0 left-0 right-0 z-[1000] shadow-sm transition-all duration-300" id="mainHeader">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">

        <!-- LOGO -->
        <a href="<?= BASE_URL ?>/index.php" class="flex items-center gap-2 group">
            <span class="text-3xl font-bold tracking-tight text-gray-900 group-hover:text-yellow-600 transition-colors duration-300 font-playfair">
                CHAUTFIFTH
            </span>
        </a>

        <!-- MENU DESKTOP -->
        <nav class="hidden md:flex items-center gap-8 font-medium text-[15px] tracking-wide">
            <a href="<?= BASE_URL ?>/index.php" class="<?= isActive('index.php', $currentPage) ?> py-2">Trang chủ</a>
            <a href="<?= BASE_URL ?>/page/gioi-thieu.php" class="<?= isActive('gioi-thieu.php', $currentPage) ?> py-2">Giới thiệu</a>
            <a href="<?= BASE_URL ?>/page/san-pham.php" class="<?= isActive('san-pham.php', $currentPage) ?> py-2">Sản phẩm</a>
            <a href="<?= BASE_URL ?>/san-ma-giam-gia.php" class="<?= isActive('san-ma-giam-gia.php', $currentPage) ?> py-2">Voucher</a>
            <a href="<?= BASE_URL ?>/contact.php" class="<?= isActive('contact.php', $currentPage) ?> py-2">Liên hệ</a>

            <div class="h-6 w-px bg-gray-300 mx-2"></div>

            <?php if ($loggedIn): ?>
                <!-- Wishlist icon (Desktop) -->
                <a href="<?= BASE_URL ?>/wishlist.php" class="relative group p-2 hover:bg-gray-50 rounded-full transition-colors" title="Yêu thích">
                    <span class="material-symbols-rounded text-[26px] text-gray-700 group-hover:text-red-500 transition-colors">favorite</span>
                </a>

                <!-- Cart icon -->
                <a href="<?= BASE_URL ?>/cart.php" class="relative group p-2 hover:bg-gray-50 rounded-full transition-colors">
                    <span class="material-symbols-rounded text-[26px] text-gray-700 group-hover:text-yellow-600 transition-colors">shopping_bag</span>
                    <?php if ($cartCount > 0): ?>
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-bold h-5 w-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm animate-bounce">
                        <?= $cartCount > 99 ? '99+' : $cartCount ?>
                    </span>
                    <?php endif; ?>
                </a>

                <!-- User Dropdown (Cải tiến UI) -->
                <div class="relative group">
                    <button class="flex items-center gap-2 pl-4 pr-3 py-1.5 rounded-full border border-gray-200 hover:border-yellow-500 hover:shadow-md transition-all duration-300 bg-white">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-yellow-400 to-yellow-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 max-w-[100px] truncate"><?= htmlspecialchars($user['username']) ?></span>
                        <span class="material-symbols-rounded text-[20px] text-gray-400 group-hover:text-yellow-600 transition-transform duration-300 group-hover:rotate-180">expand_more</span>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 top-full mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-xl opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 invisible group-hover:visible transition-all duration-200 ease-out z-50 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Tài khoản</p>
                            <p class="text-sm font-bold text-gray-800 truncate"><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                        <div class="py-1">
                            <a href="<?= BASE_URL ?>/profile.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition-colors">
                                <span class="material-symbols-rounded text-[20px]">person</span> Hồ sơ cá nhân
                            </a>
                            <a href="<?= BASE_URL ?>/orders.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition-colors">
                                <span class="material-symbols-rounded text-[20px]">receipt_long</span> Đơn hàng của tôi
                            </a>
                            <!-- Link Wishlist Mobile -->
                            <a href="<?= BASE_URL ?>/wishlist.php" class="md:hidden flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition-colors">
                                <span class="material-symbols-rounded text-[20px]">favorite</span> Yêu thích
                            </a>
                        </div>
                        <div class="border-t border-gray-100 py-1">
                            <a href="<?= BASE_URL ?>/logout.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium">
                                <span class="material-symbols-rounded text-[20px]">logout</span> Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="flex items-center gap-3">
                    <a href="<?= BASE_URL ?>/auth.php" class="text-sm font-semibold text-gray-700 hover:text-yellow-600 transition-colors">Đăng nhập</a>
                    <a href="<?= BASE_URL ?>/auth.php" class="px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-full hover:bg-yellow-600 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                        Đăng ký ngay
                    </a>
                </div>
            <?php endif; ?>
        </nav>

        <!-- MOBILE MENU BTN -->
        <button id="menuBtn" class="md:hidden p-2 text-gray-800 hover:bg-gray-100 rounded-full transition-colors">
            <span class="material-symbols-rounded text-[28px]">menu</span>
        </button>
    </div>

    <!-- MOBILE MENU PANEL -->
    <div id="mobileMenu" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-[1001] transition-opacity duration-300 opacity-0">
        <aside id="menuPanel" class="fixed right-0 top-0 w-[85%] max-w-[320px] h-full bg-white shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 ease-in-out">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <span class="text-xl font-bold font-playfair">Menu</span>
                <button id="closeMenu" class="p-2 text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-full transition-all">
                    <span class="material-symbols-rounded text-2xl">close</span>
                </button>
            </div>

            <div class="p-5 flex-1 overflow-y-auto space-y-1">
                <a href="<?= BASE_URL ?>/index.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-rounded">home</span> Trang chủ
                </a>
                <a href="<?= BASE_URL ?>/page/gioi-thieu.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-rounded">info</span> Giới thiệu
                </a>
                <a href="<?= BASE_URL ?>/page/san-pham.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-rounded">inventory_2</span> Sản phẩm
                </a>
                <a href="<?= BASE_URL ?>/san-ma-giam-gia.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-rounded">local_offer</span> Săn Sale
                </a>
                <a href="<?= BASE_URL ?>/contact.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-rounded">contact_support</span> Liên hệ
                </a>

                <div class="my-4 border-t border-gray-100"></div>

                <?php if ($loggedIn): ?>
                    <div class="bg-yellow-50 rounded-2xl p-4 mb-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-yellow-500 text-white flex items-center justify-center font-bold text-lg">
                                <?= strtoupper(substr($user['username'], 0, 1)) ?>
                            </div>
                            <div class="overflow-hidden">
                                <p class="font-bold text-gray-900 truncate"><?= htmlspecialchars($user['username']) ?></p>
                                <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>/profile.php" class="block text-sm text-yellow-700 font-semibold hover:underline">Quản lý tài khoản</a>
                    </div>
                    
                    <a href="<?= BASE_URL ?>/cart.php" class="flex items-center justify-between px-4 py-3 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                        <span class="flex items-center gap-3"><span class="material-symbols-rounded">shopping_cart</span> Giỏ hàng</span>
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"><?= $cartCount ?></span>
                    </a>
                    <a href="<?= BASE_URL ?>/orders.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                        <span class="material-symbols-rounded">receipt_long</span> Đơn hàng
                    </a>
                    <a href="<?= BASE_URL ?>/logout.php" class="flex items-center gap-3 px-4 py-3 text-red-600 font-medium rounded-xl hover:bg-red-50 transition-colors mt-2">
                        <span class="material-symbols-rounded">logout</span> Đăng xuất
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/auth.php" class="block w-full text-center py-3 rounded-xl border border-gray-200 font-semibold text-gray-700 hover:border-black transition-colors mb-3">Đăng nhập</a>
                    <a href="<?= BASE_URL ?>/auth.php" class="block w-full text-center py-3 rounded-xl bg-black text-white font-semibold hover:bg-gray-800 transition-colors shadow-lg">Đăng ký ngay</a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</header>

<script>
    const menuBtn = document.getElementById('menuBtn');
    const closeMenu = document.getElementById('closeMenu');
    const mobileMenu = document.getElementById('mobileMenu');
    const menuPanel = document.getElementById('menuPanel');

    function openMenu() {
        mobileMenu.classList.remove('hidden');
        // Trigger reflow to enable transition
        void mobileMenu.offsetWidth; 
        mobileMenu.classList.remove('opacity-0');
        menuPanel.classList.remove('translate-x-full');
        document.body.style.overflow = 'hidden'; // Prevent body scroll
    }

    function closeMenuFunc() {
        mobileMenu.classList.add('opacity-0');
        menuPanel.classList.add('translate-x-full');
        setTimeout(() => {
            mobileMenu.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    menuBtn.addEventListener('click', openMenu);
    closeMenu.addEventListener('click', closeMenuFunc);
    mobileMenu.addEventListener('click', (e) => {
        if (e.target === mobileMenu) closeMenuFunc();
    });
</script>

<style>
    /* Font custom */
    .font-playfair { font-family: 'Playfair Display', serif; }
    
    /* Smooth Dropdown */
    .group:hover .group-hover\:visible { visibility: visible; }
    .group:hover .group-hover\:opacity-100 { opacity: 1; }
    .group:hover .group-hover\:translate-y-0 { transform: translateY(0); }
</style>