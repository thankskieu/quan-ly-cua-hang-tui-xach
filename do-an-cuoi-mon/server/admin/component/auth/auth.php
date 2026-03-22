<main class="flex items-center justify-center min-h-screen bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">
  <div
    class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 relative overflow-hidden animate-fade-in-down"
  >
    <!-- Tiêu đề -->
    <h2 class="text-3xl font-bold text-center text-gray-700 mb-8">
      Đăng nhập Admin
    </h2>

    <!-- Form -->
    <form action="auth.login.php" method="POST" class="space-y-6">
      <!-- Email -->
      <div class="transition transform hover:scale-105 duration-200">
        <label class="block text-gray-600 mb-2 font-medium">Email</label>
        <input
          type="email"
          name="email"
          placeholder="Nhập email"
          class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-400 focus:outline-none transition"
          required
        />
      </div>

      <!-- Password -->
      <div class="transition transform hover:scale-105 duration-200">
        <label class="block text-gray-600 mb-2 font-medium">Mật khẩu</label>
        <input
          type="password"
          name="password"
          placeholder="Nhập mật khẩu"
          class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-400 focus:outline-none transition"
          required
        />
      </div>

      <!-- Nút đăng nhập -->
      <button
        type="submit"
        class="w-full bg-indigo-500 text-white py-3 px-6 rounded-xl shadow-md hover:bg-indigo-600 transform hover:scale-105 transition duration-300 ease-in-out"
      >
        Đăng nhập
      </button>
    </form>
  </div>

  <!-- Thêm animation -->
  <style>
    @keyframes fade-in-down {
      0% {
        opacity: 0;
        transform: translateY(-20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fade-in-down {
      animation: fade-in-down 0.6s ease-out;
    }
  </style>
</main>
