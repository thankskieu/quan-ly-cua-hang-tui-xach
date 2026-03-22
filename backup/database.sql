-- --------------------------------------------------------
-- Máy chủ:                      127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Phiên bản:           12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for tui_xach
SET foreign_key_checks = 0;

CREATE DATABASE IF NOT EXISTS `tui_xach` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `tui_xach`;

-- Dumping structure for table tui_xach.administrator
CREATE TABLE IF NOT EXISTS `administrator` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table tui_xach.administrator: ~0 rows (approximately)
DELETE FROM `administrator`;
INSERT INTO `administrator` (`id`, `email`, `password`, `created_at`, `updated_at`) VALUES
	(1, 'admin@gmail.com', '$2y$10$esJq5Y52qNojoLZf2XwDk.kRpATrAh9LJqgcVXg0j7jcfE2CHGFtq', '2025-09-26 17:40:30', '2025-09-26 17:40:30');

-- Dumping structure for table tui_xach.bill
CREATE TABLE IF NOT EXISTS `bill` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','cancelled') DEFAULT 'pending',
  `address` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table tui_xach.bill: ~19 rows (approximately)
DELETE FROM `bill`;
INSERT INTO `bill` (`id`, `user_id`, `total_price`, `status`, `address`, `created_at`) VALUES
	(2, 13, 111111.00, 'approved', 'z', '2025-10-03 06:34:14'),
	(3, 13, 111111.00, 'cancelled', 'z', '2025-10-03 06:36:52'),
	(4, 13, 111111.00, 'pending', 'zzzzzzzzzzzzzz', '2025-10-03 06:37:17'),
	(5, 13, 111111.00, 'pending', '11111111111111', '2025-10-03 06:39:15'),
	(6, 13, 111111.00, 'pending', 'zzzzzzzzzzzz', '2025-10-03 06:43:25'),
	(7, 13, 111111.00, 'pending', 'zzzzzzzzzzzz', '2025-10-03 06:44:28'),
	(8, 13, 111111.00, 'pending', 'zzzzzzzzzzzz', '2025-10-03 06:44:48'),
	(9, 13, 111111.00, 'cancelled', 'zzzzzzzzzzzz', '2025-10-03 06:45:00'),
	(10, 13, 111111.00, 'approved', 'zzz', '2025-10-03 06:45:12'),
	(11, 13, 111111.00, 'approved', 'zzz', '2025-10-03 06:45:19'),
	(12, 13, 111111.00, 'cancelled', 'zzz', '2025-10-03 06:49:31'),
	(13, 13, 111111.00, 'approved', 'zzzzzz', '2025-10-03 07:05:28'),
	(14, 13, 111111.00, 'approved', '11111111111', '2025-10-03 07:06:33'),
	(15, 13, 50000.00, 'pending', '11111111111', '2025-10-06 03:36:54'),
	(16, 13, 50000.00, 'cancelled', '11111111111', '2025-10-06 03:38:10'),
	(17, 13, 24000000.00, 'pending', 'ưdsa', '2025-10-06 03:44:02'),
	(18, 13, 850000.00, 'cancelled', 'aaaaaaaaaaaaaaaaaaaa', '2025-10-06 03:45:24'),
	(19, 13, 72100000.00, 'pending', 'sssssssssssss', '2025-10-06 03:49:55'),
	(20, 13, 24000000.00, 'approved', 'sadadasdas', '2025-10-07 03:04:21'),
	(21, 13, 50000.00, 'approved', 'r', '2025-10-08 08:30:32'),
	(22, 13, 23200000.00, 'approved', 'j', '2025-10-14 00:42:05'),
	(23, 20, 36105000.00, 'approved', '1', '2025-10-15 17:34:59'),
	(24, 20, 35000.00, 'approved', 'z', '2025-10-15 18:28:16'),
	(25, 23, 36000000.00, 'pending', '0354242988', '2025-10-15 20:02:21'),
	(26, 23, 13200000.00, 'pending', '0354242988', '2025-10-15 20:03:07'),
	(27, 23, 36000000.00, 'pending', '035424298803', '2025-10-15 20:04:33'),
	(28, 23, 36000000.00, 'pending', '035424298803', '2025-10-15 20:04:41'),
	(29, 23, 35000.00, 'approved', '0354242988003542429880', '2025-10-15 20:05:07'),
	(30, 23, 35000.00, 'approved', 'public function getAllOrders($status = \'pending\', $id = null) {\r\n    if (!!$id) {\r\n        // Query cho một hóa đơn cụ thể với ID\r\n        $sql = "\r\n            SELECT b.*, u.username \r\n            FROM bill b\r\n            JOIN user u ON b.user_id = u.id\r\n            WHERE b.status = ? AND b.id = ?\r\n        ";\r\n        $stmt = $this->conn->prepare($sql);\r\n        $stmt->execute([$status, $id]);\r\n        \r\n        // Lấy kết quả đơn lẻ\r\n        $result = $stmt->fetch(PDO::FETCH_ASSOC);\r\n        \r\n        // Nếu tìm thấy, đặt kết quả vào một mảng để khớp với kiểu trả về của `else`\r\n        if ($result) {\r\n            return [$result];\r\n        } else {\r\n            return []; // Trả về mảng rỗng nếu không tìm thấy\r\n        }\r\n    } else {\r\n        // Query cho tất cả các hóa đơn có cùng trạng thái\r\n        $sql = "\r\n            SELECT b.*, u.username \r\n            FROM bill b\r\n            JOIN user u ON b.user_id = u.id\r\n            WHERE b.status = ?\r\n            ORDER BY b.created_at DESC\r\n        ";\r\n        $stmt = $this->conn->prepare($sql);\r\n        $stmt->execute([$status]);\r\n        \r\n        // Trả về một mảng chứa tất cả các hóa đơn tìm thấy\r\n        return $stmt->fetchAll(PDO::FETCH_ASSOC);\r\n    }\r\n}', '2025-10-15 20:27:25');

