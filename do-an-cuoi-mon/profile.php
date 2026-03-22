<?php
session_start();
require_once __DIR__ . "/server/models/user.php";
require_once __DIR__ . "/server/models/bill.php";

$userObj = new User();
$billObj = new Bill();

$account = $userObj->getAccount();
if (!$account['success']) {
    header("Location: auth.php");
    exit;
}
$user = $account['user'];

$msg = '';
$msgType = '';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_info'])) {
        $name = trim($_POST['username']);
        $email = trim($_POST['email']);
        $address = trim($_POST['address']);
        
        $res = $userObj->updateInfo($user['id'], $name, $email, $address);
        $msg = $res['message'];
        $msgType = $res['success'] ? 'success' : 'error';
        if ($res['success']) {
            // Refresh user data
            $user['username'] = $name;
            $user['email'] = $email;
            $user['address'] = $address;
        }
    } elseif (isset($_POST['change_pass'])) {
        $curPass = $_POST['current_password'];
        $newPass = $_POST['new_password'];
        $cfmPass = $_POST['confirm_password'];
        
        if ($newPass !== $cfmPass) {
            $msg = "Mật khẩu xác nhận không khớp.";
            $msgType = 'error';
        } else {
            $res = $userObj->changePassword($user['id'], $curPass, $newPass);
            $msg = $res['message'];
            $msgType = $res['success'] ? 'success' : 'error';
        }
    }
}

