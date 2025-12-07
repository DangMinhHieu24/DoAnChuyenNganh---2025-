-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2025 at 10:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `salon_booking`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_check_availability` (IN `p_staff_id` INT, IN `p_booking_date` DATE, IN `p_booking_time` TIME, IN `p_duration` INT)   BEGIN
    SELECT COUNT(*) as is_available
    FROM bookings
    WHERE staff_id = p_staff_id
    AND booking_date = p_booking_date
    AND status NOT IN ('cancelled', 'no_show')
    AND (
        (booking_time <= p_booking_time AND ADDTIME(booking_time, SEC_TO_TIME(duration * 60)) > p_booking_time)
        OR
        (booking_time < ADDTIME(p_booking_time, SEC_TO_TIME(p_duration * 60)) AND booking_time >= p_booking_time)
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_booking` (IN `p_customer_id` INT, IN `p_service_id` INT, IN `p_staff_id` INT, IN `p_booking_date` DATE, IN `p_booking_time` TIME, IN `p_notes` TEXT)   BEGIN
    DECLARE v_duration INT;
    DECLARE v_price DECIMAL(10,2);
    
    -- Lấy thông tin dịch vụ
    SELECT duration, price INTO v_duration, v_price
    FROM services
    WHERE service_id = p_service_id;
    
    -- Tạo booking
    INSERT INTO bookings (customer_id, service_id, staff_id, booking_date, booking_time, duration, total_price, notes)
    VALUES (p_customer_id, p_service_id, p_staff_id, p_booking_date, p_booking_time, v_duration, v_price, p_notes);
    
    SELECT LAST_INSERT_ID() as booking_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Thời gian (phút)',
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled','no_show') DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid',
  `payment_method` enum('cash','card','transfer','momo','zalopay') DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `customer_id`, `service_id`, `staff_id`, `booking_date`, `booking_time`, `duration`, `total_price`, `status`, `payment_status`, `payment_method`, `notes`, `cancellation_reason`, `created_at`, `updated_at`) VALUES
(1, 2, 8, 2, '2025-10-23', '10:00:00', 90, 350000.00, 'cancelled', 'unpaid', 'cash', '123', '13', '2025-10-18 04:06:50', '2025-10-18 04:13:27'),
(2, 2, 11, 3, '2025-10-29', '12:00:00', 90, 400000.00, 'completed', 'unpaid', 'cash', '123', NULL, '2025-10-18 04:10:27', '2025-10-28 11:34:41'),
(3, 2, 11, 3, '2025-10-23', '10:30:00', 90, 400000.00, 'pending', 'unpaid', 'cash', '', NULL, '2025-10-18 04:13:45', '2025-10-18 04:13:45'),
(4, 2, 11, 3, '2025-10-22', '12:00:00', 90, 400000.00, 'cancelled', 'unpaid', 'cash', '', '13', '2025-10-18 05:24:34', '2025-10-18 05:28:39'),
(5, 2, 11, 3, '2025-10-27', '14:30:00', 90, 400000.00, 'confirmed', 'unpaid', 'cash', '', NULL, '2025-10-27 07:02:12', '2025-10-27 07:03:24'),
(6, 2, 10, 3, '2025-10-29', '08:00:00', 60, 200000.00, 'completed', 'unpaid', 'cash', '', NULL, '2025-10-28 10:48:53', '2025-10-28 10:59:15'),
(7, 2, 10, 3, '2025-11-06', '10:00:00', 60, 200000.00, 'completed', 'unpaid', 'cash', '', NULL, '2025-11-01 06:10:22', '2025-11-01 06:11:58'),
(8, 1, 8, 2, '2025-11-13', '08:00:00', 90, 350000.00, 'completed', 'unpaid', 'cash', '', NULL, '2025-11-05 14:13:04', '2025-11-05 14:14:20');

--
-- Triggers `bookings`
--
DELIMITER $$
CREATE TRIGGER `tr_update_staff_bookings` AFTER INSERT ON `bookings` FOR EACH ROW BEGIN
    UPDATE staff
    SET total_bookings = total_bookings + 1
    WHERE staff_id = NEW.staff_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `booking_promotions`
--

