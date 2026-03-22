<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$baseUrl = '/CDPHP/do-an-cuoi-mon';
$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');

require_once $docRoot . $baseUrl . '/server/models/products.php';
require_once $docRoot . $baseUrl . '/server/models/user.php';
require_once $docRoot . $baseUrl . '/server/models/reviews.php';

// Check ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$productObj = new Products();
$reviewObj = new Reviews();
$userObj = new User();

// Get Product Data
$product = $productObj->getById($id);
if (!$product) {
    echo "<h2>Sản phẩm không tồn tại!</h2>";
    exit;
}

// Get Account
$account = $userObj->getAccount();
$loggedIn = $account['success'];
$user = $loggedIn ? $account['user'] : null;

// Check Wishlist Status
$isLiked = false;
if ($loggedIn) {
    require_once __DIR__ . "/server/database/database.php"; 
    $db = new Database();
    $conn = $db->connect();
    $stmt = $conn->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user['id'], $id]);
    $isLiked = $stmt->fetchColumn() > 0;
}

// Get Reviews
$reviews = $reviewObj->getReviewsByProduct($id);
$avgRating = $reviewObj->getAverageRating($id);
$ratingVal = round($avgRating['avg_rating'] ?? 5, 1);
$totalReviews = $avgRating['total_reviews'] ?? 0;

// Process Images
$images = !empty($product['images']) ? json_decode($product['images'], true) : [];
if (empty($images)) $images = [$product['image']];
$mainImage = $images[0];

// Price
$discount = (float)$product['discount'];
$price = (float)$product['price'];
$finalPrice = $price * (1 - $discount/100);

