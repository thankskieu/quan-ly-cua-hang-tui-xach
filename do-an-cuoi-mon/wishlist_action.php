<?php
session_start();
require_once __DIR__ . "/server/database/database.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) && !isset($_COOKIE['token'])) {
    // Check login simple logic via user model if needed, 
    // but here we assume if no session user_id (or logic from auth.php), return error
    // For simplicity, let's use the standard User model check
    require_once __DIR__ . "/server/models/user.php";
    $userObj = new User();
    $account = $userObj->getAccount();
    if (!$account['success']) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
        exit;
    }
    $userId = $account['user']['id'];
} else {
    // If logic above covers it, great.
    // Re-verify
    require_once __DIR__ . "/server/models/user.php";
    $userObj = new User();
    $account = $userObj->getAccount();
    $userId = $account['user']['id'];
}

$action = $_POST['action'] ?? '';
$productId = intval($_POST['product_id'] ?? 0);

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
    exit;
}

$db = new Database();
$conn = $db->connect();

if ($action === 'add') {
    try {
        $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId, $productId]);
        echo json_encode(['success' => true, 'message' => 'Đã thêm vào danh sách yêu thích!']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            echo json_encode(['success' => false, 'message' => 'Sản phẩm đã có trong wishlist!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
} elseif ($action === 'remove') {
    $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId, $productId]);
    echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi danh sách yêu thích!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
}
?>