<?php
session_start();
include_once "../../server/models/user.php";

if (!isset($user)) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") { 
      $email = $_POST['email'] ?? '';
      $password = $_POST['password'] ?? '';
      $userModel = new User();
      $user = $userModel->login($email, $password);
    } else { 
      // Redirect to login page if accessed directly without POST
      header("Location: ../../auth.php");
      exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($user['success'] ?? false) ? 'Đăng nhập thành công' : 'Đăng nhập thất bại' ?></title>

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
<body class="flex items-center justify-center min-h-screen">

  <main class="text-center">
    
    <?php if ($user['success'] ?? false): ?>
      <!-- Success State -->
      <div class="bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-gray-100 max-w-md w-full transition-all duration-500">
        <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white ring-4 ring-yellow-100">
            <span class="material-symbols-rounded text-5xl text-yellow-500">done</span>
        </div>

        <h1 class="font-playfair text-3xl font-bold text-gray-900 mb-2">Đăng nhập thành công!</h1>
        <p class="text-gray-600 mb-8">Chào mừng <span class="font-semibold text-yellow-700"><?= htmlspecialchars($user['user']['username'] ?? 'bạn') ?></span> quay trở lại.</p>
        
        <div class="w-full bg-gray-100 h-2 rounded-full mb-2 overflow-hidden">
            <div class="h-full bg-yellow-500 rounded-full animate-progress"></div>
        </div>
        <p class="text-xs text-gray-500">Đang chuyển hướng về trang chủ...</p>
      </div>

        <script>
            setTimeout(() => {
                window.location.href = "../../index.php";
            }, 1800);
        </script>

    <?php else: ?>
      <!-- Failure State -->
      <div class="bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-gray-100 max-w-md w-full transition-all duration-500">
        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white ring-4 ring-red-100">
            <span class="material-symbols-rounded text-5xl text-red-500">close</span>
        </div>
        
        <h1 class="font-playfair text-3xl font-bold text-gray-900 mb-2">Đăng nhập thất bại</h1>
        <p class="text-gray-600 mb-8"><?= htmlspecialchars($user['message'] ?? "Thông tin đăng nhập không chính xác.") ?></p>

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
      animation: progress-bar 1.8s linear forwards;
    }
  </style>

</body>
</html>
