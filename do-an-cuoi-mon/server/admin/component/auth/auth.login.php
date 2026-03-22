<?php
require_once __DIR__ . "/../../../models/admin.php";

$result = ["success" => false]; // Default state
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $adminModel = new Admin();
    $result = $adminModel->login($email, $password);
    
    // Note: The Admin::login method already handles setting the secure 'admin_token' cookie.
    // We do not need to set any cookie here manually.
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả Đăng nhập Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInScale {
            0% { opacity: 0; transform: scale(0.9) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-fadeInScale {
            animation: fadeInScale 0.6s ease-out;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.1); opacity: 0; }
            60% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); }
        }
        .animate-bounceIn {
            animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        .animate-shake {
            animation: shake 0.5s;
        }
    </style>
</head>
<body class="font-sans">

<main class="flex items-center justify-center min-h-screen bg-gradient-to-r from-pink-400 via-pink-500 to-pink-600">
  <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-10 animate-fadeInScale">
    
    <?php if ($result['success'] ?? false): ?>
        <div class="text-center text-pink-600 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2 animate-bounceIn" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <h2 class="text-3xl font-extrabold tracking-wide text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-rose-600">
                <?= htmlspecialchars($result['message'] ?? "Đăng nhập thành công!") ?>
            </h2>
        </div>
    <?php else: ?>
        <div class="text-center text-red-500 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2 animate-shake" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <h2 class="text-3xl font-extrabold tracking-wide text-red-600">
                <?= htmlspecialchars($result['message'] ?? "Đăng nhập thất bại!") ?>
            </h2>
        </div>
    <?php endif; ?>

    <?php if ($result['success'] ?? false): ?>
      <div class="space-y-5 text-gray-700 text-center">
        <p class="text-lg flex items-center justify-center gap-2">
          <span class="font-semibold text-pink-600">📧 Email Admin:</span> 
          <span><?= htmlspecialchars($email ?? '') ?></span>
        </p>
      </div>

      <p class="mt-6 text-center text-gray-500">Bạn sẽ được chuyển đến Dashboard sau 3 giây...</p>

      <script>
        setTimeout(() => {
          window.location.href = "./dashboard.php";
        }, 3000);
      </script>
    <?php endif; ?>

    <div class="mt-8 flex justify-center">
      <button 
        onclick="<?= ($result['success'] ?? false) 
          ? "window.location.href='./dashboard.php'" 
          : "window.history.back()" ?>"
        class="px-8 py-3 
          <?= ($result['success'] ?? false) 
              ? 'bg-pink-500 hover:bg-pink-600' 
              : 'bg-red-500 hover:bg-red-600' ?> 
          text-white font-semibold rounded-xl shadow-lg transition duration-300 transform hover:scale-105 hover:shadow-xl">
        <?= ($result['success'] ?? false) ? "📊 Vào Dashboard" : "⬅ Quay lại" ?>
      </button>
    </div>
  </div>
</main>

</body>
</html>