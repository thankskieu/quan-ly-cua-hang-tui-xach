<?php
require_once __DIR__ . "/../database/database.php";

class Reviews {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Lấy đánh giá của một sản phẩm
    public function getReviewsByProduct($productId) {
        $sql = "SELECT r.*, u.username, u.email 
                FROM reviews r 
                JOIN user u ON r.user_id = u.id 
                WHERE r.product_id = ? 
                ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm đánh giá
    public function addReview($userId, $productId, $rating, $comment) {
        try {
            // Kiểm tra xem user đã mua sản phẩm chưa (để cho phép đánh giá - Optional)
            // Hiện tại ta cứ cho phép đánh giá nếu đã login
            
            $sql = "INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId, $productId, $rating, $comment]);
            return ['success' => true, 'message' => 'Cảm ơn bạn đã đánh giá!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    // Tính điểm trung bình
    public function getAverageRating($productId) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>