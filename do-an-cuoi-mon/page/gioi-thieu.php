<?php
// Bắt đầu session nếu chưa active
if (session_status() == PHP_SESSION_NONE) {
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
if (!defined('BASE_URL')) {
    define('BASE_URL', '/CDPHP/do-an-cuoi-mon');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Về Chúng Tôi • CHAUTFIFTH</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fefcf8; }
        .font-playfair { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="text-gray-800 antialiased">

    <!-- Header -->
    <?php include __DIR__ . '/../component/header.php'; ?>

    <!-- Hero Section -->
    <section class="relative h-[60vh] bg-black overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 opacity-60">
            <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=2070&auto=format&fit=crop" alt="Background" class="w-full h-full object-cover">
        </div>
        <div class="relative z-10 text-center text-white px-4">
            <h1 class="text-5xl md:text-7xl font-bold font-playfair mb-4 animate-fade-in-up">Câu Chuyện <br/> Thương Hiệu</h1>
            <p class="text-lg md:text-xl font-light tracking-wide max-w-2xl mx-auto opacity-90">Hành trình kiến tạo vẻ đẹp hiện đại từ những giá trị kinh điển.</p>
        </div>
    </section>

    <!-- Content 1: Vision & Mission -->
    <section class="py-24">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <h4 class="text-yellow-600 font-bold uppercase tracking-[4px] text-sm mb-4">Tầm Nhìn & Sứ Mệnh</h4>
                    <h2 class="text-4xl font-bold font-playfair mb-6 text-gray-900 leading-tight">Mang Đến Sự Tự Tin <br/> Cho Phụ Nữ Hiện Đại</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Được thành lập vào năm 2025, CHAUTFIFTH ra đời với khát vọng định nghĩa lại chuẩn mực của sự thanh lịch. Chúng tôi tin rằng một chiếc túi xách không chỉ là phụ kiện, mà là người bạn đồng hành, chứa đựng cả thế giới của người phụ nữ.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Sứ mệnh của chúng tôi là tạo ra những sản phẩm thủ công tinh xảo, bền bỉ với thời gian nhưng vẫn mang đậm hơi thở của xu hướng thời trang đương đại.
                    </p>
                </div>
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1594223274512-ad4803739b7c?q=80&w=1957&auto=format&fit=crop" class="w-full h-[500px] object-cover rounded-2xl shadow-xl z-10 relative" alt="Fashion">
                    <div class="absolute -top-6 -left-6 w-32 h-32 bg-yellow-100 rounded-full -z-0"></div>
                    <div class="absolute -bottom-6 -right-6 w-48 h-48 border-2 border-yellow-500 rounded-full -z-0"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content 2: Values (Grid) -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold font-playfair mb-4">Giá Trị Cốt Lõi</h2>
                <div class="w-20 h-1 bg-yellow-500 mx-auto"></div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 bg-gray-50 rounded-2xl text-center hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm text-yellow-600">
                        <span class="material-symbols-rounded text-3xl">workspace_premium</span>
                    </div>
                    <h3 class="text-xl font-bold font-playfair mb-3">Chất Lượng Thượng Hạng</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Sử dụng nguồn nguyên liệu da cao cấp nhất, được tuyển chọn kỹ lưỡng để đảm bảo độ bền và vẻ đẹp theo thời gian.</p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 bg-gray-50 rounded-2xl text-center hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm text-yellow-600">
                        <span class="material-symbols-rounded text-3xl">design_services</span>
                    </div>
                    <h3 class="text-xl font-bold font-playfair mb-3">Thiết Kế Tinh Tế</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Mỗi đường nét, chi tiết đều được chăm chút tỉ mỉ, tối giản nhưng không đơn điệu, sang trọng nhưng không phô trương.</p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 bg-gray-50 rounded-2xl text-center hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm text-yellow-600">
                        <span class="material-symbols-rounded text-3xl">volunteer_activism</span>
                    </div>
                    <h3 class="text-xl font-bold font-playfair mb-3">Tận Tâm Phục Vụ</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Khách hàng là trung tâm của mọi hoạt động. Chúng tôi cam kết mang đến trải nghiệm mua sắm và dịch vụ hậu mãi tuyệt vời nhất.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/../component/footer.php'; ?>

</body>
</html>