CREATE TABLE `booking_promotions` (
  `booking_promotion_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `promotion_id` int(11) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `booking_promotions`
--
DELIMITER $$
CREATE TRIGGER `tr_update_promotion_usage` AFTER INSERT ON `booking_promotions` FOR EACH ROW BEGIN
    UPDATE promotions
    SET used_count = used_count + 1
    WHERE promotion_id = NEW.promotion_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `icon`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Cắt tóc', 'Dịch vụ cắt tóc chuyên nghiệp', 'fa-scissors', 1, 'active', '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(2, 'Nhuộm tóc', 'Nhuộm tóc màu thời trang', 'fa-palette', 2, 'active', '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(3, 'Uốn tóc', 'Uốn tóc đẹp tự nhiên', 'fa-wind', 3, 'active', '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(4, 'Chăm sóc da', 'Chăm sóc da mặt chuyên sâu', 'fa-spa', 4, 'active', '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(5, 'Làm móng', 'Nail art và chăm sóc móng', 'fa-hand-sparkles', 5, 'active', '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(6, 'Massage', 'Massage thư giãn toàn thân', 'fa-hands', 6, 'active', '2025-10-16 13:31:51', '2025-10-16 13:31:51');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('booking','reminder','promotion','system') DEFAULT 'system',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `promotion_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_value` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT 0 COMMENT '0 = không giới hạn',
  `used_count` int(11) DEFAULT 0,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','inactive','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`promotion_id`, `code`, `title`, `description`, `discount_type`, `discount_value`, `min_order_value`, `max_discount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME10', 'Giảm 10% cho khách hàng mới', 'Áp dụng cho lần đặt lịch đầu tiên', 'percentage', 10.00, 100000.00, 50000.00, 100, 0, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(2, 'SUMMER50', 'Giảm 50K mùa hè', 'Giảm 50K cho đơn từ 300K', 'fixed', 50000.00, 300000.00, 50000.00, 200, 0, '2025-06-01 00:00:00', '2025-08-31 23:59:59', 'active', '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(3, 'VIP20', 'Giảm 20% khách VIP', 'Dành cho khách hàng thân thiết', 'percentage', 20.00, 500000.00, 100000.00, 50, 0, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-10-16 13:31:51', '2025-10-16 13:31:51');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `booking_id`, `customer_id`, `staff_id`, `service_id`, `rating`, `comment`, `status`, `created_at`, `updated_at`) VALUES
(1, 6, 2, 3, 10, 4, 'tạm', 'pending', '2025-10-28 10:59:38', '2025-10-28 10:59:38');

--
-- Triggers `reviews`
--
DELIMITER $$
CREATE TRIGGER `tr_update_staff_rating` AFTER INSERT ON `reviews` FOR EACH ROW BEGIN
    UPDATE staff
    SET rating = (
        SELECT AVG(rating)
        FROM reviews
        WHERE staff_id = NEW.staff_id
        AND status = 'approved'
    )
    WHERE staff_id = NEW.staff_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `service_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Thời gian thực hiện (phút)',
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `category_id`, `service_name`, `description`, `price`, `duration`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Cắt tóc nam', 'Cắt tóc nam chuyên nghiệp theo xu hướng. Tư vấn kiểu tóc phù hợp với khuôn mặt.', 100000.00, 30, 'services/cattoc-1.png', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(2, 1, 'Cắt tóc nữ', 'Cắt tóc nữ hiện đại, từ ngắn đến dài. Stylist giàu kinh nghiệm tạo kiểu theo yêu cầu.', 150000.00, 45, 'services/cattoc-nu-1.jpg', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(3, 1, 'Cắt tóc trẻ em', 'Cắt tóc cho bé yêu nhẹ nhàng, an toàn. Không gian thân thiện với trẻ nhỏ.', 80000.00, 20, 'services/cattoc-treem-1.jpg', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(4, 2, 'Nhuộm tóc thời trang', 'Nhuộm tóc màu thời trang với thuốc cao cấp. Đa dạng bảng màu: highlight, ombre, balayage.', 500000.00, 120, 'services/nhuom-tieu-chuan-1.jpg', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(5, 2, 'Nhuộm phủ bạc', 'Nhuộm phủ tóc bạc tự nhiên, giữ màu lâu. Không gây hại cho tóc và da đầu.', 300000.00, 90, 'services/nhuomtoc-nam-1.jpg', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(6, 2, 'Highlight/Ombre', 'Nhuộm highlight, ombre đẹp tự nhiên. Tạo điểm nhấn cho mái tóc thêm cuốn hút.', 600000.00, 150, 'services/higlight-nu-1.jpg', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(7, 3, 'Uốn xoăn', 'Uốn tóc xoăn, sóng nhẹ công nghệ Hàn Quốc. Giữ nếp lâu, không hư tổn tóc.', 400000.00, 120, 'services/curling-hair-man-1.jpg', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(8, 3, 'Uốn duỗi', 'Duỗi thẳng tự nhiên hoặc ép phồng. Sử dụng thuốc cao cấp an toàn cho tóc.', 350000.00, 90, 'services/uonduoi-1.jpg', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(9, 3, 'Uốn Hàn Quốc', 'Uốn tóc kiểu Hàn Quốc trendy. Tạo độ phồng tự nhiên, bồng bềnh lâu dài.', 550000.00, 150, 'services/UontocHanQuoc-1.jpg', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(10, 4, 'Chăm sóc da cơ bản', 'Làm sạch sâu và dưỡng da cơ bản. Phù hợp mọi loại da, giúp da sạch khỏe.', 200000.00, 60, 'services/chamsocda-2.png', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(11, 4, 'Chăm sóc da cao cấp', 'Điều trị da chuyên sâu với công nghệ hiện đại. Phục hồi da hư tổn hiệu quả.', 400000.00, 90, 'services/chamsocda-1.png', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(12, 4, 'Trị mụn', 'Điều trị mụn hiệu quả, không để lại scar. Liệu trình chuyên nghiệp cho da mụn.', 300000.00, 75, 'services/trimun-1.png', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(13, 5, 'Sơn móng tay', 'Sơn móng tay chuyên nghiệp: sơn gel bền màu. Đa dạng mẫu mã theo xu hướng.', 100000.00, 30, 'services/sonmongtay-1.png', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(14, 5, 'Nail art', 'Vẽ nail art nghệ thuật theo yêu cầu. Họa tiết đẹp mắt, độc đáo và sáng tạo.', 200000.00, 60, 'services/nailart-1.png', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(15, 5, 'Nối móng', 'Nối móng gel/acrylic chắc khỏe. Tạo form móng đẹp, tự nhiên hoặc dài theo ý.', 300000.00, 90, 'services/noimong-1.png', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(16, 6, 'Massage body', 'Massage toàn thân thư giãn giảm stress. Kỹ thuật bấm huyệt chuyên nghiệp.', 350000.00, 90, 'services/massagebody-1.png', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(17, 6, 'Massage mặt', 'Massage mặt và đầu giảm căng thẳng. Kích thích lưu thông máu, da khỏe đẹp.', 150000.00, 45, 'services/massagemat-1.png', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00'),
(18, 6, 'Massage chân', 'Massage chân giảm mỏi mệt sau ngày dài. Bấm huyệt chân thư giãn toàn thân.', 120000.00, 30, 'services/massagechan-1.png', 'active', '2025-10-16 13:31:51', '2025-11-06 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'site_name', 'Salon Booking', 'Tên website', '2025-10-16 13:31:51'),
(2, 'site_email', 'contact@salon.com', 'Email liên hệ', '2025-10-16 13:31:51'),
(3, 'site_phone', '1900-xxxx', 'Số điện thoại', '2025-10-16 13:31:51'),
(4, 'site_address', '123 Đường ABC, Quận 1, TP.HCM', 'Địa chỉ', '2025-10-16 13:31:51'),
(5, 'booking_advance_days', '30', 'Số ngày có thể đặt trước', '2025-10-16 13:31:51'),
(6, 'booking_cancel_hours', '24', 'Số giờ trước khi có thể hủy', '2025-10-16 13:31:51'),
(7, 'working_start_time', '08:00', 'Giờ mở cửa', '2025-10-16 13:31:51'),
(8, 'working_end_time', '20:00', 'Giờ đóng cửa', '2025-10-16 13:31:51'),
(9, 'slot_duration', '30', 'Thời gian mỗi slot (phút)', '2025-10-16 13:31:51'),
(10, 'currency', 'VND', 'Đơn vị tiền tệ', '2025-10-16 13:31:51');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` text DEFAULT NULL COMMENT 'Chuyên môn',
  `experience_years` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 5.00,
  `total_bookings` int(11) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `status` enum('active','inactive','on_leave') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `user_id`, `specialization`, `experience_years`, `rating`, `total_bookings`, `bio`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'Cắt tóc, Nhuộm tóc', 5, 4.80, 0, 'Chuyên gia tóc với 5 năm kinh nghiệm', 'active', '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(2, 5, 'Uốn tóc, Duỗi tóc', 3, 4.50, 2, 'Thợ uốn tóc chuyên nghiệp', 'active', '2025-10-16 13:31:51', '2025-11-05 14:13:04'),
(3, 6, 'Chăm sóc da, Massage', 4, NULL, 6, 'Chuyên viên spa cao cấp', 'active', '2025-10-16 13:31:51', '2025-11-01 06:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `staff_services`
--

CREATE TABLE `staff_services` (
  `staff_service_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_services`
--

INSERT INTO `staff_services` (`staff_service_id`, `staff_id`, `service_id`, `created_at`) VALUES
(1, 1, 1, '2025-10-16 13:31:51'),
(2, 1, 2, '2025-10-16 13:31:51'),
(3, 1, 3, '2025-10-16 13:31:51'),
(4, 1, 4, '2025-10-16 13:31:51'),
(5, 1, 5, '2025-10-16 13:31:51'),
(6, 1, 6, '2025-10-16 13:31:51'),
(7, 2, 7, '2025-10-16 13:31:51'),
(8, 2, 8, '2025-10-16 13:31:51'),
(9, 2, 9, '2025-10-16 13:31:51'),
(10, 3, 10, '2025-10-16 13:31:51'),
(11, 3, 11, '2025-10-16 13:31:51'),
(12, 3, 12, '2025-10-16 13:31:51'),
(13, 3, 16, '2025-10-16 13:31:51'),
(14, 3, 17, '2025-10-16 13:31:51'),
(15, 3, 18, '2025-10-16 13:31:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','customer','staff') DEFAULT 'customer',
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','blocked') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `avatar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'adminHieu', 'dminhhieu2408@gmail.com', '$2y$10$.sc/atsll191T/iYpKERG.GzPyv8vUvyZ4SGK7DIkMhdTVht5RxUO', 'Đặng Minh Hiếu', '0976985305', NULL, 'admin', NULL, 'active', '2025-10-16 13:31:51', '2025-11-09 07:01:00'),
(2, 'Dangthiminhngoc', 'DangThiMinhNgoc@gmail.com', '$2y$10$.sc/atsll191T/iYpKERG.GzPyv8vUvyZ4SGK7DIkMhdTVht5RxUO', 'Đặng Thị Minh Ngọc', '0912345678', '', 'customer', 'avatars/68f30b7fd6456.png', 'active', '2025-10-16 13:31:51', '2025-11-09 07:01:00'),
(3, 'Nguyenthikimngan', 'NguyenThiKimNgan@gmail.com', '$2y$10$.sc/atsll191T/iYpKERG.GzPyv8vUvyZ4SGK7DIkMhdTVht5RxUO', 'Nguyễn Thị Kim Ngân', '0923456789', NULL, 'customer', NULL, 'active', '2025-10-16 13:31:51', '2025-11-09 07:01:00'),
(4, 'Lethichau', 'LeThiChau@gmail.com', '$2y$10$.sc/atsll191T/iYpKERG.GzPyv8vUvyZ4SGK7DIkMhdTVht5RxUO', 'Lê Thị Châu', '0934567890', NULL, 'staff', NULL, 'active', '2025-10-16 13:31:51', '2025-11-09 07:01:00'),
(5, 'Phamvanduoc', 'PhamVanDuoc@gmail.com', '$2y$10$.sc/atsll191T/iYpKERG.GzPyv8vUvyZ4SGK7DIkMhdTVht5RxUO', 'Phạm Văn Được', '0945678901', NULL, 'staff', NULL, 'active', '2025-10-16 13:31:51', '2025-11-09 07:01:00'),
(6, 'Hoangthiem', 'HoangThiEm@gmail.com', '$2y$10$.sc/atsll191T/iYpKERG.GzPyv8vUvyZ4SGK7DIkMhdTVht5RxUO', 'Hoàng Thị Em', '0956789012', NULL, 'staff', NULL, 'active', '2025-10-16 13:31:51', '2025-11-09 07:01:00');


-- --------------------------------------------------------

--
-- Stand-in structure for view `v_daily_bookings`
-- (See below for the actual view)
--
CREATE TABLE `v_daily_bookings` (
`booking_date` date
,`total_bookings` bigint(21)
,`confirmed` decimal(22,0)
,`completed` decimal(22,0)
,`cancelled` decimal(22,0)
,`total_revenue` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_popular_services`
-- (See below for the actual view)
--
CREATE TABLE `v_popular_services` (
`service_id` int(11)
,`service_name` varchar(150)
,`category_name` varchar(100)
,`price` decimal(10,2)
,`booking_count` bigint(21)
,`avg_rating` decimal(7,4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_staff_stats`
-- (See below for the actual view)
--
CREATE TABLE `v_staff_stats` (
`staff_id` int(11)
,`full_name` varchar(100)
,`phone` varchar(20)
,`rating` decimal(3,2)
,`total_bookings` int(11)
,`total_services` bigint(21)
,`status` enum('active','inactive','on_leave')
);

-- --------------------------------------------------------

--
-- Table structure for table `working_hours`
--

CREATE TABLE `working_hours` (
  `working_hour_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `day_of_week` tinyint(4) NOT NULL COMMENT '0=CN, 1=T2, ..., 6=T7',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `working_hours`
--

INSERT INTO `working_hours` (`working_hour_id`, `staff_id`, `day_of_week`, `start_time`, `end_time`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(2, 1, 2, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(3, 1, 3, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(4, 1, 4, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(5, 1, 5, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(6, 1, 6, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(7, 2, 1, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(8, 2, 2, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(9, 2, 3, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(10, 2, 4, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(11, 2, 5, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(12, 2, 6, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(13, 3, 1, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(14, 3, 2, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(15, 3, 3, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(16, 3, 4, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(17, 3, 5, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51'),
(18, 3, 6, '08:00:00', '18:00:00', 1, '2025-10-16 13:31:51', '2025-10-16 13:31:51');

-- --------------------------------------------------------

--
-- Structure for view `v_daily_bookings`
--
DROP TABLE IF EXISTS `v_daily_bookings`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_daily_bookings`  AS SELECT `bookings`.`booking_date` AS `booking_date`, count(0) AS `total_bookings`, sum(case when `bookings`.`status` = 'confirmed' then 1 else 0 end) AS `confirmed`, sum(case when `bookings`.`status` = 'completed' then 1 else 0 end) AS `completed`, sum(case when `bookings`.`status` = 'cancelled' then 1 else 0 end) AS `cancelled`, sum(`bookings`.`total_price`) AS `total_revenue` FROM `bookings` GROUP BY `bookings`.`booking_date` ORDER BY `bookings`.`booking_date` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_popular_services`
--
DROP TABLE IF EXISTS `v_popular_services`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_popular_services`  AS SELECT `s`.`service_id` AS `service_id`, `s`.`service_name` AS `service_name`, `c`.`category_name` AS `category_name`, `s`.`price` AS `price`, count(`b`.`booking_id`) AS `booking_count`, avg(`r`.`rating`) AS `avg_rating` FROM (((`services` `s` left join `categories` `c` on(`s`.`category_id` = `c`.`category_id`)) left join `bookings` `b` on(`s`.`service_id` = `b`.`service_id`)) left join `reviews` `r` on(`s`.`service_id` = `r`.`service_id`)) GROUP BY `s`.`service_id` ORDER BY count(`b`.`booking_id`) DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_staff_stats`
--
DROP TABLE IF EXISTS `v_staff_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_staff_stats`  AS SELECT `s`.`staff_id` AS `staff_id`, `u`.`full_name` AS `full_name`, `u`.`phone` AS `phone`, `s`.`rating` AS `rating`, `s`.`total_bookings` AS `total_bookings`, count(distinct `ss`.`service_id`) AS `total_services`, `s`.`status` AS `status` FROM ((`staff` `s` join `users` `u` on(`s`.`user_id` = `u`.`user_id`)) left join `staff_services` `ss` on(`s`.`staff_id` = `ss`.`staff_id`)) GROUP BY `s`.`staff_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_staff` (`staff_id`),
  ADD KEY `idx_date` (`booking_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment` (`payment_status`);

--
-- Indexes for table `booking_promotions`
--
ALTER TABLE `booking_promotions`
  ADD PRIMARY KEY (`booking_promotion_id`),
  ADD KEY `idx_booking` (`booking_id`),
  ADD KEY `idx_promotion` (`promotion_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_order` (`display_order`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promotion_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_booking_review` (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `idx_staff` (`staff_id`),
  ADD KEY `idx_service` (`service_id`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_price` (`price`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `staff_services`
--
ALTER TABLE `staff_services`
  ADD PRIMARY KEY (`staff_service_id`),
  ADD UNIQUE KEY `unique_staff_service` (`staff_id`,`service_id`),
  ADD KEY `idx_staff` (`staff_id`),
  ADD KEY `idx_service` (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `working_hours`
--
ALTER TABLE `working_hours`
  ADD PRIMARY KEY (`working_hour_id`),
  ADD KEY `idx_staff` (`staff_id`),
  ADD KEY `idx_day` (`day_of_week`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `booking_promotions`
--
ALTER TABLE `booking_promotions`
  MODIFY `booking_promotion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staff_services`
--
ALTER TABLE `staff_services`
  MODIFY `staff_service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `working_hours`
--
ALTER TABLE `working_hours`
  MODIFY `working_hour_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_promotions`
--
ALTER TABLE `booking_promotions`
  ADD CONSTRAINT `booking_promotions_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_promotions_ibfk_2` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`promotion_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_4` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_services`
--
ALTER TABLE `staff_services`
  ADD CONSTRAINT `staff_services_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE;

--
-- Constraints for table `working_hours`
--
ALTER TABLE `working_hours`
  ADD CONSTRAINT `working_hours_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
