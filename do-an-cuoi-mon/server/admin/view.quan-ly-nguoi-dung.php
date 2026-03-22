<?php
// This file is included from dashboard.php, so it has access to $conn, $message, $error, etc.
// The $userModel is also available here due to dashboard.php's inclusions.

// Pagination setup
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Get total users count
$totalUsersCount = $userModel->getTotalUsersCount();
$totalPages = ceil($totalUsersCount / $itemsPerPage);

// Fetch paginated users
$result = $userModel->getAllUsers($itemsPerPage, $offset);
$users = $result['success'] ? $result['users'] : [];

// Handle Edit Request
$edit_user = null;
if (isset($_GET['edit_user'])) {
    // Assuming you have a getUserById method in your UserModel
    // For now, using direct PDO from $conn as per existing pattern
    $stmt = $conn->prepare("SELECT id, username, email, sdt, address FROM user WHERE id = ?");
    $stmt->execute([$_GET['edit_user']]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?php if ($message): ?><div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-md" role="alert"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-md" role="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<!-- Add New User Form -->
<div class="bg-white p-6 rounded-2xl shadow-lg mb-8 border border-gray-100">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Thêm Người dùng mới</h2>
    <form action="dashboard.php?tab=nguoi-dung" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Username -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Tên người dùng</label>
                <input type="text" name="username" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" required>
            </div>
            
            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" required>
            </div>

            <!-- Phone Number (sdt) -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                <input type="text" name="sdt" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" required>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                <input type="password" name="password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" required>
            </div>

            <!-- Address -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                <textarea name="address" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" required></textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <button type="submit" name="add_user" class="bg-black hover:bg-yellow-700 text-white font-bold py-2.5 px-8 rounded-xl shadow-lg transition-all">
                Thêm Người dùng
            </button>
        </div>
    </form>
</div>

<?php if ($edit_user): ?>
<!-- Edit Form -->
<div class="bg-white p-6 rounded-2xl shadow-lg mb-8 border border-gray-100">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Sửa thông tin Người dùng</h2>
    <form action="dashboard.php?tab=nguoi-dung" method="POST">
        <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Username -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Tên người dùng</label>
                <input type="text" name="username" value="<?= htmlspecialchars($edit_user['username'] ?? '') ?>" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" required>
            </div>
            
            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($edit_user['email'] ?? '') ?>" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" required>
            </div>

            <!-- Address -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                <textarea name="address" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" required><?= htmlspecialchars($edit_user['address'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <button type="submit" name="save_user" class="bg-black hover:bg-yellow-700 text-white font-bold py-2.5 px-8 rounded-xl shadow-lg transition-all">
                Lưu thay đổi
            </button>
            <a href="dashboard.php?tab=nguoi-dung" class="text-gray-600 hover:text-gray-900 font-medium">Hủy</a>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- User List -->
<div class="bg-white p-6 rounded-2xl shadow-lg overflow-x-auto border border-gray-100">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Danh sách Người dùng</h2>
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50/50">
            <tr>
                <th scope="col" class="px-4 py-3">ID</th>
                <th scope="col" class="px-4 py-3">Tên người dùng</th>
                <th scope="col" class="px-4 py-3">Email</th>
                <th scope="col" class="px-4 py-3">Địa chỉ</th>
                <th scope="col" class="px-4 py-3">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="5" class="text-center py-8 text-gray-400">Chưa có người dùng nào.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr class="bg-white border-b hover:bg-yellow-50/30">
                        <td class="px-4 py-4 font-bold">#<?= $user['id'] ?></td>
                        <td class="px-4 py-4 font-medium text-gray-900"><?= htmlspecialchars($user['username']) ?></td>
                        <td class="px-4 py-4"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="px-4 py-4 text-xs"><?= htmlspecialchars($user['address']) ?></td>
                        <td class="px-4 py-4 flex items-center gap-3">
                            <a href="?tab=nguoi-dung&edit_user=<?= $user['id'] ?>" class="font-medium text-blue-600 hover:underline">Sửa</a>
                            <a href="?tab=nguoi-dung&delete_user=<?= $user['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này? Thao tác này không thể hoàn tác!')" class="font-medium text-red-600 hover:underline">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
