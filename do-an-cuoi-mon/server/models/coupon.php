<?php
require_once __DIR__ . "/../database/database.php";

class Coupon {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAllCoupons($limit, $offset) {
        $sql = "SELECT * FROM coupons ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCouponsCount() {
        $sql = "SELECT COUNT(id) FROM coupons";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchColumn();
    }

    public function getCouponById($id) {
        $sql = "SELECT * FROM coupons WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCoupon($code, $description, $type, $value, $min_order_value, $max_uses, $usage_limit_per_user, $start_date, $end_date, $max_discount_amount, $is_active, $is_public) {
        $sql = "INSERT INTO coupons (code, description, type, value, min_order_value, max_uses, usage_limit_per_user, start_date, end_date, max_discount_amount, is_active, is_public, created_at) 
                VALUES (:code, :description, :type, :value, :min_order_value, :max_uses, :usage_limit_per_user, :start_date, :end_date, :max_discount_amount, :is_active, :is_public, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':code' => $code,
            ':description' => $description,
            ':type' => $type,
            ':value' => $value,
            ':min_order_value' => $min_order_value,
            ':max_uses' => $max_uses,
            ':usage_limit_per_user' => $usage_limit_per_user,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':max_discount_amount' => $max_discount_amount,
            ':is_active' => $is_active,
            ':is_public' => $is_public
        ]);
    }

    public function updateCoupon($id, $code, $description, $type, $value, $min_order_value, $max_uses, $usage_limit_per_user, $start_date, $end_date, $max_discount_amount, $is_active, $is_public) {
        $sql = "UPDATE coupons SET 
                    code = :code, description = :description, type = :type, value = :value, 
                    min_order_value = :min_order_value, max_uses = :max_uses, 
                    usage_limit_per_user = :usage_limit_per_user, start_date = :start_date, end_date = :end_date, 
                    max_discount_amount = :max_discount_amount, is_active = :is_active, is_public = :is_public, updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':code' => $code,
            ':description' => $description,
            ':type' => $type,
            ':value' => $value,
            ':min_order_value' => $min_order_value,
            ':max_uses' => $max_uses,
            ':usage_limit_per_user' => $usage_limit_per_user,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':max_discount_amount' => $max_discount_amount,
            ':is_active' => $is_active,
            ':is_public' => $is_public
        ]);
    }

    public function deleteCoupon($id) {
        $sql = "DELETE FROM coupons WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>