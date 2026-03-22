<?php
require_once "../models/products.php";

$product = new Products();
$products = $product->getAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .card-buttons {
            display: none;
            flex-direction: column;
            gap: 0.5rem;
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
        }
        .product-card:hover .card-buttons {
            display: flex;
        }
        .modal {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-6 mt-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800">Quản lý Sản phẩm</h1>
            <a href="tao-san-pham.php" class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tạo sản phẩm mới
            </a>
        </div>

        <?php if (empty($products)) : ?>
            <p class="text-center text-gray-500 text-lg">Chưa có sản phẩm nào.</p>
        <?php else : ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($products as $p) : ?>
                    <div class="product-card relative bg-white rounded-xl shadow-lg overflow-hidden flex flex-col group transition-transform hover:scale-105">
                        
                        <div class="relative w-full h-48 overflow-hidden">
                            <?php if (!empty($p['image'])) : ?>
                                <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name_product']) ?>" class="w-full h-full object-cover">
                            <?php else : ?>
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">Không có ảnh</div>
                            <?php endif; ?>
                            <?php if (!empty($p['discount']) && $p['discount'] > 0) : ?>
                                <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full font-bold">
                                    -<?= intval($p['discount']) ?>%
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-buttons absolute top-2 right-2 group-hover:opacity-100 flex flex-col gap-2 transition duration-300">
                            <a href="sua-san-pham.php?id=<?= $p['id'] ?>" class="bg-yellow-400 text-white px-3 py-1 rounded-md shadow-md hover:bg-yellow-500 text-sm text-center">Sửa</a>
                            <button class="delete-btn bg-red-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-red-600 text-sm text-center" data-id="<?= $p['id'] ?>">Xóa</button>
                            <a href="xem-san-pham.php?id=<?= $p['id'] ?>" class="bg-blue-500 text-white px-3 py-1 rounded-md shadow-md hover:bg-blue-600 text-sm text-center">Xem</a>
                        </div>

                        <div class="p-4 flex-1 flex flex-col">
                            <h2 class="text-lg font-semibold mb-2 text-gray-800"><?= htmlspecialchars($p['name_product']) ?></h2>
                            <p class="text-gray-700 mb-2">Giá: <span class="font-bold text-green-600"><?= number_format($p['price'], 0, ',', '.') ?>₫</span></p>
                            <p class="text-gray-600 text-sm flex-1 line-clamp-3"><?= htmlspecialchars($p['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div id="deleteModal" class="modal hidden fixed inset-0 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-sm">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Xác nhận xóa sản phẩm</h3>
            <p class="text-gray-600 mb-6">Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác.</p>
            <div class="flex justify-end gap-3">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Hủy</button>
                <a id="confirmDelete" href="#" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">Xác nhận</a>
            </div>
        </div>
    </div>

    <script>
        const deleteModal = document.getElementById('deleteModal');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        const cancelDeleteBtn = document.getElementById('cancelDelete');
        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.dataset.id;
                confirmDeleteBtn.href = `xoa-san-pham.php?id=${productId}`;
                deleteModal.classList.remove('hidden');
            });
        });

        cancelDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        deleteModal.addEventListener('click', (e) => {
            if (e.target.id === 'deleteModal') {
                deleteModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>