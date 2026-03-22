<?php
// Bắt buộc phân quyền admin
include_once __DIR__ . '/phan-quyen.php';
require_once __DIR__ . '/../models/user.php'; // Include User model
require_once __DIR__ . '/../models/coupon.php'; // Include Coupon model

// Xác định tab hiện tại để active menu
$tab = $_GET['tab'] ?? 'thong-ke';

// --- Coupon & Payment Management Logic ---
$message = '';
$error = '';
$db = new Database();
$conn = $db->connect();
$userModel = new User(); // Instantiate User model
$couponModel = new Coupon(); // Instantiate Coupon model

try {
    // Handle Quick Status Update from Order List
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'quick_update_status') {
        require_once __DIR__ . '/../models/bill.php';
        $billObj = new Bill();
        $billId = $_POST['bill_id'];
        $newStatus = $_POST['new_status'];
        if ($billId && $newStatus) {
            $billObj->updateStatus($billId, $newStatus);
            // Add a success message to session to provide feedback
            $_SESSION['message'] = "Đã cập nhật trạng thái cho đơn hàng #{$billId}.";
            header("Location: dashboard.php?tab=don-hang");
            exit;
        }
    }

    // Handle Payment Confirmation POST
    if ($tab === 'xac-thuc' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve') {
        require_once __DIR__ . '/../models/bill.php';
        $billObj = new Bill();
        $billId = $_POST['bill_id'];
        if ($billId) {
            $billObj->updateStatus($billId, 'confirmed');
            header("Location: order_detail.php?bill_id=" . $billId);
            exit;
        }
    }