-- Dumping structure for table tui_xach.cart
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table tui_xach.cart: ~1 rows (approximately)
DELETE FROM `cart`;
INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
	(12, 13, 16, 1, 35000.00, '2025-10-14 09:33:54'),
	(21, 20, 14, 1, 36000000.00, '2025-10-15 18:54:17'),
	(28, 23, 13, 1, 725000.00, '2025-10-15 20:28:57');

-- Dumping structure for table tui_xach.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bill_id` (`bill_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table tui_xach.orders: ~10 rows (approximately)
DELETE FROM `orders`;
INSERT INTO `orders` (`id`, `bill_id`, `product_id`, `quantity`, `price`) VALUES
	(4, 17, 15, 1, 24000000.00),
	(5, 18, 11, 1, 850000.00),
	(6, 19, 14, 2, 36000000.00),
	(7, 19, 16, 2, 50000.00),
	(8, 20, 15, 1, 24000000.00),
	(9, 21, 16, 1, 50000.00),
	(10, 22, 15, 1, 13200000.00),
	(11, 22, 7, 1, 10000000.00),
	(12, 23, 16, 3, 35000.00),
	(13, 23, 14, 1, 36000000.00),
	(14, 24, 16, 1, 35000.00),
	(15, 25, 14, 1, 36000000.00),
	(16, 26, 15, 1, 13200000.00),
	(17, 27, 14, 1, 36000000.00),
	(18, 29, 16, 1, 35000.00),
	(19, 30, 16, 1, 35000.00);

