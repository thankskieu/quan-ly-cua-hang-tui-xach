<?php
require_once __DIR__ . "/user.php";
require_once __DIR__ . "/../database/database.php";

class Cart {
    private $conn;
    private $userModel;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
        $this->userModel = new User();
    }

    private function getCurrentUserId() {
        $account = $this->userModel->getAccount();
        if ($account['success'] && isset($account['user']['id'])) {
            return $account['user']['id'];
        }
        return null;
    }

  public function addToCart($product_id,  $quantity, $price) {
    $user_id = $this->getCurrentUserId();
    if (!$user_id) return ["success" => false, "message" => "Chưa đăng nhập"];

    try {
        // Kiểm tra sản phẩm đã có trong giỏ chưa (user + product_id)
        $check = $this->conn->prepare("SELECT id, quantity FROM Cart WHERE user_id = ? AND product_id = ?");
        $check->execute([$user_id, $product_id]);
        $exist = $check->fetch(PDO::FETCH_ASSOC);

        if ($exist) {
            $newQty = $exist['quantity'] + $quantity;
            $update = $this->conn->prepare("UPDATE Cart SET quantity = ? WHERE id = ?");
            $update->execute([$newQty, $exist['id']]);
        } else {
            $stmt = $this->conn->prepare(
                "INSERT INTO Cart (user_id, product_id,  quantity, price) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$user_id, $product_id,  $quantity, $price]);
        }

        return ["success" => true, "message" => "Đã thêm vào giỏ hàng"];
    } catch (PDOException $e) {
        return ["success" => false, "message" => $e->getMessage()];
    }
}


    public function getCart() {
    $user_id = $this->getCurrentUserId();
    if (!$user_id) return ["success" => false, "message" => "Chưa đăng nhập"];

    try {
        $stmt = $this->conn->prepare("
            SELECT c.id AS cart_id, c.quantity, c.price AS cart_price, 
                   p.id AS product_id, p.name_product AS product_name, p.price AS product_price, p.image
            FROM Cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["success" => true, "data" => $items];
    } catch (PDOException $e) {
        return ["success" => false, "message" => $e->getMessage()];
    }
}


    public function updateQuantity($cart_id, $quantity) {
        try {
            $stmt = $this->conn->prepare("UPDATE Cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$quantity, $cart_id]);
            return ["success" => true, "message" => "Đã cập nhật số lượng"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function removeItem($cart_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM Cart WHERE id = ?");
            $stmt->execute([$cart_id]);
            return ["success" => true, "message" => "Đã xóa sản phẩm khỏi giỏ"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function clearCart() {
        $user_id = $this->getCurrentUserId();
        if (!$user_id) return ["success" => false, "message" => "Chưa đăng nhập"];

        try {
            $stmt = $this->conn->prepare("DELETE FROM Cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            return ["success" => true, "message" => "Đã xóa toàn bộ giỏ hàng"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getTotalItems() {
    $user_id = $this->getCurrentUserId();
    if (!$user_id) {
        return ["success" => false, "total_items" => 0];
    }

    try {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total_items FROM Cart WHERE user_id = ?"
        );
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            "success" => true,
            "total_items" => isset($result['total_items']) ? (int)$result['total_items'] : 0
        ];
    } catch (PDOException $e) {
        return ["success" => false, "total_items" => 0];
    }
}

    public function checkout($items, $address = '') {
        $user_id = $this->getCurrentUserId();
        if (!$user_id) return ["success" => false, "message" => "Chưa đăng nhập"];

        if (empty($items)) return ["success" => false, "message" => "Không có sản phẩm để thanh toán"];

        // Calculate total
        $total_price = 0;
        foreach ($items as $item) {
            if (isset($item['price'], $item['quantity'])) {
                $total_price += $item['price'] * $item['quantity'];
            }
        }

        try {
            $this->conn->beginTransaction();

            // Insert into Bill
            // Table 'bill' columns: user_id, total_price, status, address, created_at
            $stmt = $this->conn->prepare("INSERT INTO bill (user_id, total_price, status, address, created_at) VALUES (?, ?, 'pending', ?, NOW())");
            $stmt->execute([$user_id, $total_price, $address]);
            $bill_id = $this->conn->lastInsertId();

            // Insert into Orders
            // Table 'orders' columns: bill_id, product_id, quantity, price
            $stmtOrder = $this->conn->prepare("INSERT INTO orders (bill_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            
            $cart_ids = [];

            foreach ($items as $item) {
                if (!isset($item['product_id'], $item['quantity'], $item['price'])) {
                     $this->conn->rollBack();
                     return ["success" => false, "message" => "Dữ liệu sản phẩm không hợp lệ"];
                }
                $stmtOrder->execute([$bill_id, $item['product_id'], $item['quantity'], $item['price']]);
                
                if (isset($item['cart_id'])) {
                    $cart_ids[] = $item['cart_id'];
                }
            }

            // Remove from Cart
            if (!empty($cart_ids)) {
                 $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
                 $stmtRemove = $this->conn->prepare("DELETE FROM cart WHERE id IN ($placeholders) AND user_id = ?");
                 $stmtRemove->execute(array_merge($cart_ids, [$user_id]));
            }

            $this->conn->commit();
            return ["success" => true, "message" => "Thanh toán thành công", "order_id" => $bill_id];

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ["success" => false, "message" => "Lỗi thanh toán: " . $e->getMessage()];
        }
    }

}
?>
