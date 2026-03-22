<?php
require_once __DIR__ . "/../database/database.php";

class Products {
    private $conn;

    /**
     * Khởi tạo kết nối database
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    /**
     * Lấy sản phẩm theo danh mục, có thể giới hạn số lượng
     */
    public function getProductsByCategory(string $category, int $limit = 5): array {
        $sql = "SELECT * FROM products WHERE category = :category ORDER BY id DESC LIMIT " . (int)$limit;
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả danh mục có sản phẩm
     * Trả về mảng có 'name' và 'slug' để dùng trong dropdown
     */
    public function getAllCategories(): array {
        $sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $categories = [];
        foreach ($rows as $c) {
            $categories[] = [
                'name' => $c,
                'slug' => strtolower(preg_replace('/[^a-z0-9\-]+/i', '-', trim($c)))
            ];
        }

        return $categories;
    }

    /**
     * Tạo sản phẩm mới
     */
    public function create(string $name_product, float $price, float $discount, int $stock, string $image, string $description, string $category): array {
        try {
            $sql = "INSERT INTO products (name_product, price, discount, stock, image, description, category)
                    VALUES (:name_product, :price, :discount, :stock, :image, :description, :category)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ":name_product" => $name_product,
                ":price" => $price,
                ":discount" => $discount,
                ":stock" => $stock,
                ":image" => $image,
                ":description" => $description,
                ":category" => $category
            ]);
            return $result ? ["success" => true, "message" => "Tạo sản phẩm thành công!"]
                           : ["success" => false, "message" => "Không thể tạo sản phẩm."];
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Lỗi: " . $e->getMessage()];
        }
    }

    /**
     * Cập nhật sản phẩm theo ID
     */
    public function update(int $id, string $name_product, float $price, float $discount, int $stock, string $image, string $description, string $category): array {
        try {
            $sql = "UPDATE products 
                    SET name_product=:name, price=:price, discount=:discount, stock=:stock, image=:image, description=:desc, category=:category
                    WHERE id=:id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':id' => $id,
                ':name' => $name_product,
                ':price' => $price,
                ':discount' => $discount,
                ':stock' => $stock,
                ':image' => $image,
                ':desc' => $description,
                ':category' => $category
            ]);
            return $result ? ['success' => true, 'message' => 'Cập nhật sản phẩm thành công!']
                           : ['success' => false, 'message' => 'Không thể cập nhật sản phẩm.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    /**
     * Lấy tất cả sản phẩm, có phân trang
     */
    public function getAll(int $limit, int $offset): array {
        $sql = "SELECT * FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tổng số sản phẩm
     */
    public function getTotalProductsCount(): int {
        $sql = "SELECT COUNT(id) FROM products";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchColumn();
    }

    /**
     * Lấy sản phẩm theo ID
     */
    public function getById(int $id): array|false {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Xóa sản phẩm theo ID
     */
    public function delete(int $id): array {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([":id" => $id]);
        return $result ? ["success" => true, "message" => "Xóa sản phẩm thành công!"]
                       : ["success" => false, "message" => "Không thể xóa sản phẩm."];
    }

    /**
     * Lấy top sản phẩm giảm giá
     */
    public function getTopDiscount(int $limit = 5): array {
        $sql = "SELECT * FROM products WHERE discount > 0 ORDER BY discount DESC LIMIT " . (int)$limit;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lọc sản phẩm nâng cao (Pagination + Price Range)
     */
    public function filterProducts(string $search = '', string $category = '', string $sort = '', float $minPrice = 0, float $maxPrice = 0, int $page = 1, int $limit = 12): array {
        $offset = ($page - 1) * $limit;
        
        // Base SQL
        $where = " WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $where .= " AND name_product LIKE :search";
            $params[':search'] = "%" . $search . "%";
        }

        if (!empty($category)) {
            $where .= " AND category = :category";
            $params[':category'] = $category;
        }
        
        if ($minPrice > 0) {
            $where .= " AND price >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }
        
        if ($maxPrice > 0) {
            $where .= " AND price <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        // Count Total
        $countSql = "SELECT COUNT(*) FROM products" . $where;
        $stmtCount = $this->conn->prepare($countSql);
        $stmtCount->execute($params);
        $total = $stmtCount->fetchColumn();

        // Sort
        $order = " ORDER BY id DESC";
        if ($sort === 'price_asc') {
            $order = " ORDER BY price ASC";
        } elseif ($sort === 'price_desc') {
            $order = " ORDER BY price DESC";
        } elseif ($sort === 'name_asc') {
            $order = " ORDER BY name_product ASC";
        }

        // Fetch Data
        $sql = "SELECT * FROM products" . $where . $order . " LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total' => (int)$total,
            'products' => $products,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }

    /**
     * Lấy sản phẩm mới nhất (New Arrivals)
     */
    public function getNewArrivals(int $limit = 8): array {
        return $this->getAll($limit, 0); // getAll đã order by ID DESC
    }

    /**
     * Lấy sản phẩm bán chạy (Best Sellers) - Giả lập bằng Random hoặc logic khác
     */
    public function getBestSellers(int $limit = 8): array {
        $sql = "SELECT * FROM products ORDER BY RAND() LIMIT " . (int)$limit;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * TEMPORARY: Updates all product stock to a random value between 50 and 200.
     */
    public function updateAllStockRandomly(): bool {
        try {
            $sql = "UPDATE products SET stock = FLOOR(RAND() * 151) + 50";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * TEMPORARY: Updates all product prices to a random value between 600,000 and 1,500,000 VND.
     */
    public function updateAllPricesRandomly(): bool {
        try {
            $sql = "UPDATE products SET price = FLOOR(RAND() * 900001) + 600000";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * Updates product prices randomly per category, ensuring prices are rounded to the nearest 10,000 VND
     * and are at least 600,000 VND.
     */
    public function updateCategoryPricesRandomly(): bool {
        try {
            $categories = $this->getAllCategories();
            $minPrice = 600000;
            $maxPrice = 1500000; // Max price for random generation

            foreach ($categories as $categoryData) {
                $categoryName = $categoryData['name'];

                // Generate a random price, then round to nearest 10,000
                $randomPrice = rand($minPrice, $maxPrice);
                $roundedPrice = round($randomPrice / 10000) * 10000;
                
                // Ensure the rounded price is still at least minPrice
                if ($roundedPrice < $minPrice) {
                    $roundedPrice = $minPrice;
                }

                $sql = "UPDATE products SET price = :price WHERE category = :category";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':price', $roundedPrice, PDO::PARAM_INT);
                $stmt->bindParam(':category', $categoryName, PDO::PARAM_STR);
                $stmt->execute();
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error updating category prices randomly: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Lấy sản phẩm bán chạy nhất (Top Bestsellers) dựa trên số lượng bán ra từ đơn hàng hoàn thành
     */
    public function getBestsellingProducts(int $limit = 5): array {
        $sql = "SELECT 
                    p.id, 
                    p.name_product, 
                    p.image, 
                    SUM(o.quantity) as total_quantity_sold
                FROM 
                    products p
                JOIN 
                    orders o ON p.id = o.product_id
                JOIN 
                    bill b ON o.bill_id = b.id
                LEFT JOIN 
                    payments pm ON b.id = pm.bill_id 
                WHERE 
                    DATE(b.created_at) >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                    AND (
                        (b.status = 'delivered' AND b.payment_method = 'cod')
                        OR 
                        (b.status = 'delivered' AND b.payment_method = 'vietqr' AND pm.status = 'verified')
                    )
                GROUP BY 
                    p.id, p.name_product, p.image
                ORDER BY 
                    total_quantity_sold DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
