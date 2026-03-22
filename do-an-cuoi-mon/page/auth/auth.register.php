<?php
session_start();
include_once "../../server/models/user.php";

// Initialize variables
$user = ['success' => false, 'message' => 'Invalid request.'];
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$sdt = $_POST['sdt'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$address = $_POST['address'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic validation
    if ($password !== $confirm_password) {
        $user = ["success" => false, "message" => "Mật khẩu xác nhận không khớp."];
    } elseif (empty($fullname) || empty($email) || empty($sdt) || empty($password) || empty($address)) {
        $user = ["success" => false, "message" => "Vui lòng điền đầy đủ tất cả các trường."];
    } else {
        $userModel = new User();
        $user = $userModel->createUser($fullname, $email, $sdt, $password, $address);
    }
} else {
    // Redirect if not a POST request
    header("Location: ../../auth.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($user['success'] ?? false) ? 'Đăng ký thành công' : 'Đăng ký thất bại' ?></title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Font Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0,0" />

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #fefcf8;
        }
        .font-playfair { 
            font-family: 'Playfair Display', serif; 
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

  <main class="text-center">
    
    <?php if ($user['success'] ?? false): ?>
      <!-- Success State -->
      <div class="bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-gray-100 max-w-md w-full transition-all duration-500">
        <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white ring-4 ring-yellow-100">
            <span class="material-symbols-rounded text-5xl text-yellow-500">how_to_reg</span>
        </div>

        <h1 class="font-playfair text-3xl font-bold text-gray-900 mb-2">Tạo tài khoản thành công!</h1>
        <p class="text-gray-600 mb-8">Chào mừng bạn đã đến với CHAUTFIFTH.</p>
        
        <div class="w-full bg-gray-100 h-2 rounded-full mb-2 overflow-hidden">
            <div class="h-full bg-yellow-500 rounded-full animate-progress"></div>
        </div>
        <p class="text-xs text-gray-500">Đang chuyển hướng đến trang đăng nhập...</p>
      </div>

        <script>
            setTimeout(() => {
                window.location.href = "../../auth.php";
            }, 2500);
        </script>

    <?php else: ?>
      <!-- Failure State -->
      <div class="bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-gray-100 max-w-md w-full transition-all duration-500">
        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white ring-4 ring-red-100">
            <span class="material-symbols-rounded text-5xl text-red-500">error</span>
        </div>
        
        <h1 class="font-playfair text-3xl font-bold text-gray-900 mb-2">Đăng ký thất bại</h1>
        <p class="text-gray-600 mb-8"><?= htmlspecialchars($user['message'] ?? "Đã có lỗi xảy ra. Vui lòng thử lại.") ?></p>

        <button onclick="window.history.back()" 
                class="w-full px-5 py-3 bg-black text-white text-base font-semibold rounded-full hover:bg-yellow-600 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
            Thử lại
        </button>
      </div>
    <?php endif; ?>

  </main>

  <style>
    @keyframes progress-bar {
      from { width: 0%; }
      to { width: 100%; }
    }
    .animate-progress {
      animation: progress-bar 2.5s linear forwards;
    }
  </style>

</body>
</html>