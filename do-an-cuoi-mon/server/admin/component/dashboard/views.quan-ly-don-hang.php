<?php
require_once '../models/bill.php';

$billObj = new Bill();
$statusFilter = $_GET['status'] ?? 'pending';
$searchKeyword = $_GET['search'] ?? '';

// 🔹 Lấy toàn bộ đơn hàng theo trạng thái và từ khóa tìm kiếm
$ordersData = $billObj->getAllOrders($statusFilter, $searchKeyword);

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: #fcfcfc;
            padding-top: 5rem;
        }

        .mobile-menu {
            transition: transform 0.3s ease-in-out;
        }

        .table-cell-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .table-responsive .desktop-only {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <header class="bg-gray-900 text-white shadow-lg fixed top-0 left-0 right-0 z-50">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <a href="#" class="flex items-center gap-3 text-pink-500 text-2xl font-extrabold">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="w-8 h-8 text-indigo-500">
                    <path fill-rule="evenodd"
                        d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0015 9h-1.063c-.34-.085-.68-.22-1.008-.4-.95-.513-1.838-.976-2.673-1.35C8.618 6.36 6.375 5.122 5.625 1.5z"
                        clip-rule="evenodd"></path>
                    <path
                        d="M5.625 3c0 4.394 2.518 6.8 5.742 8.56a9.986 9.986 0 011.008.4c.328.18.668.315 1.008.4h1.062a2.25 2.25 0 012.25 2.25v2.812l-5.625-2.25v-5.518c0-.663-.537-1.2-1.2-1.2H9.284C8.62 8.28 8.068 7.29 8.068 6H7.5a.75.75 0 010-1.5h.568c.552-1.127 1.488-2.022 2.56-2.668A4.502 4.502 0 0012 3c2.256 0 4.21.974 5.625 2.518v.487a.75.75 0 01-1.5 0v-.487A3.002 3.002 0 0012 3a3 3 0 00-3 3c0 .898-.364 1.76-.992 2.395A2.25 2.25 0 017.5 9.75v9.525l7.5-3v-5.698a.75.75 0 01.75-.75h1.218A3.752 3.752 0 0121 12.75v6.499c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 01-1.875-1.875v-1.125a.75.75 0 011.5 0v1.125a.375.375 0 00.375.375h12.75a.375.375 0 00.375-.375v-6.499a2.25 2.25 0 00-2.25-2.25h-1.218a.75.75 0 01-.75-.75v-5.518z">
                    </path>
                </svg>
                <span class="hidden sm:inline">Admin Panel</span>
            </a>

            <nav class="hidden md:flex items-center gap-6 text-gray-400 font-medium">
                <a href="dashboard.php" class="hover:text-pink-500 transition">Dashboard</a>
                <a href="dashboard.quan-ly-don-hang.php" class="hover:text-pink-500 transition">Quản lý đơn hàng</a>
                <a href="logout.php" class="text-red-400 font-semibold hover:text-red-500 transition">Đăng xuất</a>
            </nav>

            <button id="menuBtn" class="md:hidden p-2 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-200" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div id="mobileMenu" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden">
            <aside id="menuPanel"
                class="fixed top-0 right-0 w-72 h-full bg-gray-800 shadow-2xl p-6 flex flex-col space-y-5 transform translate-x-full transition-transform duration-300">
                <button id="closeMenu"
                    class="self-end text-gray-400 text-2xl font-bold hover:text-white">&times;</button>
                <a href="dashboard.php" class="hover:text-pink-500 text-lg font-medium">Dashboard</a>
                <a href="dashboard.quan-ly-don-hang.php" class="hover:text-pink-500 text-lg font-medium">Quản lý đơn
                    hàng</a>
                <a href="logout.php" class="text-red-400 text-lg font-medium mt-1">Đăng xuất</a>
            </aside>
        </div>
    </header>

    <main class="container mx-auto p-6 flex-grow">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Quản lý tất cả đơn hàng</h2>

                <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <form method="GET" class="flex-1 w-full sm:w-auto">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
                        <div class="flex">
                            <input type="text" name="search" placeholder="Tìm theo mã đơn hoặc người mua..."
                                value="<?= htmlspecialchars($searchKeyword) ?>"
                                class="w-full sm:w-auto border border-gray-300 rounded-l-lg p-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
                            <button type="submit"
                                class="bg-pink-500 text-white px-4 rounded-r-lg hover:bg-pink-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <div class="flex flex-wrap justify-center sm:justify-start gap-2">
                        <a href="?status=pending"
                            class="px-4 py-2 rounded-full font-semibold transition <?= $statusFilter === 'pending' ? 'bg-pink-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">Đang
                            đợi xử lý</a>
                        <a href="?status=approved"
                            class="px-4 py-2 rounded-full font-semibold transition <?= $statusFilter === 'approved' ? 'bg-pink-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">Đã
                            xử lý</a>
                    </div>
                </div>
            </div>

            <?php if (empty($ordersData)): ?>
                <div class="p-10 text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-20 h-20 mx-auto text-gray-300 mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.182 15.182a4.5 4.5 0 01-6.364 0M8.25 15.75h-.007v-.008H8.25v.008zm4.5 0h-.007v-.008h.007v.008zm4.5 0h-.007v-.008h.007v.008zm-.75-6.75h-9a2.25 2.25 0 00-2.25 2.25v1.5a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-1.5a2.25 2.25 0 00-2.25-2.25z" />
                    </svg>
                    <p>Chưa có đơn hàng nào.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto p-4">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Mã đơn</th>
                                <th class="py-3 px-6 text-left">Người mua</th>
                                <th class="py-3 px-6 text-left">Tổng tiền</th>
                                <th class="py-3 px-6 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            <?php foreach ($ordersData as $order): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-100 transition 
                                <?= $order['status'] === 'pending' ? 'bg-yellow-50' : '' ?>
                                <?= $order['status'] === 'approved' ? 'bg-green-50' : '' ?>
                                <?= $order['status'] === 'cancelled' ? 'bg-red-50' : '' ?>
                            ">
                                    <td class="py-3 px-6 text-left whitespace-nowrap font-medium">
                                        <?= htmlspecialchars($order['id']) ?></td>
                                    <td class="py-3 px-6 text-left table-cell-truncate max-w-xs">
                                        <?= htmlspecialchars($order['username']) ?></td>
                                    <td class="py-3 px-6 text-left text-pink-600 font-bold">
                                        <?= number_format($order['total_price'], 0, ',', '.') ?>₫</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center space-x-2">
                                            <a href="order_detail.php?bill_id=<?= $order['id'] ?>"
                                                class="w-6 h-6 transform hover:text-purple-500 hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        const menuBtn = document.getElementById('menuBtn');
        const closeMenu = document.getElementById('closeMenu');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuPanel = document.getElementById('menuPanel');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
            setTimeout(() => menuPanel.classList.remove('translate-x-full'), 10);
        });

        closeMenu.addEventListener('click', () => {
            menuPanel.classList.add('translate-x-full');
            setTimeout(() => mobileMenu.classList.add('hidden'), 300);
        });

        mobileMenu.addEventListener('click', (e) => {
            if (e.target === mobileMenu) {
                menuPanel.classList.add('translate-x-full');
                setTimeout(() => mobileMenu.classList.add('hidden'), 300);
            }
        });
    </script>
</body>

</html>