// Process Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!$loggedIn) {
        $msg = "Vui lòng đăng nhập để đánh giá.";
    } else {
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        $res = $reviewObj->addReview($user['id'], $id, $rating, $comment);
        if ($res['success']) {
            header("Location: product_detail.php?id=$id&msg=review_success");
            exit;
        } else {
            $msg = $res['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($product['name_product']) ?> • CHAUTFIFTH</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
<style>
    body { font-family: 'Inter', sans-serif; background: #f9fafb; color: #111; }
    .font-playfair { font-family: 'Playfair Display', serif; }
    .active-thumb { border-color: #ca8a04; }
    .star-rating input { display: none; }
    .star-rating label { font-size: 24px; color: #d1d5db; cursor: pointer; transition: color 0.2s; }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label { color: #eab308; }
    .star-rating { direction: rtl; display: inline-flex; }
</style>
</head>
<body>

<?php include $docRoot . $baseUrl . '/component/header.php'; ?>

<!-- Breadcrumb -->
<div class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-4 py-4 text-sm text-gray-500">
        <a href="index.php" class="hover:text-yellow-600">Trang chủ</a>
        <span class="mx-2">/</span>
        <a href="page/san-pham.php" class="hover:text-yellow-600">Sản phẩm</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-medium truncate"><?= htmlspecialchars($product['name_product']) ?></span>
    </div>
</div>

<main class="container mx-auto px-4 py-10">
    <!-- PRODUCT INFO SECTION -->
    <div class="flex flex-col lg:flex-row gap-10 lg:gap-16 mb-20">
        
        <!-- Gallery -->
        <div class="lg:w-1/2">
            <div class="aspect-[4/5] bg-gray-100 rounded-2xl overflow-hidden mb-4 relative group">
                <img id="mainImg" src="<?= $baseUrl ?>/server/admin/<?= htmlspecialchars($mainImage) ?>" class="w-full h-full object-cover">
                <?php if($discount > 0):
                    echo '<span class="absolute top-4 left-4 bg-red-500 text-white text-sm font-bold px-3 py-1 rounded-full shadow">-' . intval($discount) . '%</span>';
                endif; ?>
            </div>
            <div class="flex gap-4 overflow-x-auto pb-2">
                <?php foreach($images as $idx => $img):
                    echo '<button onclick="changeImage(\'' . $baseUrl . '/server/admin/' . htmlspecialchars($img) . '\', this)" class="w-20 h-24 flex-shrink-0 rounded-lg overflow-hidden border-2 ' . ($idx===0 ? 'active-thumb border-yellow-600' : 'border-transparent') . ' hover:border-yellow-400 transition">
                        <img src="' . $baseUrl . '/server/admin/' . htmlspecialchars($img) . '" class="w-full h-full object-cover">
                    </button>';
                endforeach; ?>
            </div>
        </div>

        <!-- Details -->
        <div class="lg:w-1/2 flex flex-col">
            <h1 class="text-3xl lg:text-4xl font-playfair font-bold text-gray-900 mb-2 leading-tight">
                <?= htmlspecialchars($product['name_product']) ?>
            </h1>
            
            <div class="flex items-center gap-4 mb-6">
                <div class="flex items-center text-yellow-500">
                    <span class="material-symbols-rounded text-xl">star</span>
                    <span class="font-bold ml-1 text-gray-900"><?= $ratingVal ?></span>
                </div>
                <span class="text-gray-400">|</span>
                <span class="text-gray-500 text-sm"><?= $totalReviews ?> Đánh giá</span>
            </div>

            <div class="flex items-baseline gap-4 mb-8">
                <span class="text-4xl font-bold text-gray-900"><?= number_format($finalPrice, 0, ',', '.') ?>₫</span>
                <?php if($discount > 0):
                    echo '<span class="text-xl text-gray-400 line-through">' . number_format($price, 0, ',', '.') . '₫</span>';
                endif; ?>
            </div>

            <p class="text-gray-600 leading-relaxed mb-8 text-lg">
                <?= nl2br(htmlspecialchars(substr($product['description'], 0, 150))) ?>...
                <a href="#description" class="text-yellow-600 font-medium hover:underline">Xem chi tiết</a>
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                <form action="<?= $baseUrl ?>/cart_action.php" method="POST" class="flex-1">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name_product']) ?>">
                    <input type="hidden" name="product_price" value="<?= $finalPrice ?>">
                    <input type="hidden" name="product_image" value="<?= htmlspecialchars($mainImage) ?>">
                    <input type="hidden" name="quantity" value="1">
                    
                    <button type="submit" class="w-full py-4 bg-black text-white text-lg font-bold rounded-full hover:bg-yellow-600 hover:shadow-lg transition-all transform hover:-translate-y-1">
                        THÊM VÀO GIỎ
                    </button>
                </form>
                
                <form action="<?= $baseUrl ?>/cart_action.php" method="POST" class="flex-1">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name_product']) ?>">
                    <input type="hidden" name="product_price" value="<?= $finalPrice ?>">
                    <input type="hidden" name="product_image" value="<?= htmlspecialchars($mainImage) ?>">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="buy_now" value="true">
                    
                    <button type="submit" class="w-full py-4 bg-yellow-600 text-white text-lg font-bold rounded-full hover:bg-yellow-700 hover:shadow-lg transition-all transform hover:-translate-y-1">
                        MUA NGAY
                    </button>
                </form>

                <button onclick="addToWishlist(<?= $product['id'] ?>, this)" class="w-full sm:w-auto px-6 py-4 border-2 rounded-full flex items-center justify-center gap-2 font-bold hover:border-black hover:text-black transition <?= $isLiked ? 'text-red-500 border-red-200 bg-red-50' : 'text-gray-700 border-gray-200' ?>">
                    <span class="material-symbols-rounded">favorite</span>
                    <span class="sm:hidden">YÊU THÍCH</span>
                </button>
            </div>

            <!-- Guarantee -->
            <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-green-600">check_circle</span>
                    Chính hãng 100%
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-green-600">local_shipping</span>
                    Miễn phí vận chuyển
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-green-600">refresh</span>
                    Đổi trả trong 7 ngày
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-green-600">security</span>
                    Bảo hành 1 năm
                </div>
            </div>
        </div>
    </div>

    <!-- TABS SECTION -->
    <div class="border-t border-gray-200 pt-10" id="description">
        <div class="flex gap-8 border-b border-gray-200 mb-8">
            <button onclick="switchTab('desc')" id="tab-desc" class="pb-4 border-b-2 border-black font-bold text-lg">Mô tả sản phẩm</button>
            <button onclick="switchTab('reviews')" id="tab-reviews" class="pb-4 border-b-2 border-transparent text-gray-500 hover:text-black font-medium text-lg transition">Đánh giá (<?= $totalReviews ?>)</button>
        </div>

        <!-- Description Content -->
        <div id="content-desc" class="prose max-w-none text-gray-700 leading-relaxed">
            <?= nl2br(htmlspecialchars($product['description'])) ?>
        </div>

        <!-- Reviews Content -->
        <div id="content-reviews" class="hidden">
            <div class="grid lg:grid-cols-3 gap-10">
                <!-- List -->
                <div class="lg:col-span-2 space-y-6">
                    <?php if(empty($reviews)):
                        echo '<p class="text-gray-500 italic">Chưa có đánh giá nào.</p>';
                    else:
                        foreach($reviews as $rv):
                        echo '<div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">';
                            echo '<div class="flex items-center justify-between mb-3">';
                                echo '<div class="flex items-center gap-3">';
                                    echo '<div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-600">'.strtoupper(substr($rv['username'], 0, 1)).'</div>';
                                    echo '<div>';
                                        echo '<p class="font-bold text-gray-900">' . htmlspecialchars($rv['username']) . '</p>';
                                        echo '<p class="text-xs text-gray-500">' . date('d/m/Y', strtotime($rv['created_at'])) . '</p>';
                                    echo '</div>';
                                echo '</div>';
                                echo '<div class="flex text-yellow-400">';
                                    for($i=1; $i<=5; $i++):
                                        echo '<span class="material-symbols-rounded text-lg">' . ($i <= $rv['rating'] ? 'star' : 'star_border') . '</span>';
                                    endfor;
                                echo '</div>';
                            echo '</div>';
                            echo '<p class="text-gray-700">' . htmlspecialchars($rv['comment']) . '</p>';
                        echo '</div>';
                        endforeach;
                    endif; ?>
                </div>

                <!-- Form -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 p-6 rounded-2xl sticky top-24">
                        <h3 class="font-bold text-xl mb-4 font-playfair">Viết đánh giá</h3>
                        <?php if($loggedIn): ?>
                            <form method="POST">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium mb-2">Đánh giá của bạn</label>
                                    <div class="star-rating justify-end">
                                        <input type="radio" name="rating" id="star5" value="5" checked><label for="star5">★</label>
                                        <input type="radio" name="rating" id="star4" value="4"><label for="star4">★</label>
                                        <input type="radio" name="rating" id="star3" value="3"><label for="star3">★</label>
                                        <input type="radio" name="rating" id="star2" value="2"><label for="star2">★</label>
                                        <input type="radio" name="rating" id="star1" value="1"><label for="star1">★</label>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium mb-2">Nhận xét</label>
                                    <textarea name="comment" rows="4" class="w-full border border-gray-300 rounded-lg p-3 focus:border-yellow-500 outline-none" required placeholder="Chia sẻ cảm nhận của bạn..."></textarea>
                                </div>
                                <button type="submit" name="submit_review" class="w-full bg-black text-white py-3 rounded-lg font-bold hover:bg-yellow-600 transition">Gửi đánh giá</button>
                            </form>
                        <?php else: ?>
                            <p class="text-gray-600 mb-4">Bạn cần đăng nhập để viết đánh giá.</p>
                            <a href="<?= $baseUrl ?>/auth.php" class="block text-center w-full bg-white border border-black text-black py-2 rounded-lg font-bold hover:bg-black hover:text-white transition">Đăng nhập ngay</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include $docRoot . $baseUrl . '/component/footer.php'; ?>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-24 right-0 z-[9999] p-4 flex flex-col gap-3 pointer-events-none"></div>

<script>
    function changeImage(src, btn) {
        document.getElementById('mainImg').src = src;
        document.querySelectorAll('.active-thumb').forEach(el => el.classList.remove('active-thumb', 'border-yellow-600', 'border-transparent'));
        btn.classList.add('active-thumb', 'border-yellow-600');
        btn.classList.remove('border-transparent');
    }

    function switchTab(tab) {
        if(tab === 'desc') {
            document.getElementById('content-desc').classList.remove('hidden');
            document.getElementById('content-reviews').classList.add('hidden');
            document.getElementById('tab-desc').classList.add('border-black');
            document.getElementById('tab-desc').classList.remove('border-transparent', 'text-gray-500');
            document.getElementById('tab-reviews').classList.remove('border-black');
            document.getElementById('tab-reviews').classList.add('border-transparent', 'text-gray-500');
        } else {
            document.getElementById('content-desc').classList.add('hidden');
            document.getElementById('content-reviews').classList.remove('hidden');
            document.getElementById('tab-reviews').classList.add('border-black');
            document.getElementById('tab-reviews').classList.remove('border-transparent', 'text-gray-500');
            document.getElementById('tab-desc').classList.remove('border-black');
            document.getElementById('tab-desc').classList.add('border-transparent', 'text-gray-500');
        }
    }

    /**
     * Hiển thị thông báo Toast (Giao diện đồng bộ Brand)
     */
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const configs = {
            success: { icon: 'check_circle', classes: 'bg-black text-white border-l-4 border-yellow-500' },
            error: { icon: 'error', classes: 'bg-white text-gray-800 border-l-4 border-red-500 shadow-xl border border-gray-100' },
            info: { icon: 'info', classes: 'bg-gray-800 text-white border-l-4 border-blue-400' }
        };
        const config = configs[type] || configs.success;
        const toast = document.createElement('div');
        toast.className = `${config.classes} flex items-center gap-3 px-6 py-4 rounded shadow-2xl transform translate-x-full transition-all duration-300 ease-out pointer-events-auto min-w-[300px]`;
        toast.innerHTML = `
            <span class="material-symbols-rounded text-2xl ${type === 'success' ? 'text-yellow-500' : (type === 'error' ? 'text-red-500' : 'text-blue-400')}">
                ${config.icon}
            </span>
            <div class="flex-1 font-medium text-sm font-sans tracking-wide">${message}</div>
            <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100 transition"><span class="material-symbols-rounded">close</span></button>
        `;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.remove('translate-x-full'));
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function addToWishlist(id, btn) {
        fetch('<?= $baseUrl ?>/wishlist_action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=add&product_id=' + id
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                if (btn) {
                    const isLiked = btn.classList.contains('text-red-500');
                    if (isLiked) {
                        // Remove red, back to gray
                        btn.classList.remove('text-red-500', 'border-red-200', 'bg-red-50');
                        btn.classList.add('text-gray-700', 'border-gray-200');
                    } else {
                        // Make red
                        btn.classList.remove('text-gray-700', 'border-gray-200');
                        btn.classList.add('text-red-500', 'border-red-200', 'bg-red-50');
                    }
                }
            } else {
                if(data.message === 'Vui lòng đăng nhập!') {
                     showToast('Vui lòng đăng nhập để lưu yêu thích!', 'info');
                } else if(data.message.includes('đã có')) {
                     showToast(data.message, 'info');
                     if (btn) {
                        btn.classList.remove('text-gray-700', 'border-gray-200');
                        btn.classList.add('text-red-500', 'border-red-200', 'bg-red-50');
                     }
                } else {
                    showToast(data.message, 'error');
                }
            }
        })
        .catch(err => showToast('Lỗi kết nối!', 'error'));
    }
</script>

</body>
</html>