-- Tạm thời vô hiệu hóa kiểm tra khóa ngoại để xóa và tạo lại bảng
SET FOREIGN_KEY_CHECKS=0;

-- Xóa bảng `coupons` cũ nếu tồn tại để tránh xung đột
DROP TABLE IF EXISTS `coupons`;

-- Bảng để lưu trữ mã giảm giá (coupons)
CREATE TABLE `coupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT,
  `discount_type` ENUM('percent', 'fixed') NOT NULL DEFAULT 'fixed',
  `discount_value` DECIMAL(10, 2) NOT NULL,
  `min_order_value` DECIMAL(10, 2) DEFAULT 0.00,
  `expiry_date` DATETIME,
  `is_active` BOOLEAN NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kích hoạt lại kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS=1;

-- Thêm một vài mã giảm giá mẫu (tùy chọn)
INSERT INTO `coupons` (`code`, `description`, `discount_type`, `discount_value`, `min_order_value`, `expiry_date`, `is_active`) VALUES
('GIAM10', 'Giảm 10% cho mọi đơn hàng', 'percent', '10.00', '0.00', NULL, 1),
('KHAITRUONG', 'Giảm 50,000đ cho đơn hàng từ 200,000đ', 'fixed', '50000.00', '200000.00', '2026-12-31 23:59:59', 1),
('FREESHIP', 'Miễn phí vận chuyển (giảm 30,000đ)', 'fixed', '30000.00', '150000.00', NULL, 0);
