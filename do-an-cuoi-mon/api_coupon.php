<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set timezone for consistency

require_once __DIR__ . "/server/database/database.php";
require_once __DIR__ . "/server/models/user.php";

// ---------------------------------
// Response Helper
// ---------------------------------
function json_response($success, $message, $data = []) {
    $response = ['success' => $success, 'message' => $message];
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    // Clear session on failure
    if (!$success) {
        unset($_SESSION['applied_coupon']);
    }
    echo json_encode($response);
    exit;
}

// ---------------------------------
// Check User Login
// ---------------------------------
$userModel = new User();
$account = $userModel->getAccount();
if (!$account['success']) {
    json_response(false, 'Bạn cần đăng nhập để sử dụng mã giảm giá.');
}
$userId = $account['user']['id'];

// ---------------------------------
// Check Request
// ---------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // --- Logic to fetch available vouchers for the user ---
    $subtotal = floatval($_GET['subtotal'] ?? 0);
    if ($subtotal <= 0) {
        json_response(false, 'Tổng tiền không hợp lệ.');
    }

    try {
        $db = new Database();
        $conn = $db->connect();

        // This query finds all active, valid coupons that the user has NOT used up to their personal limit.
        $sql = "
            SELECT c.*, COUNT(cu.id) as user_usage_count
            FROM coupons c
            LEFT JOIN coupon_usages cu ON c.id = cu.coupon_id AND cu.user_id = :user_id
            WHERE 
                c.is_active = 1
                AND (c.start_date IS NULL OR c.start_date <= NOW())
                AND (c.end_date IS NULL OR c.end_date >= NOW())
                AND (c.max_uses = 0 OR c.used_count < c.max_uses)
                AND c.min_order_value <= :subtotal
            GROUP BY c.id
            HAVING c.usage_limit_per_user = 0 OR user_usage_count < c.usage_limit_per_user
            ORDER BY c.value DESC
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':subtotal' => $subtotal]);
        $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        json_response(true, 'Vouchers fetched successfully.', ['vouchers' => $vouchers]);

    } catch (Exception $e) {
        error_log("Voucher Wallet API Error: " . $e->getMessage());
        json_response(false, 'Lỗi khi tải danh sách voucher.');
    }
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Yêu cầu không hợp lệ.');
}

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

$couponCode = strtoupper(trim($data['coupon_code'] ?? ''));
$subtotal = floatval($data['subtotal'] ?? 0);

if (empty($couponCode)) {
    unset($_SESSION['applied_coupon']);
    json_response(true, 'Đã xóa mã giảm giá.');
}
if ($subtotal <= 0) {
    json_response(false, 'Tổng tiền không hợp lệ.');
}

// ---------------------------------
// Database & Validation
// ---------------------------------
try {
    $db = new Database();
    $conn = $db->connect();

    // 1. Fetch coupon by code
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = :code");
    $stmt->execute([':code' => $couponCode]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$coupon) {
        json_response(false, 'Mã giảm giá không tồn tại.');
    }

    // 2. Check if active
    if (!$coupon['is_active']) {
        json_response(false, 'Mã giảm giá đã bị vô hiệu hóa.');
    }

    // 3. Check date range
    $now = new DateTime();
    if ($coupon['start_date'] && new DateTime($coupon['start_date']) > $now) {
        json_response(false, 'Mã giảm giá chưa có hiệu lực.');
    }
    if ($coupon['end_date'] && new DateTime($coupon['end_date']) < $now) {
        json_response(false, 'Mã giảm giá đã hết hạn.');
    }

    // 4. Check usage limit
    if ($coupon['max_uses'] > 0 && $coupon['used_count'] >= $coupon['max_uses']) {
        json_response(false, 'Mã giảm giá đã hết lượt sử dụng.');
    }
    
    // 5. Check minimum order value
    if ($subtotal < $coupon['min_order_value']) {
        json_response(false, 'Đơn hàng chưa đạt giá trị tối thiểu (' . number_format($coupon['min_order_value']) . 'đ) để áp dụng mã.');
    }

    // 6. Check usage limit per user
    if ($coupon['usage_limit_per_user'] > 0) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM coupon_usages WHERE coupon_id = :coupon_id AND user_id = :user_id");
        $stmt->execute([':coupon_id' => $coupon['id'], ':user_id' => $userId]);
        $userUsageCount = $stmt->fetchColumn();
        if ($userUsageCount >= $coupon['usage_limit_per_user']) {
            json_response(false, 'Bạn đã hết lượt sử dụng mã giảm giá này.');
        }
    }

    // ---------------------------------
    // Calculate Discount
    // ---------------------------------
    $discountAmount = 0;
    if ($coupon['type'] === 'percentage') {
        $discountAmount = ($subtotal * $coupon['value']) / 100;
        if ($coupon['max_discount_amount'] > 0 && $discountAmount > $coupon['max_discount_amount']) {
            $discountAmount = $coupon['max_discount_amount'];
        }
    } else { // fixed
        $discountAmount = $coupon['value'];
    }
    
    // Ensure discount doesn't exceed subtotal
    if ($discountAmount > $subtotal) {
        $discountAmount = $subtotal;
    }
    $discountAmount = round($discountAmount);
    
    // ---------------------------------
    // Store in session and respond
    // ---------------------------------
    $_SESSION['applied_coupon'] = [
        'id' => $coupon['id'],
        'code' => $coupon['code'],
        'discount_amount' => $discountAmount
    ];

    json_response(true, 'Áp dụng mã giảm giá thành công!', [
        'discountAmount' => $discountAmount,
        'couponCode' => $coupon['code']
    ]);

} catch (PDOException $e) {
    // Log error server-side instead of exposing to client
    error_log("Coupon API Error: " . $e->getMessage());
    json_response(false, 'Lỗi hệ thống, vui lòng thử lại sau.');
} catch (Exception $e) {
    error_log("Coupon API Error: " . $e->getMessage());
    json_response(false, 'Đã có lỗi xảy ra.');
}
?>
