<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .mobile-menu {
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

 <header class="bg-gray-900 text-white shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <a href="#" class="flex items-center gap-3 text-pink-500 text-2xl font-extrabold">
           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-indigo-500">
        <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0015 9h-1.063c-.34-.085-.68-.22-1.008-.4-.95-.513-1.838-.976-2.673-1.35C8.618 6.36 6.375 5.122 5.625 1.5z" clip-rule="evenodd"></path>
        <path d="M5.625 3c0 4.394 2.518 6.8 5.742 8.56a9.986 9.986 0 011.008.4c.328.18.668.315 1.008.4h1.062a2.25 2.25 0 012.25 2.25v2.812l-5.625-2.25v-5.518c0-.663-.537-1.2-1.2-1.2H9.284C8.62 8.28 8.068 7.29 8.068 6H7.5a.75.75 0 010-1.5h.568c.552-1.127 1.488-2.022 2.56-2.668A4.502 4.502 0 0012 3c2.256 0 4.21.974 5.625 2.518v.487a.75.75 0 01-1.5 0v-.487A3.002 3.002 0 0012 3a3 3 0 00-3 3c0 .898-.364 1.76-.992 2.395A2.25 2.25 0 017.5 9.75v9.525l7.5-3v-5.698a.75.75 0 01.75-.75h1.218A3.752 3.752 0 0121 12.75v6.499c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 01-1.875-1.875v-1.125a.75.75 0 011.5 0v1.125a.375.375 0 00.375.375h12.75a.375.375 0 00.375-.375v-6.499a2.25 2.25 0 00-2.25-2.25h-1.218a.75.75 0 01-.75-.75v-5.518z"></path>
</svg>
            <span class="hidden sm:inline">Admin Panel</span>
        </a>

        <nav class="hidden md:flex items-center gap-6 text-gray-400 font-medium">
            <a href="dashboard.php" class="hover:text-pink-500 transition">Dashboard</a>
            <a href="view.quan-ly-don-hang.php" class="hover:text-pink-500 transition">Quản lý đơn hàng</a>
            <a href="quan-ly-coupons.php" class="hover:text-pink-500 transition">Quản lý Mã giảm giá</a>
            <a href="logout.php" class="text-red-400 font-semibold hover:text-red-500 transition">Đăng xuất</a>
        </nav>

        <button id="menuBtn" class="md:hidden p-2 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div id="mobileMenu" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden">
        <aside id="menuPanel" class="fixed top-0 right-0 w-72 h-full bg-gray-800 shadow-2xl p-6 flex flex-col space-y-5 transform translate-x-full transition-transform duration-300">
            <button id="closeMenu" class="self-end text-gray-400 text-2xl font-bold hover:text-white">&times;</button>
            <a href="dashboard.php" class="hover:text-pink-500 text-lg font-medium">Dashboard</a>
            <a href="view.quan-ly-don-hang.php" class="hover:text-pink-500 text-lg font-medium">Quản lý đơn hàng</a>
            <a href="quan-ly-coupons.php" class="hover:text-pink-500 text-lg font-medium">Quản lý Mã giảm giá</a>
            <a href="logout.php" class="text-red-400 text-lg font-medium mt-1">Đăng xuất</a>
        </aside>
    </div>
</header>

    <main class="pt-20 p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Tổng quan</h1>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 flex items-center justify-between hover:shadow-xl transition-shadow duration-300">
                <div>
                    <p class="text-gray-500 font-medium">Doanh thu</p>
                    <h3 class="text-3xl font-extrabold text-pink-600 mt-1">125,000,000₫</h3>
                </div>
                <div class="bg-pink-100 p-3 rounded-full text-pink-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V4m0 8v4m0 4.5h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 flex items-center justify-between hover:shadow-xl transition-shadow duration-300">
                <div>
                    <p class="text-gray-500 font-medium">Đơn hàng mới</p>
                    <h3 class="text-3xl font-extrabold text-emerald-600 mt-1">45</h3>
                </div>
                <div class="bg-emerald-100 p-3 rounded-full text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 flex items-center justify-between hover:shadow-xl transition-shadow duration-300">
                <div>
                    <p class="text-gray-500 font-medium">Sản phẩm đã bán</p>
                    <h3 class="text-3xl font-extrabold text-indigo-600 mt-1">120</h3>
                </div>
                <div class="bg-indigo-100 p-3 rounded-full text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <?php include_once "views/list-products.php"?>
        </div>

        <footer class="mt-8 text-center text-gray-500 text-sm">
            &copy; 2025 CHAUTFIFTH. All rights reserved.
        </footer>
    </main>

    <div id="overlay" class="fixed inset-0 bg-black opacity-30 hidden z-30"></div>

    <script>
        const menuBtn = document.getElementById('menuBtn');
        const closeMenuBtn = document.getElementById('closeMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('overlay');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
        });

        closeMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
            overlay.classList.add('hidden');
        });

        overlay.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
</body>
</html>