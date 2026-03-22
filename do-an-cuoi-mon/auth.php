<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Đăng nhập / Đăng ký</title>

  <!-- Tailwind + AnimeJS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<?php setcookie("123a", '123dcv'); ?>

<body class="">

  <!-- Khối chính -->
  <div class="h-screen">
    <div class="flex justify-center items-center h-full">
      <main
        class="z-10 inline-block sm:w-[400px] backdrop-blur-md bg-white/90 rounded-3xl shadow-xl p-6 sm:p-8 border border-white/50 transition duration-300 hover:shadow-yellow-200">

        <!-- Logo chữ CHAUTFIFTH -->
        <div class="text-center mb-4">
          <span class="text-4xl font-bold text-yellow-500 animate-bounce">
            CHAUTFIFTH
          </span>
        </div>

        <h2 id="formTitle" class="text-2xl sm:text-3xl font-extrabold text-yellow-600 text-center mb-6">Đăng nhập</h2>

        <!-- Toggle -->
        <div class="flex p-1 bg-gray-100 rounded-full mb-6 shadow-inner">
          <button id="btnLogin"
            class="w-1/2 py-2 text-yellow-600 font-medium rounded-full transition hover:bg-white/60">Đăng nhập</button>
          <button id="btnRegister"
            class="w-1/2 py-2 text-yellow-600 font-medium rounded-full transition hover:bg-white/60">Đăng ký</button>
        </div>

        <!-- Form Login -->
        <form id="formLogin" method="POST" action="./page/auth/auth.login.php" class="flex flex-col gap-y-4 animate-fadeIn">
          <div class="relative">
            <input id="loginEmail" name="email" type="email" placeholder="Địa chỉ Email"
              class="w-full p-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-400"
              required>
            <i class="fa-solid fa-envelope absolute left-4 top-3.5 text-gray-500"></i>
          </div>

          <div class="relative">
            <input id="loginPassword" name="password" type="password" placeholder="Mật khẩu của bạn"
              class="w-full p-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-400"
              required>
            <i class="fa-solid fa-lock absolute left-4 top-3.5 text-gray-500"></i>
          </div>

          <button type="submit"
            class="bg-yellow-500 text-white py-3 rounded-xl font-bold text-lg hover:bg-yellow-600 transition duration-300 shadow-lg mt-2">
            ĐĂNG NHẬP
          </button>
        </form>

        <!-- Form Register -->
        <form id="formRegister" method="POST" action="./page/auth/auth.register.php"
          class="hidden flex flex-col gap-y-4 animate-fadeIn">
          <div class="relative">
            <input name="fullname" type="text" placeholder="Họ và tên"
              class="w-full p-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-400"
              required>
            <i class="fa-solid fa-user absolute left-4 top-3.5 text-gray-500"></i>
          </div>

          <div class="relative">
            <input name="email" type="email" placeholder="Email"
              class="w-full p-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-400"
              required>
            <i class="fa-solid fa-envelope absolute left-4 top-3.5 text-gray-500"></i>
          </div>

          <div class="relative">
            <input name="sdt" type="tel" placeholder="Số điện thoại"
              class="w-full p-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-400"
              required pattern="[0-9]{10}" maxlength="10" title="Số điện thoại phải bao gồm 10 chữ số.">
            <i class="fa-solid fa-phone absolute left-4 top-3.5 text-gray-500"></i>
          </div>

          <div class="relative">
            <input name="password" type="password" placeholder="Mật khẩu"
              class="w-full p-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-400"
              required>
            <i class="fa-solid fa-key absolute left-4 top-3.5 text-gray-500"></i>
          </div>

          <div class="relative">
            <input name="confirm_password" type="password" placeholder="Xác nhận mật khẩu"
              class="w-full p-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-400"
              required>
            <i class="fa-solid fa-check absolute left-4 top-3.5 text-gray-500"></i>
          </div>

          <div class="relative">
            <input name="address" type="text" placeholder="Địa chỉ giao hàng"
              class="w-full p-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:border-yellow-400"
              required>
            <i class="fa-solid fa-map-marker-alt absolute left-4 top-3.5 text-gray-500"></i>
          </div>

          <button type="submit"
            class="bg-yellow-500 text-white py-3 rounded-xl font-bold text-lg hover:bg-yellow-600 transition duration-300 shadow-lg mt-2">
            ĐĂNG KÝ
          </button>
        </form>
      </main>
    </div>
  </div>

  <style>
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fadeIn {
      animation: fadeIn 0.4s ease-in-out;
    }

    /* Toggle active */
    .active {
      background-color: #f59e0b; /* Tailwind yellow-500 */
      color: white;
    }
  </style>

  <script>
    const btnLogin = document.getElementById("btnLogin");
    const btnRegister = document.getElementById("btnRegister");
    const formLogin = document.getElementById("formLogin");
    const formRegister = document.getElementById("formRegister");
    const formTitle = document.getElementById("formTitle");

    const active = (btn) => { btn.classList.add("bg-yellow-500", "text-white"); btn.classList.remove("text-yellow-600"); }
    const inactive = (btn) => { btn.classList.remove("bg-yellow-500", "text-white"); btn.classList.add("text-yellow-600"); }

    btnLogin.addEventListener("click", () => {
      formLogin.classList.remove("hidden");
      formRegister.classList.add("hidden");
      formTitle.textContent = "Đăng nhập";
      active(btnLogin);
      inactive(btnRegister);
      anime({ targets: formLogin, opacity: [0, 1], translateY: [15, 0], duration: 500, easing: 'easeOutQuad' });
    });

    btnRegister.addEventListener("click", () => {
      formRegister.classList.remove("hidden");
      formLogin.classList.add("hidden");
      formTitle.textContent = "Đăng ký";
      active(btnRegister);
      inactive(btnLogin);
      anime({ targets: formRegister, opacity: [0, 1], translateY: [15, 0], duration: 500, easing: 'easeOutQuad' });
    });

    active(btnLogin);
  </script>
</body>

</html>