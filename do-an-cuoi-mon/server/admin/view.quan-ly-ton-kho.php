<?php
include_once __DIR__ . '/phan-quyen.php';
require_once __DIR__ . '/../models/products.php';

$productModel = new Products();

// Pagination setup
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Get total products count
$totalProductsCount = $productModel->getTotalProductsCount();
$totalPages = ceil($totalProductsCount / $itemsPerPage);

// Fetch paginated products
$items = $productModel->getAll($itemsPerPage, $offset);
?>

<div>
    <?php if (empty($items)): ?>
        <div class="p-12 bg-white rounded-xl shadow-sm border border-gray-100 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-6">
                <span class="material-symbols-rounded text-4xl text-gray-300">inventory_2</span>
            </div>
            <p class="text-gray-500 text-lg font-playfair italic">Kho hàng hiện đang trống.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-900 font-bold text-xs uppercase tracking-wider font-sans">
                        <tr class="border-b border-gray-200">
                            <th class="p-5 w-16">ID</th>
                            <th class="p-5 w-24">Hình ảnh</th>
                            <th class="p-5">Sản phẩm</th>
                            <th class="p-5">Giá bán</th>
                            <th class="p-5 text-center">Giảm giá</th>
                            <th class="p-5 text-center">Tồn kho</th>
                            <th class="p-5">Trạng thái</th>
                            <th class="p-5 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($items as $item): ?>
                            <tr class="hover:bg-yellow-50/50 transition-colors group">
                                <td class="p-5 text-gray-400 font-mono text-xs">#<?= htmlspecialchars($item['id']) ?></td>
                                <td class="p-5">
                                    <div class="relative w-14 h-14 rounded-lg overflow-hidden border border-gray-100 shadow-sm">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="<?= htmlspecialchars($item['image']) ?>"
                                                alt="<?= htmlspecialchars($item['name_product']) ?>"
                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-gray-50 flex items-center justify-center text-gray-300">
                                                <span class="material-symbols-rounded text-xl">image</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <p class="font-semibold text-gray-900 font-playfair text-lg leading-tight"><?= htmlspecialchars($item['name_product']) ?></p>
                                    <p class="text-xs text-gray-500 mt-1 font-sans"><?= htmlspecialchars($item['category'] ?? 'Chưa phân loại') ?></p>
                                </td>
                                <td class="p-5 font-medium text-gray-900"><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                                <td class="p-5 text-center">
                                    <?php if($item['discount'] > 0): ?>
                                        <span class="inline-block px-2 py-1 text-xs font-bold text-red-600 bg-red-50 rounded border border-red-100">-<?= intval($item['discount']) ?>%</span>
                                    <?php else: ?>
                                        <span class="text-gray-300 text-xl">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-5 text-center font-medium">
                                    <?php if ($item['stock'] === -1): ?>
                                        <span class="text-gray-400 text-xl" title="Vô hạn">∞</span>
                                    <?php elseif ($item['stock'] == 0): ?>
                                        <span class="text-red-500 font-bold">0</span>
                                    <?php elseif ($item['stock'] <= 10): ?>
                                        <span class="text-yellow-600 font-bold"><?= intval($item['stock']) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-900"><?= intval($item['stock']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-5">
                                    <?php 
                                        if ($item['stock'] === -1) {
                                            echo '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Sẵn hàng
                                                  </span>';
                                        } elseif ($item['stock'] == 0) {
                                            echo '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100 animate-pulse">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Hết hàng
                                                  </span>';
                                        } elseif ($item['stock'] <= 10) {
                                            echo '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>Sắp hết
                                                  </span>';
                                        } else {
                                            echo '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Ổn định
                                                  </span>';
                                        }
                                    ?>
                                </td>
                                <td class="p-5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="xem-san-pham.php?id=<?= $item['id'] ?>"
                                            class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-black transition-colors" title="Xem chi tiết">
                                            <span class="material-symbols-rounded text-xl">visibility</span>
                                        </a>
                                        <a href="sua-san-pham.php?id=<?= $item['id'] ?>"
                                            class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-yellow-50 text-gray-400 hover:text-yellow-600 transition-colors" title="Chỉnh sửa">
                                            <span class="material-symbols-rounded text-xl">edit</span>
                                        </a>
                                        <a href="xoa-san-pham.php?id=<?= $item['id'] ?>"
                                            onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');"
                                            class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-red-50 text-gray-400 hover:text-red-600 transition-colors" title="Xóa">
                                            <span class="material-symbols-rounded text-xl">delete</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination Controls -->
        <nav class="flex justify-center items-center gap-2 mt-8">
            <?php if ($currentPage > 1): ?>
                <a href="?tab=ton-kho&page=<?= $currentPage - 1 ?>" class="flex items-center justify-center px-4 h-10 me-3 text-base font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700">
                    <span class="material-symbols-rounded text-lg">chevron_left</span>
                    Trước
                </a>
            <?php endif; ?>

            <ul class="flex items-center -space-x-px h-10 text-base">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li>
                        <a href="?tab=ton-kho&page=<?= $i ?>" class="flex items-center justify-center px-4 h-10 leading-tight <?= $i == $currentPage ? 'text-blue-600 bg-blue-50 border border-blue-300 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?tab=ton-kho&page=<?= $currentPage + 1 ?>" class="flex items-center justify-center px-4 h-10 ms-3 text-base font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700">
                    Tiếp
                    <span class="material-symbols-rounded text-lg">chevron_right</span>
                </a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>