// Get recent orders for dashboard summary
$orders = $billObj->getUserOrders($user['id']);
$recentOrders = array_slice($orders, 0, 3);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hồ sơ của tôi • CHAUTFIFTH</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
<style>
    body { font-family: 'Inter', sans-serif; background: #f9fafb; color: #111; }
    .font-playfair { font-family: 'Playfair Display', serif; }
</style>
</head>
<body>

<?php include_once "./component/header.php"; ?>

<main class="container mx-auto px-4 py-10 min-h-[70vh]">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        <aside class="md:w-1/4">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-yellow-400 to-yellow-600 text-white flex items-center justify-center font-bold text-2xl shadow-md">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg"><?= htmlspecialchars($user['username']) ?></h3>
                        <p class="text-sm text-gray-500 truncate max-w-[150px]"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <button onclick="showTab('dashboard')" id="btn-dashboard" class="w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 font-medium transition bg-black text-white">
                        <span class="material-symbols-rounded">dashboard</span> Tổng quan
                    </button>
                    <button onclick="showTab('info')" id="btn-info" class="w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 font-medium text-gray-600 hover:bg-gray-50 transition">
                        <span class="material-symbols-rounded">person</span> Thông tin cá nhân
                    </button>
                    <button onclick="showTab('password')" id="btn-password" class="w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 font-medium text-gray-600 hover:bg-gray-50 transition">
                        <span class="material-symbols-rounded">lock</span> Đổi mật khẩu
                    </button>
                    <a href="orders.php" class="w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 font-medium text-gray-600 hover:bg-gray-50 transition">
                        <span class="material-symbols-rounded">receipt_long</span> Lịch sử đơn hàng
                    </a>
                    <a href="wishlist.php" class="w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 font-medium text-gray-600 hover:bg-gray-50 transition">
                        <span class="material-symbols-rounded">favorite</span> Sản phẩm yêu thích
                    </a>
                    <a href="logout.php" class="w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 font-medium text-red-600 hover:bg-red-50 transition mt-4">
                        <span class="material-symbols-rounded">logout</span> Đăng xuất
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <div class="md:w-3/4">
            <?php if($msg): ?>
                <div class="mb-6 p-4 rounded-xl flex items-center gap-3 <?= $msgType==='success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' ?>">
                    <span class="material-symbols-rounded"><?= $msgType==='success' ? 'check_circle' : 'error' ?></span>
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <!-- TAB: Dashboard -->
            <div id="tab-dashboard" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 mb-2">Đơn hàng</div>
                        <div class="text-3xl font-bold"><?= count($orders) ?></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 mb-2">Tổng chi tiêu</div>
                        <div class="text-3xl font-bold text-yellow-600">
                            <?php 
                                $totalSpent = 0;
                                foreach($orders as $o) if($o['status'] !== 'cancelled') $totalSpent += $o['total_price'];
                                echo number_format($totalSpent, 0, ',', '.') . '₫';
                            ?>
                        </div>
                    </div>
                     <!-- Banner/Welcome -->
                     <div class="bg-gradient-to-r from-gray-900 to-black text-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-lg mb-1">Chào mừng trở lại!</h3>
                            <p class="text-gray-300 text-sm">Khám phá bộ sưu tập mới ngay.</p>
                        </div>
                        <a href="page/san-pham.php" class="bg-white text-black px-4 py-2 rounded-lg text-sm font-bold hover:bg-yellow-500 hover:text-white transition">Mua sắm</a>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg">Đơn hàng gần đây</h3>
                        <a href="orders.php" class="text-sm text-yellow-600 hover:underline">Xem tất cả</a>
                    </div>
                    <?php if(empty($recentOrders)): ?>
                        <p class="text-gray-500 text-sm">Chưa có đơn hàng nào.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach($recentOrders as $order): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <span class="font-bold text-gray-900">#<?= $order['bill_id'] ?></span>
                                    <span class="text-sm text-gray-500 mx-2">•</span>
                                    <span class="text-sm text-gray-500"><?= date('d/m/Y', strtotime($order['created_at'])) ?></span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="font-bold text-gray-900"><?= number_format($order['total_price'], 0, ',', '.') ?>₫</span>
                                    <span class="text-xs px-2 py-1 rounded-full font-bold uppercase 
                                        <?= $order['status']==='approved'?'bg-green-100 text-green-700':($order['status']==='cancelled'?'bg-red-100 text-red-700':'bg-yellow-100 text-yellow-700') ?>">
                                        <?= $order['status'] ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB: Info -->
            <div id="tab-info" class="hidden bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-2xl font-playfair mb-6">Thông tin cá nhân</h3>
                <form method="POST" class="space-y-6 max-w-lg">
                    <div>
                        <label class="block text-sm font-medium mb-2">Họ và tên</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-yellow-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-yellow-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Địa chỉ</label>
                        <textarea name="address" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-yellow-500 outline-none" required><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>
                    <button type="submit" name="update_info" class="px-8 py-3 bg-black text-white rounded-xl font-bold hover:bg-yellow-600 transition shadow-lg">Lưu thay đổi</button>
                </form>
            </div>

            <!-- TAB: Password -->
            <div id="tab-password" class="hidden bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-2xl font-playfair mb-6">Đổi mật khẩu</h3>
                <form method="POST" class="space-y-6 max-w-lg">
                    <div>
                        <label class="block text-sm font-medium mb-2">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-yellow-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-yellow-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Xác nhận mật khẩu mới</label>
                        <input type="password" name="confirm_password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-yellow-500 outline-none" required>
                    </div>
                    <button type="submit" name="change_pass" class="px-8 py-3 bg-black text-white rounded-xl font-bold hover:bg-yellow-600 transition shadow-lg">Đổi mật khẩu</button>
                </form>
            </div>

        </div>
    </div>
</main>

<?php include_once "./component/footer.php"; ?>

<script>
    function showTab(tabName) {
        // Hide all
        ['dashboard', 'info', 'password'].forEach(t => {
            document.getElementById('tab-' + t).classList.add('hidden');
            const btn = document.getElementById('btn-' + t);
            btn.classList.remove('bg-black', 'text-white');
            btn.classList.add('text-gray-600', 'hover:bg-gray-50');
        });

        // Show selected
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        const activeBtn = document.getElementById('btn-' + tabName);
        activeBtn.classList.remove('text-gray-600', 'hover:bg-gray-50');
        activeBtn.classList.add('bg-black', 'text-white');
    }
</script>

</body>
</html>