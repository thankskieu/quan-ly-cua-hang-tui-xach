<?php
require_once __DIR__ . "/../database/database.php";

class Bill {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect(); // PDO connection
    }

    // --- PRIVATE HELPERS FOR TRANSACTION ---

    private function _insertBill($data) {
    // Lấy payment method từ bill_action.php gửi qua
    $paymentMethod = $data['payment_method'] ?? 'cod';

    // Trạng thái theo method
    $status = ($paymentMethod === 'vietqr') ? 'pending_payment' : 'processing';

    $sql = "INSERT INTO bill 
            (user_id, phone, address, subtotal, discount_amount, total_price, coupon_id, status, payment_method, created_at)
            VALUES 
            (:user_id, :phone, :address, :subtotal, :discount_amount, :final_amount, :coupon_id, :status, :payment_method, NOW())";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':user_id'         => $data['userId'],
        ':phone'           => $data['phone'],
        ':address'         => $data['address'],
        ':subtotal'        => $data['subtotal'],
        ':discount_amount' => $data['discount_amount'],
        ':final_amount'    => $data['final_amount'],
        ':coupon_id'       => $data['coupon_id'],
        ':status'          => $status,
        ':payment_method'  => $paymentMethod
    ]);

    return $this->conn->lastInsertId();
}

public function processOrder($orderData) {
    try {
        $this->conn->beginTransaction();

        // 1. Create the main bill record
        $billId = $this->_insertBill($orderData);
        if (!$billId) {
            throw new Exception("Không thể tạo hóa đơn.");
        }

        // 2. Insert into payments table if payment method is vietqr
        if ($orderData['payment_method'] === 'vietqr') {
            $stmt = $this->conn->prepare("INSERT INTO payments (bill_id, amount, status, created_at) VALUES (?, ?, 'pending_verification', NOW())");
            $stmt->execute([$billId, $orderData['final_amount']]);
        }

        // 3. Get items from cart (đúng key cart_ids)
        $cartItems = $this->getCartItems($orderData['cart_ids'], $orderData['userId']);
        if (empty($cartItems)) {
            throw new Exception("Không tìm thấy sản phẩm trong giỏ hàng để xử lý.");
        }

        // 3. Create order line items
        $this->createOrders($billId, $cartItems);

        // 4. Handle coupon updates if one was used (đúng key coupon_id)
        if (!empty($orderData['coupon_id'])) {
            $this->_incrementCouponUsage($orderData['coupon_id']);
            $this->_logCouponUsage($orderData['coupon_id'], $orderData['userId'], $billId);
        }

        // 5. Clear the cart
        $this->clearCart($orderData['cart_ids'], $orderData['userId']);

        $this->conn->commit();
        return $billId;

    } catch (Exception $e) {
        $this->conn->rollBack();
        throw $e;
    }
}
    // --- Coupon helpers (BỔ SUNG BẮT BUỘC) ---
private function _incrementCouponUsage($couponId) {
    $stmt = $this->conn->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?");
    $stmt->execute([$couponId]);
}

