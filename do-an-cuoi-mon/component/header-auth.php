
<header id="header-page" class="bg-white fixed shadow-lg  top-0 left-0 right-0 z-50">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        
        <a href="index.php" class="text-2xl font-bold text-emerald-600 hover:text-emerald-500 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-indigo-500">
                <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0015 9h-1.063c-.34-.085-.68-.22-1.008-.4-.95-.513-1.838-.976-2.673-1.35C8.618 6.36 6.375 5.122 5.625 1.5z" clip-rule="evenodd" />
                <path d="M5.625 3c0 4.394 2.518 6.8 5.742 8.56a9.986 9.986 0 011.008.4c.328.18.668.315 1.008.4h1.062a2.25 2.25 0 012.25 2.25v2.812l-5.625-2.25v-5.518c0-.663-.537-1.2-1.2-1.2H9.284C8.62 8.28 8.068 7.29 8.068 6H7.5a.75.75 0 010-1.5h.568c.552-1.127 1.488-2.022 2.56-2.668A4.502 4.502 0 0012 3c2.256 0 4.21.974 5.625 2.518v.487a.75.75 0 01-1.5 0v-.487A3.002 3.002 0 0012 3a3 3 0 00-3 3c0 .898-.364 1.76-.992 2.395A2.25 2.25 0 017.5 9.75v9.525l7.5-3v-5.698a.75.75 0 01.75-.75h1.218A3.752 3.752 0 0121 12.75v6.499c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 01-1.875-1.875v-1.125a.75.75 0 011.5 0v1.125a.375.375 0 00.375.375h12.75a.375.375 0 00.375-.375v-6.499a2.25 2.25 0 00-2.25-2.25h-1.218a.75.75 0 01-.75-.75v-5.518z" />
            </svg>
            <span>CHAUTFIFTH</span>
        </a>

        <nav class="hidden md:flex items-center gap-8 text-gray-700 font-medium">
            <a href="index.php" class="hover:text-emerald-600 transition duration-300 ease-in-out">Trang chủ</a>
            <a href="product.php" class="hover:text-emerald-600 transition duration-300 ease-in-out">Sản phẩm</a>
            <a href="contact.php" class="hover:text-emerald-600 transition duration-300 ease-in-out">Liên hệ</a>

        
                <a href="auth.php" class="text-gray-700 hover:text-emerald-600 transition duration-300 ease-in-out">Đăng nhập</a>
                <a href="auth.php" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition duration-300 ease-in-out shadow-md">Đăng ký</a>
 
        </nav>

        <button id="menuBtn" class="md:hidden p-2 text-gray-700 hover:text-emerald-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div id="mobileMenu" class="fixed inset-0 bg-black bg-opacity-40 z-40 hidden">
        <aside id="menuPanel" class="fixed top-0 right-0 w-80 h-full bg-white shadow-2xl p-8 flex flex-col space-y-6 transform translate-x-full transition-transform duration-300">
            <button id="closeMenu" class="self-end text-gray-500 text-3xl font-bold hover:text-red-500 transition-colors">&times;</button>
            
            <div class="flex flex-col gap-4 font-medium text-lg">
                <a href="index.php" class="hover:text-emerald-600 py-2 border-b border-gray-100">Trang chủ</a>
                <a href="product.php" class="hover:text-emerald-600 py-2 border-b border-gray-100">Sản phẩm</a>
                <a href="contact.php" class="hover:text-emerald-600 py-2 border-b border-gray-100">Liên hệ</a>
            </div>
                <div class="flex flex-col gap-4 pt-4 border-t border-gray-200">
                    <a href="auth.php" class="px-4 py-2 text-center text-emerald-600 font-semibold border border-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition">Đăng nhập</a>
                    <a href="auth.php" class="px-4 py-2 text-center bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition">Đăng ký</a>
                </div>
        </aside>
    </div>
</header>
