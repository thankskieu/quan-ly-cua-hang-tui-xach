-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 11, 2025 lúc 10:54 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `tui_xach`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `administrator`
--

CREATE TABLE `administrator` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(50) DEFAULT 'admin',
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `administrator`
--

INSERT INTO `administrator` (`id`, `email`, `password`, `username`, `role`, `created_at`) VALUES
(1, 'admin@example.com', '$2y$10$CovYwuycMYKtvwOHyR/xfuYcLgrm/5fF5EPAk/cNvND6BO/RiZWku', 'admin', 'admin', '2025-11-11 06:02:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bill`
--

CREATE TABLE `bill` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bill`
--

INSERT INTO `bill` (`id`, `user_id`, `total_price`, `total_amount`, `fullname`, `address`, `phone`, `created_at`, `status`) VALUES
(3, 1, 0.00, 540000.00, 'Xuyên Nguyễn', 'sd', '0000000000', '2025-11-11 05:45:30', 'pending'),
(4, 1, 0.00, 540000.00, 'Xuyên Nguyễn', 'sd', '0000000000', '2025-11-11 05:45:51', 'pending'),
(5, 1, 0.00, 540000.00, '', 'sd', '', '2025-11-11 05:51:29', 'pending'),
(6, 1, 0.00, 540000.00, '', 'sd', '', '2025-11-11 05:55:31', 'pending'),
(7, 1, 540000.00, 0.00, '', 'sdds', '', '2025-11-11 06:01:40', 'pending'),
(8, 1, 2160000.00, 0.00, '', 'd', '', '2025-11-11 06:09:53', 'pending'),
(9, 1, 2160000.00, 0.00, '', 'd', '', '2025-11-11 06:12:56', 'pending'),
(14, 2, 540000.00, 0.00, '', 'sd', '', '2025-11-11 09:07:39', 'pending'),
(15, 2, 0.00, 540000.00, '', 'sd', '', '2025-11-11 09:11:58', 'pending'),
(16, 2, 0.00, 540000.00, '', 'ể', '', '2025-11-11 09:15:30', 'pending'),
(17, 2, 0.00, 540000.00, '', 'ể', '', '2025-11-11 09:15:42', 'pending'),
(18, 2, 1620000.00, 0.00, '', 'sff', '', '2025-11-11 09:20:23', 'pending'),
(19, 2, 540000.00, 0.00, '', 'sd', '', '2025-11-11 09:21:09', 'pending'),
(20, 2, 2160000.00, 0.00, '', 'dggr', '', '2025-11-11 09:21:32', 'pending');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bill_items`
--

CREATE TABLE `bill_items` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bill_items`
--

INSERT INTO `bill_items` (`id`, `bill_id`, `product_id`, `quantity`, `price`) VALUES
(1, 3, 5, 1, 540000.00),
(2, 4, 5, 1, 540000.00),
(3, 5, 5, 1, 540000.00),
(4, 6, 5, 1, 540000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(12,2) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `product_id`, `product_name`, `product_price`, `product_image`, `quantity`, `price`, `total_price`, `user_id`, `created_at`) VALUES
(6, 5, '', 0.00, '', 4, 540000.00, NULL, 1, '2025-11-11 06:05:01'),
(13, 8, '', 0.00, '', 1, 540000.00, NULL, 2, '2025-11-11 09:20:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitiet_hd`
--

CREATE TABLE `chitiet_hd` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoadon`
--

CREATE TABLE `hoadon` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `hoadon`
--

INSERT INTO `hoadon` (`id`, `user_id`, `total_price`, `status`, `address`, `created_at`) VALUES
(1, 1, 100000.00, 'pending', '123 Đường ABC', '2025-11-11 16:46:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`id`, `name`, `email`, `password`) VALUES
(1, 'Test User', 'test@example.com', '123456');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `mathang`
--

CREATE TABLE `mathang` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `mathang`
--

INSERT INTO `mathang` (`id`, `name`, `price`, `image`) VALUES
(1, 'Hoa hồng', 100000.00, 'rose.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `bill_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(1, 7, 42, 1, 540000.00, '2025-11-11 06:01:40'),
(2, 15, 3, 1, 540000.00, '2025-11-11 09:11:58'),
(3, 16, 4, 1, 540000.00, '2025-11-11 09:15:30'),
(4, 18, 3, 2, 540000.00, '2025-11-11 09:20:23'),
(5, 18, 1, 1, 540000.00, '2025-11-11 09:20:23'),
(6, 19, 41, 1, 540000.00, '2025-11-11 09:21:09'),
(7, 20, 32, 4, 540000.00, '2025-11-11 09:21:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name_product` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `discount` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name_product`, `price`, `category_id`, `category`, `quantity`, `discount`, `image`, `description`, `images`, `stock`) VALUES
(1, 'Túi xách C5-Pocket size nhỏ (S) màu XÁM xi măng', 600000.00, 'C5', 'C5-POCKET', 10, 10, 'uploads/z7149035737823_7c2bc34ab30308d999cc4d614a782b98.jpg', '\r\n \r\n\r\nTúi C5-pockets: tên túi được đặt theo chức năng sử dụng của túi. Đây là dòng túi nhiều ngăn (multi-pockets), cụ thể là size S với 5 ngăn của Chautfifth (tên ngắn gọn là C5).\r\n\r\nSự hình dung của thiết kế là một chiếc túi được lấp đầy các ngăn nhỏ xinh xung quanh. Mỗi ngăn là một kiểu ngăn khác nhau từ ngăn dây rút đến ngăn dây kéo hay ngăn nắp đậy. Ngăn túi dây rút giúp túi xinh xinh. Ngăn túi hộp dây kéo của C5 là ngăn rất khó may, chỉ những thợ lành nghề mới được giao cho may ngăn này, càng nhỏ và để ra cái hộp thì càng khó may dù tác dụng chỉ để trang trí và nhìn dễ thương. Các ngăn túi nắp đậy hình dáng cong thành điểm độc đáo khi lần đầu nhìn vào túi.\r\n\r\nDây túi có để điều chỉnh đeo chéo hoặc đeo vai một cách dễ dàng với nhiều kích thước tuỳ chỉnh.\r\n\r\nTúi đựng được ví dài, điện thoại, sổ A6 và nhiều vật dụng khác hàng ngày khác.', '[\"uploads/z7149035737823_7c2bc34ab30308d999cc4d614a782b98.jpg\",\"uploads/z7149035737823_7c2bc34ab30308d999cc4d614a782b98.jpg\"]', 0),
(2, 'Túi xách C5-Pocket size nhỏ (S) màu Beige cát', 600000.00, 'C5', 'C5-POCKET', 5, 10, 'uploads/tuinau.jpg', '\r\n \r\n\r\nTúi C5-pockets: tên túi được đặt theo chức năng sử dụng của túi. Đây là dòng túi nhiều ngăn (multi-pockets), cụ thể là size S với 5 ngăn của Chautfifth (tên ngắn gọn là C5).\r\n\r\nSự hình dung của thiết kế là một chiếc túi được lấp đầy các ngăn nhỏ xinh xung quanh. Mỗi ngăn là một kiểu ngăn khác nhau từ ngăn dây rút đến ngăn dây kéo hay ngăn nắp đậy. Ngăn túi dây rút giúp túi xinh xinh. Ngăn túi hộp dây kéo của C5 là ngăn rất khó may, chỉ những thợ lành nghề mới được giao cho may ngăn này, càng nhỏ và để ra cái hộp thì càng khó may dù tác dụng chỉ để trang trí và nhìn dễ thương. Các ngăn túi nắp đậy hình dáng cong thành điểm độc đáo khi lần đầu nhìn vào túi.\r\n\r\nDây túi có để điều chỉnh đeo chéo hoặc đeo vai một cách dễ dàng với nhiều kích thước tuỳ chỉnh.\r\n\r\nTúi đựng được ví dài, điện thoại, sổ A6 và nhiều vật dụng khác hàng ngày khác.', '[\"uploads/tuinau.jpg\",\"uploads/tuinau.jpg\"]', 0),
(3, 'Túi xách C5-Pocket size nhỏ (S) màu đen', 600000.00, 'C5', 'C5-POCKET', 5, 10, 'uploads/z7149035737826_9e1167b93f973db8d59239e813a1222f.jpg', '\r\nTúi C5-pockets: tên túi được đặt theo chức năng sử dụng của túi. Đây là dòng túi nhiều ngăn (multi-pockets), cụ thể là size S với 5 ngăn của Chautfifth (tên ngắn gọn là C5). \r\n\r\nSự hình dung của thiết kế là một chiếc túi được lấp đầy các ngăn nhỏ xinh xung quanh. Mỗi ngăn là một kiểu ngăn khác nhau từ ngăn dây rút đến ngăn dây kéo hay ngăn nắp đậy. Ngăn túi dây rút giúp túi xinh xinh. Ngăn túi hộp dây kéo của C5 là ngăn rất khó may, chỉ những thợ lành nghề mới được giao cho may ngăn này, càng nhỏ và để ra cái hộp thì càng khó may dù tác dụng chỉ để trang trí và nhìn dễ thương. Các ngăn túi nắp đậy hình dáng cong thành điểm độc đáo khi lần đầu nhìn vào túi.\r\n\r\nDây túi có để điều chỉnh đeo chéo hoặc đeo vai một cách dễ dàng với nhiều kích thước tuỳ chỉnh.\r\n\r\nTúi đựng được ví dài, điện thoại, sổ A6 và nhiều vật dụng khác hàng ngày khác.', '[\"uploads/z7149035737826_9e1167b93f973db8d59239e813a1222f.jpg\",\"uploads/z7149035737826_9e1167b93f973db8d59239e813a1222f.jpg\"]', 0),
(4, 'Túi xách C5-Pocket size nhỏ (S) màu đỏ đô', 600000.00, 'C5', 'C5-POCKET', 5, 10, 'uploads/z7148773768883_fb72e79beb2e489f69997da77cb58534.jpg', '\r\nTúi C5-pockets: tên túi được đặt theo chức năng sử dụng của túi. Đây là dòng túi nhiều ngăn (multi-pockets), cụ thể là size S với 5 ngăn của Chautfifth (tên ngắn gọn là C5).\r\n\r\nSự hình dung của thiết kế là một chiếc túi được lấp đầy các ngăn nhỏ xinh xung quanh. Mỗi ngăn là một kiểu ngăn khác nhau từ ngăn dây rút đến ngăn dây kéo hay ngăn nắp đậy. Ngăn túi dây rút giúp túi xinh xinh. Ngăn túi hộp dây kéo của C5 là ngăn rất khó may, chỉ những thợ lành nghề mới được giao cho may ngăn này, càng nhỏ và để ra cái hộp thì càng khó may dù tác dụng chỉ để trang trí và nhìn dễ thương. Các ngăn túi nắp đậy hình dáng cong thành điểm độc đáo khi lần đầu nhìn vào túi.\r\n\r\nDây túi có để điều chỉnh đeo chéo hoặc đeo vai một cách dễ dàng với nhiều kích thước tuỳ chỉnh.\r\n\r\nTúi đựng được ví dài, điện thoại, sổ A6 và nhiều vật dụng khác hàng ngày khác.', '[\"uploads/z7148773768883_fb72e79beb2e489f69997da77cb58534.jpg\",\"uploads/z7148773768883_fb72e79beb2e489f69997da77cb58534.jpg\"]', 0),
(5, 'Túi xách C5-Pocket size nhỏ (S) màu xanh chuối', 600000.00, 'C5', 'C5-POCKET', 5, 10, 'uploads/z7149035737836_41fdf679c390747c9fe6af234f99abac.jpg', '\r\nTúi C5-pockets: tên túi được đặt theo chức năng sử dụng của túi. Đây là dòng túi nhiều ngăn (multi-pockets), cụ thể là size S với 5 ngăn của Chautfifth (tên ngắn gọn là C5).\r\n\r\nSự hình dung của thiết kế là một chiếc túi được lấp đầy các ngăn nhỏ xinh xung quanh. Mỗi ngăn là một kiểu ngăn khác nhau từ ngăn dây rút đến ngăn dây kéo hay ngăn nắp đậy. Ngăn túi dây rút giúp túi xinh xinh. Ngăn túi hộp dây kéo của C5 là ngăn rất khó may, chỉ những thợ lành nghề mới được giao cho may ngăn này, càng nhỏ và để ra cái hộp thì càng khó may dù tác dụng chỉ để trang trí và nhìn dễ thương. Các ngăn túi nắp đậy hình dáng cong thành điểm độc đáo khi lần đầu nhìn vào túi.\r\n\r\nDây túi có để điều chỉnh đeo chéo hoặc đeo vai một cách dễ dàng với nhiều kích thước tuỳ chỉnh.\r\n\r\nTúi đựng được ví dài, điện thoại, sổ A6 và nhiều vật dụng khác hàng ngày khác.', '[\"uploads/z7149035737836_41fdf679c390747c9fe6af234f99abac.jpg\",\"uploads/z7149035737836_41fdf679c390747c9fe6af234f99abac.jpg\"]', 0),
(6, 'Túi xách Y2K POCKET màu Xanh xi măng\r\nY2K POCKET BAG\r\n', 600000.00, 'Y2K', 'Y2K POCKET', 5, 10, 'uploads/z7147582471464_8340a061d2b00e5368d6bf71ceaf12b1.jpg', '\r\nLấy cảm hứng từ thời kỳ bùng nổ Y2K, chiếc túi mang hơi thở của thập niên 2000, tái hiện hình ảnh những chiếc túi IT-GIRL đình đám theo cách hiện đại và tiện dụng hơn bao giờ hết.\r\n\r\nTừ form túi to bản mềm mại kết hợp chi tiết túi hộp và túi rút ở mặt trước. Dây kéo dài, kim loại ánh vàng cùng khoen C5 O-ring làm điểm nhấn - pha chút nổi loạn và cá tính.\r\n\r\nThiết kế hai khoen mở rộng bên hông cho phép tuỳ chỉnh linh hoạt:\r\nKhi mở khoen, túi đủ sức chứa laptop 13”, sổ tay A4, điện thoại, ví dài, sách và các phụ kiện cần thiết khác.\r\nKhi đóng khoen, túi giữ form gọn gàng – trở thành chiếc túi cỡ vừa hoàn hảo cho những buổi dạo phố hay du lịch nhẹ nhàng, với sức chứa đáng kể nhưng không bị mất form.\r\n\r\nMang trọn tinh thần “Utility meets chic” trong từng chi tiết. Chiếc túi là lựa chọn lý tưởng cho những ngày bận rộn – từ văn phòng đến phố xá, từ những chuyến đi xa đến buổi hẹn bất chợt - Thời thượng, ứng dụng cao , phù hợp với mọi hoạt động xung quanh bạn. \r\nSự kết hợp hoàn hảo giữa công năng và tính thời trang.', '[\"uploads/z7147582471464_8340a061d2b00e5368d6bf71ceaf12b1.jpg\",\"uploads/z7147582471464_8340a061d2b00e5368d6bf71ceaf12b1.jpg\"]', 0),
(7, 'Túi xách Y2K POCKET màu NÂU vân\r\nY2K POCKET BAG\r\n', 600000.00, 'Y2K', 'Y2K POCKET', 5, 10, 'uploads/z7147579477807_c3388e5c7120bc87ea0bd92f510a4329.jpg', '\r\nLấy cảm hứng từ thời kỳ bùng nổ Y2K, chiếc túi mang hơi thở của thập niên 2000, tái hiện hình ảnh những chiếc túi IT-GIRL đình đám theo cách hiện đại và tiện dụng hơn bao giờ hết.\r\n\r\nTừ form túi to bản mềm mại kết hợp chi tiết túi hộp và túi rút ở mặt trước. Dây kéo dài, kim loại ánh vàng cùng khoen C5 O-ring làm điểm nhấn - pha chút nổi loạn và cá tính.\r\n\r\nThiết kế hai khoen mở rộng bên hông cho phép tuỳ chỉnh linh hoạt:\r\nKhi mở khoen, túi đủ sức chứa laptop 13”, sổ tay A4, điện thoại, ví dài, sách và các phụ kiện cần thiết khác.\r\nKhi đóng khoen, túi giữ form gọn gàng – trở thành chiếc túi cỡ vừa hoàn hảo cho những buổi dạo phố hay du lịch nhẹ nhàng, với sức chứa đáng kể nhưng không bị mất form.\r\n\r\nMang trọn tinh thần “Utility meets chic” trong từng chi tiết. Chiếc túi là lựa chọn lý tưởng cho những ngày bận rộn – từ văn phòng đến phố xá, từ những chuyến đi xa đến buổi hẹn bất chợt - Thời thượng, ứng dụng cao , phù hợp với mọi hoạt động xung quanh bạn. \r\nSự kết hợp hoàn hảo giữa công năng và tính thời trang.', '[\"uploads/z7147579477807_c3388e5c7120bc87ea0bd92f510a4329.jpg\",\"uploads/z7147579477807_c3388e5c7120bc87ea0bd92f510a4329.jpg\"]', 0),
(8, 'Túi xách Y2K POCKET màu trắng KEM\r\nY2K POCKET BAG\r\n', 600000.00, 'Y2K', 'Y2K POCKET', 5, 10, 'uploads/z7147583506809_6f1d7ff158f015c84e762bd2106beb7a.jpg', '\r\nLấy cảm hứng từ thời kỳ bùng nổ Y2K, chiếc túi mang hơi thở của thập niên 2000, tái hiện hình ảnh những chiếc túi IT-GIRL đình đám theo cách hiện đại và tiện dụng hơn bao giờ hết.\r\n\r\nTừ form túi to bản mềm mại kết hợp chi tiết túi hộp và túi rút ở mặt trước. Dây kéo dài, kim loại ánh vàng cùng khoen C5 O-ring làm điểm nhấn - pha chút nổi loạn và cá tính.\r\n\r\nThiết kế hai khoen mở rộng bên hông cho phép tuỳ chỉnh linh hoạt:\r\nKhi mở khoen, túi đủ sức chứa laptop 13”, sổ tay A4, điện thoại, ví dài, sách và các phụ kiện cần thiết khác.\r\nKhi đóng khoen, túi giữ form gọn gàng – trở thành chiếc túi cỡ vừa hoàn hảo cho những buổi dạo phố hay du lịch nhẹ nhàng, với sức chứa đáng kể nhưng không bị mất form.\r\n\r\nMang trọn tinh thần “Utility meets chic” trong từng chi tiết. Chiếc túi là lựa chọn lý tưởng cho những ngày bận rộn – từ văn phòng đến phố xá, từ những chuyến đi xa đến buổi hẹn bất chợt - Thời thượng, ứng dụng cao , phù hợp với mọi hoạt động xung quanh bạn. \r\nSự kết hợp hoàn hảo giữa công năng và tính thời trang.', '[\"uploads/z7147583506809_6f1d7ff158f015c84e762bd2106beb7a.jpg\",\"uploads/z7147583506809_6f1d7ff158f015c84e762bd2106beb7a.jpg\"]', 0),
(9, 'Túi xách Y2K POCKET màu ĐỎ đô\r\nY2K POCKET BAG\r\n', 600000.00, 'Y2K', 'Y2K POCKET', 5, 10, 'uploads/z7148994895388_88bf3e7914c9c0cf517319580938a1eb.jpg', '\r\nLấy cảm hứng từ thời kỳ bùng nổ Y2K, chiếc túi mang hơi thở của thập niên 2000, tái hiện hình ảnh những chiếc túi IT-GIRL đình đám theo cách hiện đại và tiện dụng hơn bao giờ hết.\r\n\r\nTừ form túi to bản mềm mại kết hợp chi tiết túi hộp và túi rút ở mặt trước. Dây kéo dài, kim loại ánh vàng cùng khoen C5 O-ring làm điểm nhấn - pha chút nổi loạn và cá tính.\r\n\r\nThiết kế hai khoen mở rộng bên hông cho phép tuỳ chỉnh linh hoạt:\r\nKhi mở khoen, túi đủ sức chứa laptop 13”, sổ tay A4, điện thoại, ví dài, sách và các phụ kiện cần thiết khác.\r\nKhi đóng khoen, túi giữ form gọn gàng – trở thành chiếc túi cỡ vừa hoàn hảo cho những buổi dạo phố hay du lịch nhẹ nhàng, với sức chứa đáng kể nhưng không bị mất form.\r\n\r\nMang trọn tinh thần “Utility meets chic” trong từng chi tiết. Chiếc túi là lựa chọn lý tưởng cho những ngày bận rộn – từ văn phòng đến phố xá, từ những chuyến đi xa đến buổi hẹn bất chợt - Thời thượng, ứng dụng cao , phù hợp với mọi hoạt động xung quanh bạn. \r\nSự kết hợp hoàn hảo giữa công năng và tính thời trang.', '[\"uploads/z7148994895388_88bf3e7914c9c0cf517319580938a1eb.jpg\",\"uploads/z7148994895388_88bf3e7914c9c0cf517319580938a1eb.jpg\"]', 0),
(10, 'Túi xách Y2K POCKET màu ĐỎ đô\r\nY2K POCKET BAG\r\n', 600000.00, 'Y2K', 'Y2K POCKET', 5, 10, 'uploads/z7148994895396_8dd55c53d050ea3a79981b815337d973.jpg', '\r\nLấy cảm hứng từ thời kỳ bùng nổ Y2K, chiếc túi mang hơi thở của thập niên 2000, tái hiện hình ảnh những chiếc túi IT-GIRL đình đám theo cách hiện đại và tiện dụng hơn bao giờ hết.\r\n\r\nTừ form túi to bản mềm mại kết hợp chi tiết túi hộp và túi rút ở mặt trước. Dây kéo dài, kim loại ánh vàng cùng khoen C5 O-ring làm điểm nhấn - pha chút nổi loạn và cá tính.\r\n\r\nThiết kế hai khoen mở rộng bên hông cho phép tuỳ chỉnh linh hoạt:\r\nKhi mở khoen, túi đủ sức chứa laptop 13”, sổ tay A4, điện thoại, ví dài, sách và các phụ kiện cần thiết khác.\r\nKhi đóng khoen, túi giữ form gọn gàng – trở thành chiếc túi cỡ vừa hoàn hảo cho những buổi dạo phố hay du lịch nhẹ nhàng, với sức chứa đáng kể nhưng không bị mất form.\r\n\r\nMang trọn tinh thần “Utility meets chic” trong từng chi tiết. Chiếc túi là lựa chọn lý tưởng cho những ngày bận rộn – từ văn phòng đến phố xá, từ những chuyến đi xa đến buổi hẹn bất chợt - Thời thượng, ứng dụng cao , phù hợp với mọi hoạt động xung quanh bạn. \r\nSự kết hợp hoàn hảo giữa công năng và tính thời trang.', '[\"uploads/z7148994895396_8dd55c53d050ea3a79981b815337d973.jpg\",\"uploads/z7148994895396_8dd55c53d050ea3a79981b815337d973.jpg\"]', 0),
(11, 'Túi xách CRESCENT MOON màu Nâu Beige vân cá sấu\r\nCRESCENT MOON BAG\r\n', 600000.00, 'CRE', 'CRESCENT MOON', 5, 10, 'uploads/z7149022463947_fc1fb2bffb7285f5b6d6947280c6d97a.jpg', 'Túi xách CRESCENT MOON màu Nâu Beige vân cá sấu\r\nCRESCENT MOON BAG\r\n\r\nLấy cảm hứng từ vẻ đẹp mất cân đối đầy cuốn hút của mặt trăng lưỡi liềm, chiếc túi này tôn vinh sự bất đối xứng một cách tinh tế nhưng đầy ấn tượng.\r\nĐiểm nhấn nằm ở phần đáy túi, nơi hai ngăn dây kéo hai bên không chỉ tạo nên sự tiện dụng mà còn mang lại nét thú vị cho thiết kế. Thay vì dáng đáy tròn quen thuộc, thiết kế hai ngăn đáy tạo nên một hình dáng túi độc đáo, khác biệt.\r\nĐây là một chiếc túi đa năng có thể đeo chéo, dài vai, ngắn vai, hoặc đeo chéo linh hoạt. Phom dáng linh hoạt giúp chiếc túi dễ dàng hòa vào nhiều phong cách khác nhau.', '[\"uploads/z7149022463947_fc1fb2bffb7285f5b6d6947280c6d97a.jpg\",\"uploads/z7149022463947_fc1fb2bffb7285f5b6d6947280c6d97a.jpg\"]', 0),
(12, 'Túi xách CRESCENT MOON màu XÁM xi măng\r\nCRESCENT MOON BAG\r\n', 600000.00, 'CRE', 'CRESCENT MOON', 5, 10, 'uploads/z7149022453698_0c2d3ab562625dfb59878851aa4e2e84.jpg', 'Túi xách CRESCENT MOON màu Nâu Beige vân cá sấu\r\nCRESCENT MOON BAG\r\n\r\nLấy cảm hứng từ vẻ đẹp mất cân đối đầy cuốn hút của mặt trăng lưỡi liềm, chiếc túi này tôn vinh sự bất đối xứng một cách tinh tế nhưng đầy ấn tượng.\r\nĐiểm nhấn nằm ở phần đáy túi, nơi hai ngăn dây kéo hai bên không chỉ tạo nên sự tiện dụng mà còn mang lại nét thú vị cho thiết kế. Thay vì dáng đáy tròn quen thuộc, thiết kế hai ngăn đáy tạo nên một hình dáng túi độc đáo, khác biệt.\r\nĐây là một chiếc túi đa năng có thể đeo chéo, dài vai, ngắn vai, hoặc đeo chéo linh hoạt. Phom dáng linh hoạt giúp chiếc túi dễ dàng hòa vào nhiều phong cách khác nhau.', '[\"uploads/z7149022453698_0c2d3ab562625dfb59878851aa4e2e84.jpg\",\"uploads/z7149022453698_0c2d3ab562625dfb59878851aa4e2e84.jpg\"]', 0),
(13, 'Túi xách CRESCENT MOON màu nâu\r\nCRESCENT MOON BAG\r\n', 600000.00, 'CRE', 'CRESCENT MOON', 5, 10, 'uploads/z7149022463948_5f11f8391e8775c57cf2e7de65523380.jpg', 'Túi xách CRESCENT MOON màu Nâu Beige vân cá sấu\r\nCRESCENT MOON BAG\r\n\r\nLấy cảm hứng từ vẻ đẹp mất cân đối đầy cuốn hút của mặt trăng lưỡi liềm, chiếc túi này tôn vinh sự bất đối xứng một cách tinh tế nhưng đầy ấn tượng.\r\nĐiểm nhấn nằm ở phần đáy túi, nơi hai ngăn dây kéo hai bên không chỉ tạo nên sự tiện dụng mà còn mang lại nét thú vị cho thiết kế. Thay vì dáng đáy tròn quen thuộc, thiết kế hai ngăn đáy tạo nên một hình dáng túi độc đáo, khác biệt.\r\nĐây là một chiếc túi đa năng có thể đeo chéo, dài vai, ngắn vai, hoặc đeo chéo linh hoạt. Phom dáng linh hoạt giúp chiếc túi dễ dàng hòa vào nhiều phong cách khác nhau.', '[\"uploads/z7149022463948_5f11f8391e8775c57cf2e7de65523380.jpg\",\"uploads/z7149022463948_5f11f8391e8775c57cf2e7de65523380.jpg\"]', 0),
(14, 'Túi xách CRESCENT MOON màu KEM\r\nCRESCENT MOON BAG\r\n', 600000.00, 'CRE', 'CRESCENT MOON', 5, 10, 'uploads/z7149022453696_7311b636495b4a487ad489ddfab0662b.jpg', 'Túi xách CRESCENT MOON màu Nâu Beige vân cá sấu\r\nCRESCENT MOON BAG\r\n\r\nLấy cảm hứng từ vẻ đẹp mất cân đối đầy cuốn hút của mặt trăng lưỡi liềm, chiếc túi này tôn vinh sự bất đối xứng một cách tinh tế nhưng đầy ấn tượng.\r\nĐiểm nhấn nằm ở phần đáy túi, nơi hai ngăn dây kéo hai bên không chỉ tạo nên sự tiện dụng mà còn mang lại nét thú vị cho thiết kế. Thay vì dáng đáy tròn quen thuộc, thiết kế hai ngăn đáy tạo nên một hình dáng túi độc đáo, khác biệt.\r\nĐây là một chiếc túi đa năng có thể đeo chéo, dài vai, ngắn vai, hoặc đeo chéo linh hoạt. Phom dáng linh hoạt giúp chiếc túi dễ dàng hòa vào nhiều phong cách khác nhau.', '[\"uploads/z7149022453696_7311b636495b4a487ad489ddfab0662b.jpg\",\"uploads/z7149022453696_7311b636495b4a487ad489ddfab0662b.jpg\"]', 0),
(15, 'Túi xách CRESCENT MOON màu đỏ ĐÔ\r\nCRESCENT MOON BAG\r\n', 600000.00, 'CRE', 'CRESCENT MOON', 5, 10, 'uploads/z7149022463964_521f6342ee2fbc7d0070ce9f09b5492a.jpg', 'Túi xách CRESCENT MOON màu Nâu Beige vân cá sấu\r\nCRESCENT MOON BAG\r\n\r\nLấy cảm hứng từ vẻ đẹp mất cân đối đầy cuốn hút của mặt trăng lưỡi liềm, chiếc túi này tôn vinh sự bất đối xứng một cách tinh tế nhưng đầy ấn tượng.\r\nĐiểm nhấn nằm ở phần đáy túi, nơi hai ngăn dây kéo hai bên không chỉ tạo nên sự tiện dụng mà còn mang lại nét thú vị cho thiết kế. Thay vì dáng đáy tròn quen thuộc, thiết kế hai ngăn đáy tạo nên một hình dáng túi độc đáo, khác biệt.\r\nĐây là một chiếc túi đa năng có thể đeo chéo, dài vai, ngắn vai, hoặc đeo chéo linh hoạt. Phom dáng linh hoạt giúp chiếc túi dễ dàng hòa vào nhiều phong cách khác nhau.', '[\"uploads/z7149022463964_521f6342ee2fbc7d0070ce9f09b5492a.jpg\",\"uploads/z7149022463964_521f6342ee2fbc7d0070ce9f09b5492a.jpg\"]', 0),
(16, 'Túi xách SPORTIE size nhỏ (S) màu Đỏ đô\r\nTÚI SPORTIE', 600000.00, 'SP', 'SPORTIE', 5, 10, 'uploads/z7148788559948_903671e00b95a61925a95fa186f50a9f.jpg', 'Túi xách SPORTIE size nhỏ (S) màu Đỏ đô\r\nTÚI SPORTIE\r\n\r\nTúi Sportie với form dáng chuẩn của một chiếc túi Shopping Bag. Thiết kế lấy cảm hứng từ dây giày thể thao như một trong cảm hứng chủ đạo Chautfifth ứng dụng vào ví, giày, túi. Do đó, túi được đặt tên Sportie gợi nhớ ngay hình ảnh dây giày thể thao được cột giữa hai thiết kế lưỡi gà phá cách, sáng tạo; ẩn sau là đường cong ngăn dây kéo tạo thêm một ngăn bên ngoài với không gian chứa đồ đáng kể. Quai cầm tay với thiết kế cứng, bo tròn thoạt nhìn như đôi mắt phối với mũi miệng là hoạ tiết dây giày x lưỡi gà tăng tính thú vị cho chiếc túi.', '[\"uploads/z7148788559948_903671e00b95a61925a95fa186f50a9f.jpg\",\"uploads/z7148788559948_903671e00b95a61925a95fa186f50a9f.jpg\"]', 0),
(29, 'Túi xách SPORTIE size nhỏ (S) màu KEM\r\nTÚI SPORTIE', 600000.00, 'SP', 'SPORTIE', 5, 10, 'uploads/z7148788584163_f0188a87e25fd6ab550646c1761e6cb0.jpg', 'Túi xách SPORTIE size nhỏ (S) màu Đỏ đô\r\nTÚI SPORTIE\r\n\r\nTúi Sportie với form dáng chuẩn của một chiếc túi Shopping Bag. Thiết kế lấy cảm hứng từ dây giày thể thao như một trong cảm hứng chủ đạo Chautfifth ứng dụng vào ví, giày, túi. Do đó, túi được đặt tên Sportie gợi nhớ ngay hình ảnh dây giày thể thao được cột giữa hai thiết kế lưỡi gà phá cách, sáng tạo; ẩn sau là đường cong ngăn dây kéo tạo thêm một ngăn bên ngoài với không gian chứa đồ đáng kể. Quai cầm tay với thiết kế cứng, bo tròn thoạt nhìn như đôi mắt phối với mũi miệng là hoạ tiết dây giày x lưỡi gà tăng tính thú vị cho chiếc túi.', '[\"uploads/z7148788584163_f0188a87e25fd6ab550646c1761e6cb0.jpg\",\"uploads/z7148788584163_f0188a87e25fd6ab550646c1761e6cb0.jpg\"]', 0),
(30, 'Túi xách SPORTIE size nhỏ (S) màu NÂU\r\nTÚI SPORTIE', 600000.00, 'SP', 'SPORTIE', 5, 10, 'uploads/z7148788559898_cec3b090b9bb4b79424433be136f05b4.jpg', 'Túi xách SPORTIE size nhỏ (S) màu Đỏ đô\r\nTÚI SPORTIE\r\n\r\nTúi Sportie với form dáng chuẩn của một chiếc túi Shopping Bag. Thiết kế lấy cảm hứng từ dây giày thể thao như một trong cảm hứng chủ đạo Chautfifth ứng dụng vào ví, giày, túi. Do đó, túi được đặt tên Sportie gợi nhớ ngay hình ảnh dây giày thể thao được cột giữa hai thiết kế lưỡi gà phá cách, sáng tạo; ẩn sau là đường cong ngăn dây kéo tạo thêm một ngăn bên ngoài với không gian chứa đồ đáng kể. Quai cầm tay với thiết kế cứng, bo tròn thoạt nhìn như đôi mắt phối với mũi miệng là hoạ tiết dây giày x lưỡi gà tăng tính thú vị cho chiếc túi.', '[\"uploads/z7148788559898_cec3b090b9bb4b79424433be136f05b4.jpg\",\"uploads/z7148788559898_cec3b090b9bb4b79424433be136f05b4.jpg\"]', 0),
(31, 'Túi xách SPORTIE size nhỏ (S) màu XÁM xi măng\r\nTÚI SPORTIE', 600000.00, 'SP', 'SPORTIE', 5, 10, 'uploads/z7148788653433_ca6693df730731885834c254f237d96e.jpg', 'Túi xách SPORTIE size nhỏ (S) màu Đỏ đô\r\nTÚI SPORTIE\r\n\r\nTúi Sportie với form dáng chuẩn của một chiếc túi Shopping Bag. Thiết kế lấy cảm hứng từ dây giày thể thao như một trong cảm hứng chủ đạo Chautfifth ứng dụng vào ví, giày, túi. Do đó, túi được đặt tên Sportie gợi nhớ ngay hình ảnh dây giày thể thao được cột giữa hai thiết kế lưỡi gà phá cách, sáng tạo; ẩn sau là đường cong ngăn dây kéo tạo thêm một ngăn bên ngoài với không gian chứa đồ đáng kể. Quai cầm tay với thiết kế cứng, bo tròn thoạt nhìn như đôi mắt phối với mũi miệng là hoạ tiết dây giày x lưỡi gà tăng tính thú vị cho chiếc túi.', '[\"uploads/z7148788653433_ca6693df730731885834c254f237d96e.jpg\",\"uploads/z7148788653433_ca6693df730731885834c254f237d96e.jpg\"]', 0),
(32, 'Túi xách SPORTIE size nhỏ (S) màu ĐEN\r\nTÚI SPORTIE', 600000.00, 'SP', 'SPORTIE', 5, 10, 'uploads/z7148788722988_6321a339261c185423cb07ee06a40995.jpg', '\r\n\r\nTúi Sportie với form dáng chuẩn của một chiếc túi Shopping Bag. Thiết kế lấy cảm hứng từ dây giày thể thao như một trong cảm hứng chủ đạo Chautfifth ứng dụng vào ví, giày, túi. Do đó, túi được đặt tên Sportie gợi nhớ ngay hình ảnh dây giày thể thao được cột giữa hai thiết kế lưỡi gà phá cách, sáng tạo; ẩn sau là đường cong ngăn dây kéo tạo thêm một ngăn bên ngoài với không gian chứa đồ đáng kể. Quai cầm tay với thiết kế cứng, bo tròn thoạt nhìn như đôi mắt phối với mũi miệng là hoạ tiết dây giày x lưỡi gà tăng tính thú vị cho chiếc túi.', '[\"uploads/z7148788722988_6321a339261c185423cb07ee06a40995.jpg\",\"uploads/z7148788722988_6321a339261c185423cb07ee06a40995.jpg\"]', 0),
(33, 'Túi xách KAMELEON màu ĐEN\r\nKAMELEON\r\n', 600000.00, 'KA', 'KAMELEON', 5, 10, 'uploads/z7149018566189_219522ef45730675e1387477dad65314.jpg', '\r\nThoạt nhìn, chỉ là một hình khối chữ nhật giản đơn. Nhưng KAMELEON – lấy cảm hứng từ loài tắc kè hoa (Chameleon) – lại mang trong mình khả năng biến hóa bất ngờ.\r\n\r\nMột chiếc túi tối giản, KAMELEON mang đến trải nghiệm thời trang tinh gọn, được thiết kế với sức chứa thông minh, gọn gàng nhưng vẫn thoải mái cho những món đồ cần thiết hằng ngày. Cùng với đó là bộ sưu tập quai đeo đa dạng – từ tối giản đến cá tính – cho phép nàng linh hoạt thay đổi phong cách chỉ trong vài giây.\r\n\r\nKAMELEON luôn mang đến những sắc thái mới mẻ, đầy biến hoá.\r\n\r\nGiản đơn, nhưng đa diện. \r\n\r\nĐổi mood – chỉ cần thay dây.\r\n\r\nMinimal x Maximal ', '[\"uploads/z7149018566189_219522ef45730675e1387477dad65314.jpg\",\"uploads/z7149018566189_219522ef45730675e1387477dad65314.jpg\"]', 0),
(34, 'Túi xách KAMELEON màu ĐỎ\r\nKAMELEON\r\n', 600000.00, 'KA', 'KAMELEON', 5, 10, 'uploads/z7149019554730_3ba2b68e45cb75d19aa7bccd867a70ed.jpG', '\r\nThoạt nhìn, chỉ là một hình khối chữ nhật giản đơn. Nhưng KAMELEON – lấy cảm hứng từ loài tắc kè hoa (Chameleon) – lại mang trong mình khả năng biến hóa bất ngờ.\r\n\r\nMột chiếc túi tối giản, KAMELEON mang đến trải nghiệm thời trang tinh gọn, được thiết kế với sức chứa thông minh, gọn gàng nhưng vẫn thoải mái cho những món đồ cần thiết hằng ngày. Cùng với đó là bộ sưu tập quai đeo đa dạng – từ tối giản đến cá tính – cho phép nàng linh hoạt thay đổi phong cách chỉ trong vài giây.\r\n\r\nKAMELEON luôn mang đến những sắc thái mới mẻ, đầy biến hoá.\r\n\r\nGiản đơn, nhưng đa diện. \r\n\r\nĐổi mood – chỉ cần thay dây.\r\n\r\nMinimal x Maximal ', '[\"uploads/z7149019554730_3ba2b68e45cb75d19aa7bccd867a70ed.jpG\",\"uploads/z7149019554730_3ba2b68e45cb75d19aa7bccd867a70ed.jpG\"]', 0),
(35, 'Túi xách KAMELEON màu NÂU\r\nKAMELEON\r\n', 600000.00, 'KA', 'KAMELEON', 5, 10, 'uploads/z7148783937726_c3bb76ac8938b9328dd150e46ad0fb78.jpg', '\r\nThoạt nhìn, chỉ là một hình khối chữ nhật giản đơn. Nhưng KAMELEON – lấy cảm hứng từ loài tắc kè hoa (Chameleon) – lại mang trong mình khả năng biến hóa bất ngờ.\r\n\r\nMột chiếc túi tối giản, KAMELEON mang đến trải nghiệm thời trang tinh gọn, được thiết kế với sức chứa thông minh, gọn gàng nhưng vẫn thoải mái cho những món đồ cần thiết hằng ngày. Cùng với đó là bộ sưu tập quai đeo đa dạng – từ tối giản đến cá tính – cho phép nàng linh hoạt thay đổi phong cách chỉ trong vài giây.\r\n\r\nKAMELEON luôn mang đến những sắc thái mới mẻ, đầy biến hoá.\r\n\r\nGiản đơn, nhưng đa diện. \r\n\r\nĐổi mood – chỉ cần thay dây.\r\n\r\nMinimal x Maximal ', '[\"uploads/z7148783937726_c3bb76ac8938b9328dd150e46ad0fb78.jpg\",\"uploads/z7148783937726_c3bb76ac8938b9328dd150e46ad0fb78.jpg\"]', 0),
(36, 'Túi xách KAMELEON màu KEM\r\nKAMELEON\r\n', 600000.00, 'KA', 'KAMELEON', 5, 10, 'uploads/z7148783913347_458b2a3bdd406c0c8185443d0c6c2be4.jpg', '\r\nThoạt nhìn, chỉ là một hình khối chữ nhật giản đơn. Nhưng KAMELEON – lấy cảm hứng từ loài tắc kè hoa (Chameleon) – lại mang trong mình khả năng biến hóa bất ngờ.\r\n\r\nMột chiếc túi tối giản, KAMELEON mang đến trải nghiệm thời trang tinh gọn, được thiết kế với sức chứa thông minh, gọn gàng nhưng vẫn thoải mái cho những món đồ cần thiết hằng ngày. Cùng với đó là bộ sưu tập quai đeo đa dạng – từ tối giản đến cá tính – cho phép nàng linh hoạt thay đổi phong cách chỉ trong vài giây.\r\n\r\nKAMELEON luôn mang đến những sắc thái mới mẻ, đầy biến hoá.\r\n\r\nGiản đơn, nhưng đa diện. \r\n\r\nĐổi mood – chỉ cần thay dây.\r\n\r\nMinimal x Maximal ', '[\"uploads/z7148783913347_458b2a3bdd406c0c8185443d0c6c2be4.jpg\",\"uploads/z7148783913347_458b2a3bdd406c0c8185443d0c6c2be4.jpg\"]', 0),
(37, 'Túi xách KAMELEON màu NÂU vâ ngựa\r\nKAMELEON\r\n', 600000.00, 'KA', 'KAMELEON', 5, 10, 'uploads/z7148783962975_db1e87ca8bcb58c41727ec2a76290412.jpg', '\r\nThoạt nhìn, chỉ là một hình khối chữ nhật giản đơn. Nhưng KAMELEON – lấy cảm hứng từ loài tắc kè hoa (Chameleon) – lại mang trong mình khả năng biến hóa bất ngờ.\r\n\r\nMột chiếc túi tối giản, KAMELEON mang đến trải nghiệm thời trang tinh gọn, được thiết kế với sức chứa thông minh, gọn gàng nhưng vẫn thoải mái cho những món đồ cần thiết hằng ngày. Cùng với đó là bộ sưu tập quai đeo đa dạng – từ tối giản đến cá tính – cho phép nàng linh hoạt thay đổi phong cách chỉ trong vài giây.\r\n\r\nKAMELEON luôn mang đến những sắc thái mới mẻ, đầy biến hoá.\r\n\r\nGiản đơn, nhưng đa diện. \r\n\r\nĐổi mood – chỉ cần thay dây.\r\n\r\nMinimal x Maximal ', '[\"uploads/z7148783962975_db1e87ca8bcb58c41727ec2a76290412.jpg\",\"uploads/z7148783962975_db1e87ca8bcb58c41727ec2a76290412.jpg\"]', 0),
(38, 'Túi xách DEMI màu Denim', 600000.00, 'DE', 'DEMI', 5, 10, 'uploads/z7148783820459_88fd4c5b3d182e169c537e50308be5d2.jpg', '\r\nDEMI BAG: Half the shape, Full of style!\r\n\r\nTúi DEMI mang tinh thần tối giản nhưng không hề nhạt với thiết kế form bán nguyệt mềm mại, nắp gập nhẹ nhàng và các đường bo tròn tinh tế. \r\n\r\nVừa vặn để mang theo những món đồ thiết yếu: ví dài, điện thoại và phụ kiện cơ bản khác\r\n\r\nSử dụng màu sắc trung tính, hòa phối được nhiều phong cách – từ tối giản, thanh lịch đến năng động thường ngày hay cả những buổi tiệc tùng.\r\n\r\nĐiểm nhấn nằm ở quai đeo tháo rời – dễ dàng thay đổi kiểu quai theo tâm trạng và outfit. Bên cạnh dây cơ bản của túi, bạn có thể dễ dàng sở hữu thêm phiên bản dây WRINKLE để thay đổi cho từng dịp sử dụng khác nhau.\r\n\r\nThay quai – thay vibe!\r\n\r\nMỗi ngày, chỉ với một chiếc túi, bạn có thể tạo nên một phiên bản phong cách hoàn toàn mới.\r\n\r\nDEMI không cần quá nhiều chi tiết để nổi bật – chỉ cần “một nửa vừa đủ” là đủ để bạn thể hiện chính mình.', '[\"uploads/z7148783820459_88fd4c5b3d182e169c537e50308be5d2.jpg\",\"uploads/z7148783820459_88fd4c5b3d182e169c537e50308be5d2.jpg\"]', 0),
(39, 'Túi xách DEMI màu ĐEN', 600000.00, 'DE', 'DEMI', 5, 10, 'uploads/z7148783794930_816df764612f1cb0b557370fae63cc2c.jpg', '\r\nDEMI BAG: Half the shape, Full of style!\r\n\r\nTúi DEMI mang tinh thần tối giản nhưng không hề nhạt với thiết kế form bán nguyệt mềm mại, nắp gập nhẹ nhàng và các đường bo tròn tinh tế. \r\n\r\nVừa vặn để mang theo những món đồ thiết yếu: ví dài, điện thoại và phụ kiện cơ bản khác\r\n\r\nSử dụng màu sắc trung tính, hòa phối được nhiều phong cách – từ tối giản, thanh lịch đến năng động thường ngày hay cả những buổi tiệc tùng.\r\n\r\nĐiểm nhấn nằm ở quai đeo tháo rời – dễ dàng thay đổi kiểu quai theo tâm trạng và outfit. Bên cạnh dây cơ bản của túi, bạn có thể dễ dàng sở hữu thêm phiên bản dây WRINKLE để thay đổi cho từng dịp sử dụng khác nhau.\r\n\r\nThay quai – thay vibe!\r\n\r\nMỗi ngày, chỉ với một chiếc túi, bạn có thể tạo nên một phiên bản phong cách hoàn toàn mới.\r\n\r\nDEMI không cần quá nhiều chi tiết để nổi bật – chỉ cần “một nửa vừa đủ” là đủ để bạn thể hiện chính mình.', '[\"uploads/z7148783794930_816df764612f1cb0b557370fae63cc2c.jpg\",\"uploads/z7148783794930_816df764612f1cb0b557370fae63cc2c.jpg\"]', 0),
(40, 'Túi xách DEMI màu NÂU vân ngựa', 600000.00, 'DE', 'DEMI', 5, 10, 'uploads/z7148783887332_9a0d3628bc065174bf3e4e1f1731c00f.jpg', '\r\nDEMI BAG: Half the shape, Full of style!\r\n\r\nTúi DEMI mang tinh thần tối giản nhưng không hề nhạt với thiết kế form bán nguyệt mềm mại, nắp gập nhẹ nhàng và các đường bo tròn tinh tế. \r\n\r\nVừa vặn để mang theo những món đồ thiết yếu: ví dài, điện thoại và phụ kiện cơ bản khác\r\n\r\nSử dụng màu sắc trung tính, hòa phối được nhiều phong cách – từ tối giản, thanh lịch đến năng động thường ngày hay cả những buổi tiệc tùng.\r\n\r\nĐiểm nhấn nằm ở quai đeo tháo rời – dễ dàng thay đổi kiểu quai theo tâm trạng và outfit. Bên cạnh dây cơ bản của túi, bạn có thể dễ dàng sở hữu thêm phiên bản dây WRINKLE để thay đổi cho từng dịp sử dụng khác nhau.\r\n\r\nThay quai – thay vibe!\r\n\r\nMỗi ngày, chỉ với một chiếc túi, bạn có thể tạo nên một phiên bản phong cách hoàn toàn mới.\r\n\r\nDEMI không cần quá nhiều chi tiết để nổi bật – chỉ cần “một nửa vừa đủ” là đủ để bạn thể hiện chính mình.', '[\"uploads/z7148783887332_9a0d3628bc065174bf3e4e1f1731c00f.jpg\",\"uploads/z7148783887332_9a0d3628bc065174bf3e4e1f1731c00f.jpg\"]', 0),
(41, 'Túi xách DEMI màu KEM', 600000.00, 'DE', 'DEMI', 5, 10, 'uploads/z7148987744166_205d453ddfd227f08154a6ab03dd95f2.jpg', '\r\nDEMI BAG: Half the shape, Full of style!\r\n\r\nTúi DEMI mang tinh thần tối giản nhưng không hề nhạt với thiết kế form bán nguyệt mềm mại, nắp gập nhẹ nhàng và các đường bo tròn tinh tế. \r\n\r\nVừa vặn để mang theo những món đồ thiết yếu: ví dài, điện thoại và phụ kiện cơ bản khác\r\n\r\nSử dụng màu sắc trung tính, hòa phối được nhiều phong cách – từ tối giản, thanh lịch đến năng động thường ngày hay cả những buổi tiệc tùng.\r\n\r\nĐiểm nhấn nằm ở quai đeo tháo rời – dễ dàng thay đổi kiểu quai theo tâm trạng và outfit. Bên cạnh dây cơ bản của túi, bạn có thể dễ dàng sở hữu thêm phiên bản dây WRINKLE để thay đổi cho từng dịp sử dụng khác nhau.\r\n\r\nThay quai – thay vibe!\r\n\r\nMỗi ngày, chỉ với một chiếc túi, bạn có thể tạo nên một phiên bản phong cách hoàn toàn mới.\r\n\r\nDEMI không cần quá nhiều chi tiết để nổi bật – chỉ cần “một nửa vừa đủ” là đủ để bạn thể hiện chính mình.', '[\"uploads/z7148987744166_205d453ddfd227f08154a6ab03dd95f2.jpg\",\"uploads/z7148987744166_205d453ddfd227f08154a6ab03dd95f2.jpg\"]', 0),
(42, 'Túi xách DEMI màu ĐỎ đô', 600000.00, 'DE', 'DEMI', 5, 10, 'uploads/z7149001426170_eb83ef3eb1a3ae6285c13f66295404a3.jpg', '\r\nDEMI BAG: Half the shape, Full of style!\r\n\r\nTúi DEMI mang tinh thần tối giản nhưng không hề nhạt với thiết kế form bán nguyệt mềm mại, nắp gập nhẹ nhàng và các đường bo tròn tinh tế. \r\n\r\nVừa vặn để mang theo những món đồ thiết yếu: ví dài, điện thoại và phụ kiện cơ bản khác\r\n\r\nSử dụng màu sắc trung tính, hòa phối được nhiều phong cách – từ tối giản, thanh lịch đến năng động thường ngày hay cả những buổi tiệc tùng.\r\n\r\nĐiểm nhấn nằm ở quai đeo tháo rời – dễ dàng thay đổi kiểu quai theo tâm trạng và outfit. Bên cạnh dây cơ bản của túi, bạn có thể dễ dàng sở hữu thêm phiên bản dây WRINKLE để thay đổi cho từng dịp sử dụng khác nhau.\r\n\r\nThay quai – thay vibe!\r\n\r\nMỗi ngày, chỉ với một chiếc túi, bạn có thể tạo nên một phiên bản phong cách hoàn toàn mới.\r\n\r\nDEMI không cần quá nhiều chi tiết để nổi bật – chỉ cần “một nửa vừa đủ” là đủ để bạn thể hiện chính mình.', '[\"uploads/z7149001426170_eb83ef3eb1a3ae6285c13f66295404a3.jpg\",\"uploads/z7149001426170_eb83ef3eb1a3ae6285c13f66295404a3.jpg\"]', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `address`, `password`, `role`) VALUES
(1, 'thanhxuyennnn', 'xuyen77@gmail.com', 'sd', '$2y$10$JbVrD4oNL/q9P3Cd8L1bBOvtNxYAEUfRy8ENITSqiVQo94pvGFeI2', 'user'),
(2, 'thnhxyn', 'xuyen11@gmail.com', 'sd', '$2y$10$64LrRf8INlANXqIR0w8b7ODhz84xBQi1lBxSkL2aXG9gHHljCtQFC', 'user');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', 'passwordhash', 'admin@example.com', '2025-11-11 05:43:41'),
(2, 'testuser', '123', 'test@example.com', '2025-11-11 09:07:30');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `bill_items`
--
ALTER TABLE `bill_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `chitiet_hd`
--
ALTER TABLE `chitiet_hd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `mathang`
--
ALTER TABLE `mathang`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `administrator`
--
ALTER TABLE `administrator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `bill`
--
ALTER TABLE `bill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `bill_items`
--
ALTER TABLE `bill_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `chitiet_hd`
--
ALTER TABLE `chitiet_hd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `mathang`
--
ALTER TABLE `mathang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `bill_items`
--
ALTER TABLE `bill_items`
  ADD CONSTRAINT `bill_items_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bill_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `chitiet_hd`
--
ALTER TABLE `chitiet_hd`
  ADD CONSTRAINT `chitiet_hd_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `hoadon` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitiet_hd_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `mathang` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `khachhang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