// Handle POST for Save/Update Coupon
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_coupon'])) {
        // Sanitize and prepare data
        $id = !empty($_POST['id']) ? intval($_POST['id']) : null;
        $code = strtoupper(trim($_POST['code']));
        $description = trim($_POST['description']);
        $type = $_POST['type'];
        $value = floatval($_POST['value']);
        $min_order_value = !empty($_POST['min_order_value']) ? floatval($_POST['min_order_value']) : 0;
        $max_uses = !empty($_POST['max_uses']) ? intval($_POST['max_uses']) : 0;
        $usage_limit_per_user = !empty($_POST['usage_limit_per_user']) ? intval($_POST['usage_limit_per_user']) : 1;
        $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $max_discount_amount = !empty($_POST['max_discount_amount']) ? floatval($_POST['max_discount_amount']) : null;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $is_public = isset($_POST['is_public']) ? 1 : 0;

        // Basic validation
        if (empty($code) || empty($type) || $value <= 0) {
            $_SESSION['error'] = 'Mã, loại và giá trị của coupon không được để trống và giá trị phải lớn hơn 0.';
            header("Location: dashboard.php?tab=coupons");
            exit;
        }

        $success = false;
        if ($id) {
            // Update existing coupon
            $success = $couponModel->updateCoupon($id, $code, $description, $type, $value, $min_order_value, $max_uses, $usage_limit_per_user, $start_date, $end_date, $max_discount_amount, $is_active, $is_public);
        } else {
            // Insert new coupon
            $success = $couponModel->createCoupon($code, $description, $type, $value, $min_order_value, $max_uses, $usage_limit_per_user, $start_date, $end_date, $max_discount_amount, $is_active, $is_public);
        }

        if ($success) {
            $_SESSION['message'] = 'Đã ' . ($id ? 'cập nhật' : 'thêm') . ' mã giảm giá thành công!';
        } else {
            $_SESSION['error'] = 'Lỗi: Không thể lưu mã giảm giá.';
        }
        header("Location: dashboard.php?tab=coupons");
        exit;
    }

    // Handle GET for Delete Coupon
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $success = $couponModel->deleteCoupon($id);
        if ($success) {
            $_SESSION['message'] = 'Đã xóa mã giảm giá thành công.';
        } else {
            $_SESSION['error'] = 'Lỗi: Không thể xóa mã giảm giá.';
        }
        header("Location: dashboard.php?tab=coupons");
        exit;
    }

    // Handle POST for Add New User
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $sdt = trim($_POST['sdt']);
        $password = trim($_POST['password']);
        $address = trim($_POST['address']);

        // Basic validation
        if (empty($username) || empty($email) || empty($sdt) || empty($password) || empty($address)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ các trường để thêm người dùng.';
        } else {
            $result = $userModel->createUser($username, $email, $sdt, $password, $address);
            if ($result['success']) {
                $_SESSION['message'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        }
        header("Location: dashboard.php?tab=nguoi-dung");
        exit;
    }


    // Handle POST for Save/Update User
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
        $id = !empty($_POST['id']) ? intval($_POST['id']) : null;
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $address = trim($_POST['address']);
        
        // Basic validation
        if (empty($username) || empty($email) || empty($address)) {
            $_SESSION['error'] = 'Tên người dùng, email, và địa chỉ không được để trống.';
        } elseif (!$id) {
            $_SESSION['error'] = 'Lỗi: Không tìm thấy ID người dùng để cập nhật.';
        } else {
            $result = $userModel->updateInfo($id, $username, $email, $address);
            if ($result['success']) {
                $_SESSION['message'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        }
        header("Location: dashboard.php?tab=nguoi-dung");
        exit;
    }

    // Handle GET for Delete User
    if (isset($_GET['delete_user'])) {
        $id = intval($_GET['delete_user']);

        // Optional: Prevent deleting the main admin (e.g., user with ID 1)
        if ($id === 1) {
            $_SESSION['error'] = 'Không thể xóa người dùng quản trị viên chính.';
        } else {
            $result = $userModel->deleteUser($id);
            if ($result['success']) {
                $_SESSION['message'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        }
        header("Location: dashboard.php?tab=nguoi-dung");
        exit;
    }



} catch (PDOException $e) {
    // ... (existing error handling is here, collapsed for brevity)
} catch (Exception $e) {
    // ... (existing error handling is here, collapsed for brevity)
}

// Check for session messages to display
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
// --- End Management Logic ---


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Hệ Thống - CHAUTFIFTH</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fefcf8; }
        h1, h2, h3, .font-playfair { font-family: 'Playfair Display', serif; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</head>
<body class="text-gray-800 antialiased">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-72 bg-white border-r border-gray-100 flex flex-col shadow-sm z-20">
            <!-- Logo / Brand -->
            <div class="h-20 flex items-center justify-center border-b border-gray-50">
                <a href="dashboard.php" class="text-2xl font-bold tracking-tight text-gray-900 font-playfair hover:text-yellow-600 transition-colors">
                    CHAUTFIFTH <span class="text-xs font-sans font-medium text-gray-400 block text-center tracking-widest uppercase mt-1">Admin Panel</span>
                </a>
            </div>

            <!-- Menu -->
            <nav class="flex-1 overflow-y-auto py-6 px-4">
                <ul class="space-y-2">
                    <!-- Mục Thống kê -->
                    <li>
                        <a href="?tab=thong-ke" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-300 group <?= $tab === 'thong-ke' ? 'bg-black text-white shadow-lg shadow-gray-200' : 'text-gray-500 hover:bg-yellow-50 hover:text-yellow-700' ?>">
                            <span class="material-symbols-rounded text-[22px] <?= $tab === 'thong-ke' ? 'text-yellow-400' : 'group-hover:text-yellow-600' ?>">analytics</span>
                            <span class="font-medium">Tổng quan</span>
                        </a>
                    </li>

                    <div class="pt-6 pb-2 pl-4 text-xs font-bold text-gray-400 uppercase tracking-widest font-sans">Quản lý</div>

                    <!-- Mục Đơn hàng -->
                    <li>
                        <a href="?tab=don-hang" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-300 group <?= $tab === 'don-hang' ? 'bg-black text-white shadow-lg shadow-gray-200' : 'text-gray-500 hover:bg-yellow-50 hover:text-yellow-700' ?>">
                            <span class="material-symbols-rounded text-[22px] <?= $tab === 'don-hang' ? 'text-yellow-400' : 'group-hover:text-yellow-600' ?>">receipt_long</span>
                            <span class="font-medium">Đơn hàng</span>
                        </a>
                    </li>

                    <!-- Mục Xác thực TT -->
                    <li>
                        <a href="?tab=xac-thuc" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-300 group <?= $tab === 'xac-thuc' ? 'bg-black text-white shadow-lg shadow-gray-200' : 'text-gray-500 hover:bg-yellow-50 hover:text-yellow-700' ?>">
                            <span class="material-symbols-rounded text-[22px] <?= $tab === 'xac-thuc' ? 'text-yellow-400' : 'group-hover:text-yellow-600' ?>">published_with_changes</span>
                            <span class="font-medium">Xác thực Thanh toán</span>
                        </a>
                    </li>

                    <!-- Mục Vận đơn -->
                    <li>
                        <a href="?tab=van-don" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-300 group <?= $tab === 'van-don' ? 'bg-black text-white shadow-lg shadow-gray-200' : 'text-gray-500 hover:bg-yellow-50 hover:text-yellow-700' ?>">
                            <span class="material-symbols-rounded text-[22px] <?= $tab === 'van-don' ? 'text-yellow-400' : 'group-hover:text-yellow-600' ?>">local_shipping</span>
                            <span class="font-medium">Vận đơn</span>
                        </a>
                    </li>



                    <!-- Mục Tồn kho -->
                    <li>
                        <a href="?tab=ton-kho" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-300 group <?= $tab === 'ton-kho' ? 'bg-black text-white shadow-lg shadow-gray-200' : 'text-gray-500 hover:bg-yellow-50 hover:text-yellow-700' ?>">
                            <span class="material-symbols-rounded text-[22px] <?= $tab === 'ton-kho' ? 'text-yellow-400' : 'group-hover:text-yellow-600' ?>">inventory_2</span>
                            <span class="font-medium">Sản phẩm & Kho</span>
                        </a>
                    </li>
                    
                    <!-- Mục Mã giảm giá -->
                    <li>
                        <a href="?tab=coupons" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-300 group <?= $tab === 'coupons' ? 'bg-black text-white shadow-lg shadow-gray-200' : 'text-gray-500 hover:bg-yellow-50 hover:text-yellow-700' ?>">
                            <span class="material-symbols-rounded text-[22px] <?= $tab === 'coupons' ? 'text-yellow-400' : 'group-hover:text-yellow-600' ?>">sell</span>
                            <span class="font-medium">Mã giảm giá</span>
                        </a>
                    </li>
                    <!-- Mục Người dùng -->
                    <li>
                        <a href="?tab=nguoi-dung" class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-300 group <?= $tab === 'nguoi-dung' ? 'bg-black text-white shadow-lg shadow-gray-200' : 'text-gray-500 hover:bg-yellow-50 hover:text-yellow-700' ?>">
                            <span class="material-symbols-rounded text-[22px] <?= $tab === 'nguoi-dung' ? 'text-yellow-400' : 'group-hover:text-yellow-600' ?>">group</span>
                            <span class="font-medium">Người dùng</span>
                        </a>
                    </li>

                </ul>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 m-4 bg-gray-50 rounded-2xl border border-gray-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center text-white font-bold shadow-md shadow-yellow-200">
                        A
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Administrator</p>
                        <p class="text-xs text-gray-500">Super Admin</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="../../index.php" class="flex-1 flex items-center justify-center gap-2 py-2 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:border-yellow-500 hover:text-yellow-600 transition-colors">
                        <span class="material-symbols-rounded text-base">home</span> Web
                    </a>
                    <a href="logout.php" class="flex-1 flex items-center justify-center gap-2 py-2 text-xs font-semibold text-red-500 bg-white border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-colors">
                        <span class="material-symbols-rounded text-base">logout</span> Thoát
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-[#fefcf8]">
            
            <!-- Top Header (Minimalist) -->
            <header class="h-20 flex items-center justify-between px-8 py-4 bg-[#fefcf8]/80 backdrop-blur-md sticky top-0 z-10">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 font-playfair">
                        <?php
                            switch($tab) {
                                case 'don-hang': echo 'Quản lý đơn hàng'; break;
                                case 'xac-thuc': echo 'Xác thực Thanh toán'; break;
                                case 'van-don': echo 'Vận chuyển & Giao nhận'; break;
                                case 'ton-kho': echo 'Kho hàng & Sản phẩm'; break;
                                case 'coupons': echo 'Quản lý Mã giảm giá'; break;
                                case 'nguoi-dung': echo 'Quản lý Người dùng'; break;
                                default: echo 'Tổng quan kinh doanh';
                            }
                        ?>
                    </h2>
                    <p class="text-sm text-gray-500 font-medium mt-0.5"><?= date('l, d F Y') ?></p>
                </div>
                
                <div class="flex items-center gap-4">
                    <?php if ($tab === 'thong-ke'): ?>
                    <a href="export_report.php?type=revenue" class="px-5 py-2.5 bg-green-600 text-white rounded-xl font-bold text-sm hover:bg-green-700 transition shadow-lg flex items-center gap-2">
                        <span class="material-symbols-rounded text-lg">download</span>
                        Xuất báo cáo (Excel)
                    </a>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Scrollable Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 pt-2">
                <div class="animate-fade-in-up">
                    <?php
                                        if ($tab === 'don-hang') {
                                            include_once __DIR__ . '/view.quan-ly-don-hang.php';
                                                            } elseif ($tab === 'xac-thuc') {
                                                                include_once __DIR__ . '/view.xac-thuc-thanh-toan.php';
                                                            } elseif ($tab === 'van-don') {
                                                                include_once __DIR__ . '/view.quan-ly-van-don.php';
                                                            } elseif ($tab === 'ton-kho') {                                            include_once __DIR__ . '/view.quan-ly-ton-kho.php';
                                        } elseif ($tab === 'coupons') {
                        include_once __DIR__ . '/view.quan-ly-coupons.php';
                    } elseif ($tab === 'nguoi-dung') {
                        include_once __DIR__ . '/view.quan-ly-nguoi-dung.php';
                    } elseif ($tab === 'thong-ke') {
                        include_once __DIR__ . '/view.thong-ke.php';
                    } else {
                        echo "<div class='bg-red-50 text-red-600 p-4 rounded-xl border border-red-200'>Tab không hợp lệ.</div>";
                    }
                    ?>
                </div>
            </main>
        </div>

    </div>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
    </style>
</body>
</html>
