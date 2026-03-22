<?php
session_start();
require_once __DIR__ . "/server/models/user.php";
require_once __DIR__ . "/server/database/database.php";

$userObj = new User();
$account = $userObj->getAccount();
if (!$account['success']) {
    header("Location: auth.php");
    exit;
}
$user = $account['user'];

$db = new Database();
$conn = $db->connect();

// Get Wishlist items
$sql = "SELECT w.id as wishlist_id, p.* 
        FROM wishlist w 
        JOIN products p ON w.product_id = p.id 
        WHERE w.user_id = ? 
        ORDER BY w.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$user['id']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Yêu Thích • CHAUTFIFTH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #f9fafb; }
        .font-playfair { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body>
    <?php include_once "./component/header.php"; ?>

    <main class="container mx-auto px-4 py-10 min-h-[60vh]">
        <h1 class="text-3xl font-playfair font-bold text-center mb-10">Sản Phẩm Yêu Thích</h1>

        <?php if (empty($items)): ?>
            <div class="text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <span class="material-symbols-rounded text-4xl">favorite_border</span>
                </div>
                <p class="text-gray-500 mb-6">Bạn chưa lưu sản phẩm nào.</p>
                <a href="page/san-pham.php" class="px-6 py-3 bg-black text-white rounded-full font-medium hover:bg-yellow-600 transition">Khám phá ngay</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($items as $p): 
                     $images = !empty($p['images']) ? json_decode($p['images'], true) : [];
                     $mainImage = $p['image'] ?? ($images[0] ?? '');
                     $price = (float)$p['price'];
                     $discount = (float)$p['discount'];
                     $finalPrice = $price * (1 - $discount/100);
                ?>
                <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition relative group" id="wishlist-item-<?= $p['id'] ?>">
                    <button onclick="removeFromWishlist(<?= $p['id'] ?>)" class="absolute top-2 right-2 w-8 h-8 bg-white/80 rounded-full flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition z-10" title="Xóa">
                        <span class="material-symbols-rounded text-lg">close</span>
                    </button>
                    
                    <a href="product_detail.php?id=<?= $p['id'] ?>" class="block relative h-64 overflow-hidden">
                        <img src="<?= BASE_URL ?>/server/admin/<?= htmlspecialchars($mainImage) ?>" 
                             class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image';">
                    </a>
                    
                    <div class="p-4">
                        <h3 class="font-medium text-gray-900 truncate mb-1">
                            <a href="product_detail.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name_product']) ?></a>
                        </h3>
                        <div class="flex gap-2 items-baseline">
                             <span class="font-bold text-gray-900"><?= number_format($finalPrice, 0, ',', '.') ?>₫</span>
                             <?php if ($discount > 0): ?>
                                <span class="text-xs text-gray-400 line-through"><?= number_format($price, 0, ',', '.') ?>₫</span>
                             <?php endif; ?>
                        </div>
                        <form action="cart_action.php" method="POST" class="mt-3">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="product_name" value="<?= htmlspecialchars($p['name_product']) ?>">
                            <input type="hidden" name="product_price" value="<?= $finalPrice ?>">
                            <input type="hidden" name="product_image" value="<?= htmlspecialchars($mainImage) ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button class="w-full py-2 bg-gray-100 text-gray-800 rounded-lg text-sm font-medium hover:bg-black hover:text-white transition">Thêm vào giỏ</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include_once "./component/footer.php"; ?>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-24 right-0 z-[9999] p-4 flex flex-col gap-3 pointer-events-none"></div>

    <script>
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

    function removeFromWishlist(id) {
        fetch('wishlist_action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=remove&product_id=' + id
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                showToast(data.message, 'success');
                const el = document.getElementById('wishlist-item-' + id);
                if(el) {
                    el.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => {
                        el.remove();
                        // Nếu không còn item nào, reload để hiện thông báo trống
                        if (document.querySelectorAll('[id^="wishlist-item-"]').length === 0) {
                            window.location.reload();
                        }
                    }, 300);
                }
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(err => showToast('Lỗi kết nối!', 'error'));
    }
    </script>
</body>
</html>