private function _logCouponUsage($couponId, $userId, $billId) {
    $stmt = $this->conn->prepare("INSERT INTO coupon_usages (coupon_id, user_id, bill_id) VALUES (?, ?, ?)");
    $stmt->execute([$couponId, $userId, $billId]);
}
    // Tạo chi tiết đơn (orders)
    public function createOrders($bill_id, $items) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO orders (bill_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            foreach ($items as $item) {
                $stmt->execute([
                    $bill_id,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi tạo chi tiết đơn: " . $e->getMessage());
        }
    }

    // Xóa giỏ hàng sau khi thanh toán
    public function clearCart($cartIds, $user_id) {
        try {
            if (!empty($cartIds)) {
                $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
                $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ? AND id IN ($placeholders)");
                $stmt->execute(array_merge([$user_id], $cartIds));
            }
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi xóa giỏ hàng: " . $e->getMessage());
        }
    }

    // Lấy sản phẩm trong giỏ (cho BillAction)
    public function getCartItems($cartIds, $user_id) {
        try {
            if (empty($cartIds)) return [];
            $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
            $stmt = $this->conn->prepare("
                SELECT c.id AS cart_id, c.product_id, c.quantity, c.price,
                       p.name_product, p.image
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ? AND c.id IN ($placeholders)
            ");
            $stmt->execute(array_merge([$user_id], $cartIds));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi lấy sản phẩm giỏ hàng: " . $e->getMessage());
        }
    }

    // Lấy đơn hàng của 1 user (user view)
    public function getUserOrders($userId){
        $sql = "SELECT h.id as bill_id, h.total_price, h.status, h.address, h.created_at, h.payment_method,
                       i.id as item_id, i.product_id, i.quantity, p.name_product, p.image, p.price
                FROM bill h
                JOIN orders i ON h.id=i.bill_id
                JOIN products p ON i.product_id=p.id
                WHERE h.user_id=?
                ORDER BY h.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $orders = [];
        foreach($result as $row){
            $id = $row['bill_id'];
            if(!isset($orders[$id])){
                $orders[$id] = [
                    'bill_id'=>$id,
                    'total_price'=>$row['total_price'],
                    'status'=>$row['status'],
                    'address'=>$row['address'],
                    'created_at'=>$row['created_at'],
                    'payment_method'=>$row['payment_method'],
                    'items'=>[]
                ];
            }
            $orders[$id]['items'][] = [
                'item_id'=>$row['item_id'],
                'product_id'=>$row['product_id'],
                'quantity'=>$row['quantity'],
                'name_product'=>$row['name_product'],
                'image'=>$row['image'],
                'price'=>$row['price']
            ];
        }

        return array_values($orders);
    }

    // Lấy thông tin một đơn hàng của user (user detail view)
    public function getOrderByIdAndUserId($billId, $userId) {
        $sql = "SELECT b.id as bill_id, b.total_price, b.subtotal, b.discount_amount, b.status, b.address, b.created_at, b.phone,
                       b.payment_method, /* Thêm dòng này */
                       u.username,
                       i.id as item_id, i.product_id, i.quantity, p.name_product, p.image, p.price,
                       c.code as coupon_code,
                       (SELECT p_sub.status FROM payments p_sub WHERE p_sub.bill_id = b.id ORDER BY p_sub.created_at DESC LIMIT 1) as payment_status
                FROM bill b
                JOIN orders i ON b.id = i.bill_id
                JOIN products p ON i.product_id = p.id
                JOIN user u ON b.user_id = u.id
                LEFT JOIN coupons c ON b.coupon_id = c.id
                WHERE b.id = ? AND b.user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$billId, $userId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return null; // Return null if no order found
        }

        $order = [
            'bill_id' => $result[0]['bill_id'],
            'total_price' => $result[0]['total_price'],
            'subtotal' => $result[0]['subtotal'],
            'discount_amount' => $result[0]['discount_amount'],
            'status' => $result[0]['status'],
            'address' => $result[0]['address'],
            'created_at' => $result[0]['created_at'],
            'phone' => $result[0]['phone'],
            'username' => $result[0]['username'],
            'coupon_code' => $result[0]['coupon_code'],
            'payment_method' => $result[0]['payment_method'], /* Thêm dòng này */
            'payment_status' => $result[0]['payment_status'],
            'items' => []
        ];

        foreach ($result as $row) {
            $order['items'][] = [
                'item_id' => $row['item_id'],
                'product_id' => $row['product_id'],
                'quantity' => $row['quantity'],
                'name_product' => $row['name_product'],
                'image' => $row['image'],
                'price' => $row['price']
            ];
        }

        return $order;
    }

    // Lấy tất cả đơn hàng (admin view) với phân trang
    public function getAllOrders($limit, $offset){
        $sql = "SELECT h.id as bill_id, h.total_price, h.status, h.address, h.created_at,
                       k.username as customer_name
                FROM bill h
                JOIN user k ON h.user_id = k.id
                ORDER BY h.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Lấy tổng số đơn hàng
    public function getTotalOrdersCount(){
        $sql = "SELECT COUNT(id) FROM bill";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchColumn();
    }
    // Lấy đơn hàng theo trạng thái (cho trang xác thực)
    public function getOrdersByStatus($status, $paymentMethod = null) {
        $sql = "SELECT b.id as bill_id, b.total_price, b.status, b.created_at, b.payment_method,
                       u.username as customer_name
                FROM bill b
                JOIN user u ON b.user_id = u.id
                WHERE b.status = :status";
        
        $params = [':status' => $status];

        if ($paymentMethod !== null) {
            $sql .= " AND b.payment_method = :paymentMethod";
            $params[':paymentMethod'] = $paymentMethod;
        }
        
        $sql .= " ORDER BY b.created_at ASC"; // ASC để ưu tiên đơn cũ trước

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy thông tin một đơn hàng theo ID (admin detail)
    public function getOrderById($billId) {
        $sql = "SELECT h.id as bill_id, h.total_price, h.status, h.address, h.created_at, h.user_id, h.phone,
               h.payment_method,
               u.username,
               (SELECT p_sub.status FROM payments p_sub WHERE p_sub.bill_id = h.id ORDER BY p_sub.created_at DESC LIMIT 1) as payment_status
        FROM bill h
        LEFT JOIN user u ON h.user_id = u.id
        WHERE h.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$billId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ensure payment_status is set, even if subquery returns null (no payment record)
        if ($order && !isset($order['payment_status'])) {
            $order['payment_status'] = null; 
        }
        return $order;
    }

    // Lấy các sản phẩm trong một đơn hàng (admin detail)
    public function getItemsByBillId($billId) {
        $sql = "SELECT i.product_id, i.quantity, i.price,
                       p.name_product, p.image
                FROM orders i
                JOIN products p ON i.product_id = p.id
                WHERE i.bill_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$billId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus($billId, $status) {
        $billId = intval($billId);
        // Whitelist allowed statuses to prevent any injection risk
        $allowed_statuses = ['approved', 'confirmed', 'cancelled', 'pending', 'processing', 'pending_payment', 'delivered', 'shipping', 'returned', 'success'];
        if (!in_array($status, $allowed_statuses)) {
            return false;
        }
        
        try {
            $sql = "UPDATE bill SET status = :status WHERE id = :billId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':billId', $billId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0; // Return true if at least one row was affected
        } catch (PDOException $e) {
            // Optional: Log the error message
            // error_log("Error updating status: " . $e->getMessage());
            return false;
        }
    }

    // Khách hàng hủy đơn (kiểm tra quyền sở hữu và trạng thái)
    public function cancelOrderUser($billId, $userId) {
        // Kiểm tra xem đơn hàng có phải của user này và đang pending không
        $sql = "SELECT id FROM bill WHERE id = ? AND user_id = ? AND status IN ('pending', 'processing', 'pending_payment')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$billId, $userId]);
        
        if ($stmt->fetch()) {
            return $this->updateStatus($billId, 'cancelled');
        }
        return false;
    }

    // Thống kê doanh thu 7 ngày gần nhất
    public function getRevenueStats() {
        $sql = "SELECT 
                    DATE(b.created_at) as date, 
                    SUM(b.total_price) as revenue
                FROM 
                    bill b
                LEFT JOIN 
                    payments p ON b.id = p.bill_id
                WHERE 
                    DATE(b.created_at) >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    AND (
                        (b.status = 'delivered' AND b.payment_method = 'cod') 
                        OR 
                        (b.status = 'delivered' AND b.payment_method = 'vietqr' AND p.status = 'verified')
                    )
                GROUP BY 
                    DATE(b.created_at)
                ORDER BY 
                    date ASC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Xác nhận thanh toán (admin)
    public function verifyPayment($billId) {
        try {
            // Update the status of the latest payment record to 'verified'
            $sql = "UPDATE payments SET status = 'verified' WHERE bill_id = :billId ORDER BY created_at DESC LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':billId', $billId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Also update the main bill status to 'confirmed' if it's not already
            // This ensures the order proceeds even if the bill status was stuck at pending_payment
            $this->updateStatus($billId, 'confirmed');

            return true;
        } catch (PDOException $e) {
            // Log the error for debugging purposes
            error_log("Error verifying payment for bill ID $billId: " . $e->getMessage());
            return false;
        }
    }
    // Đếm số lượng đơn hàng hoàn thành 7 ngày gần nhất
    public function getCompletedOrdersCount() {
        $sql = "SELECT 
                    COUNT(DISTINCT b.id) as completed_orders_count
                FROM 
                    bill b
                LEFT JOIN 
                    payments p ON b.id = p.bill_id
                WHERE 
                    DATE(b.created_at) >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    AND (
                        (b.status = 'delivered' AND b.payment_method = 'cod') 
                        OR 
                        (b.status = 'delivered' AND b.payment_method = 'vietqr' AND p.status = 'verified')
                    )";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['completed_orders_count'] ?? 0;
    }
}
?>