-- Dumping structure for table tui_xach.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name_product` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` int DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table tui_xach.products: ~9 rows (approximately)
DELETE FROM `products`;
INSERT INTO `products` (`id`, `name_product`, `price`, `discount`, `image`, `description`, `created_at`, `updated_at`) VALUES
	(7, '🌸 Cây Hoa Sứ Bonsai Trang Trí – Hoa Hồng Phớt Nở Rực Rỡ', 10000000.00, 20, 'uploads/1759720327_dieu-khien-viec-ra-hoa-cay-su.gif', 'Mô tả sản phẩm:\r\nCây Hoa Sứ Bonsai có dáng độc đáo, thân phình đẹp tự nhiên, hoa nở màu hồng pha trắng rực rỡ mang lại không gian tươi mới và sang trọng. Thích hợp để trang trí sân vườn, ban công, phòng khách hoặc làm quà tặng phong thủy.\r\n<br>✅ Đặc điểm nổi bật:\r\n– Hoa nở quanh năm, dễ trồng và chăm sóc.\r\n– Phù hợp khí hậu nóng ẩm, rất thích hợp tại Việt Nam.\r\n– Dáng cây bonsai nghệ thuật, mang ý nghĩa may mắn – thịnh vượng.\r\n<br>🌿 Cách chăm sóc:\r\n– Đặt nơi có đủ ánh sáng tự nhiên.\r\n– Tưới nước vừa phải, tránh để ngập úng.\r\n– Bón phân định kỳ để cây ra hoa đẹp và đều.', '2025-10-06 03:12:07', '2025-10-06 03:12:07'),
	(9, '🌼 Chậu Hoa Lan Hồ Điệp Trắng Sang Trọng – Trang Trí & Quà Tặng Cao Cấp', 1200000.00, 10, 'uploads/1759720894_chau-lan-ho-diep-trang-061.jpg', 'Hoa Lan Hồ Điệp Trắng tượng trưng cho sự tinh khiết, sang trọng và thịnh vượng. Chậu lan với nhiều cành hoa nở rực rỡ, phù hợp trang trí phòng khách, văn phòng, khách sạn hoặc làm quà tặng dịp lễ – Tết – khai trương.\r\n<br>✅ Đặc điểm nổi bật:\r\n– Hoa nở đẹp, lâu tàn (có thể kéo dài 1–2 tháng).\r\n– Màu trắng thanh lịch, dễ phối với mọi không gian.\r\n– Chậu cao cấp, bố cục cân đối, mang giá trị thẩm mỹ cao.\r\n<br>🌿 Cách chăm sóc:\r\n– Đặt nơi thoáng mát, tránh ánh nắng trực tiếp.\r\n– Tưới nước 1–2 lần/tuần, tránh ngập úng.\r\n– Có thể dùng bình phun sương giữ ẩm nhẹ cho hoa.', '2025-10-06 03:21:34', '2025-10-06 03:21:34'),
	(10, '🌺 Chậu Hoa Lan Hồ Điệp Đa Sắc 99 Cành – Sang Trọng & Đẳng Cấp', 25000000.00, 2, 'uploads/1759721031_chau-hoa-lan-ho-diep-da-sac-99-canh-ds85.webp', 'Chậu Lan Hồ Điệp đa sắc 99 cành được thiết kế hoành tráng, kết hợp tinh tế giữa sắc trắng, tím, vàng và cam rực rỡ. Đây là lựa chọn hoàn hảo để trang trí không gian lớn như sảnh công ty, khách sạn, trung tâm hội nghị hoặc dùng làm quà tặng cao cấp trong dịp Tết, khai trương, lễ kỷ niệm.\r\n<br>✅ Đặc điểm nổi bật:\r\n– Thiết kế lớn, bố cục cân đối, mang lại vẻ đẹp sang trọng & đẳng cấp.\r\n– 99 cành lan tượng trưng cho sự trường tồn, may mắn & thịnh vượng.\r\n– Màu sắc đa dạng, phối hợp hài hòa và bắt mắt.\r\n<br>🌿 Cách chăm sóc:\r\n– Đặt nơi thoáng mát, tránh nắng gắt trực tiếp.\r\n– Tưới nước 1–2 lần/tuần, tránh úng.\r\n– Giữ ẩm nhẹ bằng bình phun sương để hoa tươi lâu.', '2025-10-06 03:23:51', '2025-10-06 03:23:51'),
	(11, '🌹 Chậu Cây Hoa Đỏ Trang Trí – Sang Trọng & Hiện Đại', 850000.00, 5, 'uploads/1759721115_chau-hoa-ido.jpg', 'Chậu cây hoa đỏ trang trí mang vẻ đẹp rực rỡ, tượng trưng cho sự may mắn và hạnh phúc. Thiết kế chậu hiện đại, phù hợp cho trang trí phòng khách, văn phòng, sảnh lễ tân, quán cà phê hoặc làm quà tặng sang trọng trong các dịp đặc biệt.\r\n✅ Đặc điểm nổi bật:\r\n– Màu hoa đỏ tươi rực rỡ, nổi bật trong không gian.\r\n– Chậu thiết kế cao cấp, phong cách hiện đại.\r\n– Tăng tính thẩm mỹ & phong thủy cho ngôi nhà.\r\n🌿 Cách chăm sóc:\r\n– Đặt nơi có ánh sáng tự nhiên, thoáng mát.\r\n– Tưới nước định kỳ, tránh úng.\r\n– Bón phân nhẹ giúp cây ra hoa đều và đẹp.', '2025-10-06 03:25:15', '2025-10-06 03:25:15'),
	(12, '🌹 Chậu Hoa Hồng Đỏ Mini – Trang Trí Bàn & Quà Tặng Lãng Mạn', 120000.00, 15, 'uploads/1759721187_chau-hoa-hong-do.jpg', 'Chậu hoa hồng đỏ mini mang vẻ đẹp tinh tế, tượng trưng cho tình yêu và sự nồng nhiệt. Thích hợp trang trí bàn làm việc, ban công, cửa sổ hoặc làm quà tặng ý nghĩa trong các dịp lễ như Valentine, 8/3, sinh nhật…\r\n<br>✅ Đặc điểm nổi bật:\r\n– Hoa nở rực rỡ, màu đỏ đậm quyến rũ.\r\n– Kích thước nhỏ gọn, dễ bài trí ở nhiều không gian.\r\n– Thích hợp làm quà tặng lãng mạn & trang trí không gian sống.\r\n<br>🌿 Cách chăm sóc:\r\n– Đặt nơi có ánh sáng nhẹ, thoáng khí.\r\n– Tưới nước đều đặn, tránh ngập úng.\r\n– Tỉa bớt lá hư để cây luôn khỏe mạnh và ra hoa đẹp.', '2025-10-06 03:26:27', '2025-10-06 03:26:27'),
	(13, 'Chậu hoa Địa Lan vàng trang trí Tết', 1450000.00, 50, 'uploads/1759721344_chau-hoa-dia-lan-thuan-loi-tram-be374.jpg', '🌸 Đây là một chậu địa lan vàng được cắm trong bình trắng cao, điểm xuyết thêm hoa đỏ và nơ đỏ tạo cảm giác trang trọng — rất hợp để trưng bày dịp khai trương hoặc Tết.', '2025-10-06 03:29:04', '2025-10-06 03:29:04'),
	(14, 'Chậu hoa Trà đỏ trang trí', 45000000.00, 20, 'uploads/1759721387_268775385-4814092618683413-5639140663482941868-n.webp', '👉 Phù hợp trang trí phòng khách, quán cà phê, văn phòng hoặc làm quà tặng sang trọng.', '2025-10-06 03:29:47', '2025-10-06 03:29:47'),
	(15, 'Chậu hoa hướng dương mini trang trí để bàn 🌻✨', 24000000.00, 45, 'uploads/1759721477_6A832A87-A16C-4EC8-B216-1236199B9E8A-scaled.jpeg', '👉 Thích hợp trang trí bàn làm việc, kệ sách, quán cà phê, góc học tập hoặc làm quà tặng dễ thương.\r\n👉 Mang lại cảm giác tươi sáng, tràn đầy năng lượng tích cực.', '2025-10-06 03:31:17', '2025-10-06 03:31:17'),
	(16, 'Chậu Cây Sen Đá Mini Trang Trí Bàn Làm Việc – Xanh Tươi Tự Nhiên', 50000.00, 30, 'uploads/1759722179_images.png', 'Chậu cây sen đá mini xinh xắn với sắc xanh tươi mát, thích hợp trang trí bàn làm việc, kệ sách, quán cà phê hoặc làm quà tặng nhỏ xinh. Sen đá tượng trưng cho sự bền bỉ và may mắn, mang lại cảm giác thư giãn và gần gũi với thiên nhiên.\r\n✅ Đặc điểm nổi bật:\r\n– Kiểu dáng nhỏ gọn, dễ bày trí.\r\n– Màu xanh mát mắt, không cần chăm sóc nhiều.\r\n– Tạo không gian xanh và sinh động.\r\n🌿 Cách chăm sóc:\r\n– Đặt nơi có ánh sáng nhẹ.\r\n– Tưới ít nước (1–2 lần/tuần).\r\n– Tránh để nơi quá ẩm ướt.', '2025-10-06 03:42:59', '2025-10-06 03:42:59');

-- Dumping structure for table tui_xach.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table tui_xach.user: ~13 rows (approximately)
DELETE FROM `user`;
INSERT INTO `user` (`id`, `username`, `email`, `password`, `address`) VALUES
	(8, 'Dư Bảo Nhàn', 'nguLaoTinh@gmail.com', '$2y$10$TsolsviYRBO5fo1uBmGKJ.3.RWGpV94rTNovhCEdt3rjhQNlOzbSm', 'số 4, ỷ lan'),
	(9, 'Dư Bảo Nhàn', 'haokietkcomaacomas7@gmail.com', '$2y$10$wZIBnphctgJ/3bLVbbKw6.0ls8jldKjDuWI0wXs29pStWIbcDnoXy', 'CDPHP/do-an-cuoi-mon'),
	(10, 'Dư Bảo Nhàn', 'DU1231231@gmail.com', '$2y$10$ALPeFjfINoDTq5M8RAchbeYusFNGPqw04/uSG/KQ/7fSJupqm.Jw2', '123123'),
	(11, 'Dư Bảo Nhàn', 'admin@gmail.com', '$2y$10$SpLGk1GWoZo0pOdmm6xQnes3LlrkqUNdC0AE6GebSHoyotLhMtPLy', '11111111'),
	(12, 'Dư Bảo Nhàn', 'gamo168@gmail.com', '$2y$10$BW/SvALvbOhvMl/2sk/5veSWA0LHD0GTur1HMQT3.kQbmEZmQ5GTK', '/do-an-cuoi-mo'),
	(13, 'Dư Bảo Nhàn', 'gamo123@gmail.com', '$2y$10$NkUEZ0CG1G9ouvhKaiXLO.8foHwtHSD2TTF6UlJ5B3..QECjmxm5G', '/do-an-cuoi-mo'),
	(14, 'gamo15', 'haokietks7@gmail.com', '$2y$10$U3DxnUParcxVHe2xnN4QPefxY0D0y6csVtMQqF/vC6GC/X.yQAc7K', 'aaaaaaa'),
	(15, 'saaaaaa', 'gamo123a@gmail.com', '$2y$10$acR8qViaGM6B7QKRXYkSM.aeybZcxL/8joE1zsMNv.i1rPwrq2WN2', 'aaaaaaaaa'),
	(16, 'Dư Bảo Nhàn', 'gamoa123@gmail.com', '$2y$10$qGAE31.5szRsLEzR0XsV6uq02oe9BcdEnV5tstH.DS815ELwvD0t6', 'z'),
	(17, 'zxzxc', 'gamo12zxc3@gmail.com', '$2y$10$8x67rBBeLSfFNoUlmERc6uylvgoYnoTkCUvvuoEwmMdIZkLooIGQG', '1123132'),
	(18, 'Dư Bảo Nhàn', 'haokietk22s7@gmail.com', '$2y$10$qZ4Tcjs3IQbzVUBUrxq1m.8r/zuEvIuR5YGzjEfX112T90ilWwVcG', 'a'),
	(19, 'Dư Bảo Nhàn', 'gamo1234@gmail.com', '$2y$10$vLiqEKU6LMa4n21Q8Y88..JSLKHxuAxsDffVd1a5ekiPDbADkBoSK', 'gamo1234@gmail.com'),
	(20, 'gamo12345@gmail.com', 'gamo12345@gmail.com', '$2y$10$KaeOh49/sUcTRECj8q5XxeiYGVHyYpIDsqtQhRHMZuvmqTCYeZNbu', 'gamo12345@gmail.com'),
	(21, 'saaaaaa', 'nguLaaoTinh@gmail.com', '$2y$10$5D6LFpOrZvyNAqL0xVZ2mewNGD2Ji5d4y.6ANoLVaAVtcrvkZWANe', 'nguLaaoTinh@gmail.com'),
	(22, 'Dư Bảo Nhàn', 'gamos123@gmail.com', '$2y$10$d4LXCDamW4hJb174ueYw3Oe.K8SmW.xv3IkbIUSo4oqZ9TOcCMOtm', 'gamos123@gmail.com'),
	(23, 'gamao12345@gmail.com', 'gamao12345@gmail.com', '$2y$10$8K6XLTOYmCgGPq5DCtKrzu7syMlUSORscfledVmc1iN86HTzijLwe', 'gamao12345@gmail.com'),
	(24, 'Dư Bảo Nhàn', 'gamo1223@gmail.com', '$2y$10$jXRS.2NaT5SLLd/xByGG7eNtFnL/4V8azyGKmBvg2fNB6tMeC01/S', 'gamo123@gmail.com');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
SET foreign_key_checks = 1;
