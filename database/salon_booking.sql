-- =============================================
-- DATABASE: SALON BOOKING SYSTEM
-- Hệ thống đặt lịch Salon/Spa Online
-- =============================================

CREATE DATABASE IF NOT EXISTS salon_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE salon_booking;

-- =============================================
-- Bảng: users (Người dùng)
-- =============================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'customer', 'staff') DEFAULT 'customer',
    avatar VARCHAR(255),
    status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: categories (Danh mục dịch vụ)
-- =============================================
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: services (Dịch vụ)
-- =============================================
CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    service_name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration INT NOT NULL COMMENT 'Thời gian thực hiện (phút)',
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: staff (Nhân viên)
-- =============================================
CREATE TABLE staff (
    staff_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    specialization TEXT COMMENT 'Chuyên môn',
    experience_years INT DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 5.00,
    total_bookings INT DEFAULT 0,
    bio TEXT,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: staff_services (Dịch vụ của nhân viên)
-- =============================================
CREATE TABLE staff_services (
    staff_service_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    service_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE CASCADE,
    UNIQUE KEY unique_staff_service (staff_id, service_id),
    INDEX idx_staff (staff_id),
    INDEX idx_service (service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: working_hours (Giờ làm việc)
-- =============================================
CREATE TABLE working_hours (
    working_hour_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    day_of_week TINYINT NOT NULL COMMENT '0=CN, 1=T2, ..., 6=T7',
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    INDEX idx_staff (staff_id),
    INDEX idx_day (day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: bookings (Đặt lịch)
-- =============================================
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    service_id INT NOT NULL,
    staff_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    duration INT NOT NULL COMMENT 'Thời gian (phút)',
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    payment_method ENUM('cash', 'card', 'transfer', 'momo', 'zalopay') DEFAULT 'cash',
    notes TEXT,
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    INDEX idx_customer (customer_id),
    INDEX idx_staff (staff_id),
    INDEX idx_date (booking_date),
    INDEX idx_status (status),
    INDEX idx_payment (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: reviews (Đánh giá)
-- =============================================
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    customer_id INT NOT NULL,
    staff_id INT NOT NULL,
    service_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE CASCADE,
    UNIQUE KEY unique_booking_review (booking_id),
    INDEX idx_staff (staff_id),
    INDEX idx_service (service_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: notifications (Thông báo)
-- =============================================
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('booking', 'reminder', 'promotion', 'system') DEFAULT 'system',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: promotions (Khuyến mãi)
-- =============================================
CREATE TABLE promotions (
    promotion_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') DEFAULT 'percentage',
    discount_value DECIMAL(10, 2) NOT NULL,
    min_order_value DECIMAL(10, 2) DEFAULT 0,
    max_discount DECIMAL(10, 2),
    usage_limit INT DEFAULT 0 COMMENT '0 = không giới hạn',
    used_count INT DEFAULT 0,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: booking_promotions (Khuyến mãi đã sử dụng)
-- =============================================
CREATE TABLE booking_promotions (
    booking_promotion_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    promotion_id INT NOT NULL,
    discount_amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE,
    FOREIGN KEY (promotion_id) REFERENCES promotions(promotion_id) ON DELETE CASCADE,
    INDEX idx_booking (booking_id),
    INDEX idx_promotion (promotion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Bảng: settings (Cài đặt hệ thống)
-- =============================================
CREATE TABLE settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DỮ LIỆU MẪU
-- =============================================

-- Admin account (password: admin123)
-- Hash được tạo bằng: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, email, password, full_name, phone, role, status) VALUES
('admin', 'admin@salon.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFtXmf7A.qPq1n9.Uj9TkXr9Y5b5O3lO', 'Quản trị viên', '0901234567', 'admin', 'active'),
('customer1', 'customer1@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFtXmf7A.qPq1n9.Uj9TkXr9Y5b5O3lO', 'Nguyễn Văn A', '0912345678', 'customer', 'active'),
('customer2', 'customer2@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFtXmf7A.qPq1n9.Uj9TkXr9Y5b5O3lO', 'Trần Thị B', '0923456789', 'customer', 'active'),
('staff1', 'staff1@salon.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFtXmf7A.qPq1n9.Uj9TkXr9Y5b5O3lO', 'Lê Thị C', '0934567890', 'staff', 'active'),
('staff2', 'staff2@salon.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFtXmf7A.qPq1n9.Uj9TkXr9Y5b5O3lO', 'Phạm Văn D', '0945678901', 'staff', 'active'),
('staff3', 'staff3@salon.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFtXmf7A.qPq1n9.Uj9TkXr9Y5b5O3lO', 'Hoàng Thị E', '0956789012', 'staff', 'active');

-- Danh mục dịch vụ
INSERT INTO categories (category_name, description, icon, display_order, status) VALUES
('Cắt tóc', 'Dịch vụ cắt tóc chuyên nghiệp', 'fa-scissors', 1, 'active'),
('Nhuộm tóc', 'Nhuộm tóc màu thời trang', 'fa-palette', 2, 'active'),
('Uốn tóc', 'Uốn tóc đẹp tự nhiên', 'fa-wind', 3, 'active'),
('Chăm sóc da', 'Chăm sóc da mặt chuyên sâu', 'fa-spa', 4, 'active'),
('Làm móng', 'Nail art và chăm sóc móng', 'fa-hand-sparkles', 5, 'active'),
('Massage', 'Massage thư giãn toàn thân', 'fa-hands', 6, 'active');

-- Dịch vụ
INSERT INTO services (category_id, service_name, description, price, duration, status) VALUES
(1, 'Cắt tóc nam', 'Cắt tóc nam theo xu hướng hiện đại', 100000, 30, 'active'),
(1, 'Cắt tóc nữ', 'Cắt tóc nữ chuyên nghiệp', 150000, 45, 'active'),
(1, 'Cắt tóc trẻ em', 'Cắt tóc cho bé yêu', 80000, 20, 'active'),
(2, 'Nhuộm tóc thời trang', 'Nhuộm màu theo xu hướng', 500000, 120, 'active'),
(2, 'Nhuộm phủ bạc', 'Nhuộm phủ tóc bạc tự nhiên', 300000, 90, 'active'),
(2, 'Highlight/Ombre', 'Nhuộm highlight, ombre đẹp', 600000, 150, 'active'),
(3, 'Uốn xoăn', 'Uốn tóc xoăn tự nhiên', 400000, 120, 'active'),
(3, 'Uốn duỗi', 'Duỗi tóc thẳng mượt', 350000, 90, 'active'),
(3, 'Uốn Hàn Quốc', 'Uốn tóc kiểu Hàn Quốc', 550000, 150, 'active'),
(4, 'Chăm sóc da cơ bản', 'Làm sạch và dưỡng da', 200000, 60, 'active'),
(4, 'Chăm sóc da cao cấp', 'Điều trị chuyên sâu', 400000, 90, 'active'),
(4, 'Trị mụn', 'Điều trị mụn hiệu quả', 300000, 75, 'active'),
(5, 'Sơn móng tay', 'Sơn móng tay đơn giản', 100000, 30, 'active'),
(5, 'Nail art', 'Vẽ móng nghệ thuật', 200000, 60, 'active'),
(5, 'Nối móng', 'Nối móng gel/acrylic', 300000, 90, 'active'),
(6, 'Massage body', 'Massage toàn thân thư giãn', 350000, 90, 'active'),
(6, 'Massage mặt', 'Massage mặt và đầu', 150000, 45, 'active'),
(6, 'Massage chân', 'Massage chân giảm mỏi', 120000, 30, 'active');

-- Nhân viên
INSERT INTO staff (user_id, specialization, experience_years, rating, bio, status) VALUES
(4, 'Cắt tóc, Nhuộm tóc', 5, 4.8, 'Chuyên gia tóc với 5 năm kinh nghiệm', 'active'),
(5, 'Uốn tóc, Duỗi tóc', 3, 4.5, 'Thợ uốn tóc chuyên nghiệp', 'active'),
(6, 'Chăm sóc da, Massage', 4, 4.9, 'Chuyên viên spa cao cấp', 'active');

-- Dịch vụ của nhân viên
INSERT INTO staff_services (staff_id, service_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6),
(2, 7), (2, 8), (2, 9),
(3, 10), (3, 11), (3, 12), (3, 16), (3, 17), (3, 18);

-- Giờ làm việc (T2-T7: 8:00-18:00)
INSERT INTO working_hours (staff_id, day_of_week, start_time, end_time, is_available) VALUES
-- Staff 1
(1, 1, '08:00:00', '18:00:00', TRUE),
(1, 2, '08:00:00', '18:00:00', TRUE),
(1, 3, '08:00:00', '18:00:00', TRUE),
(1, 4, '08:00:00', '18:00:00', TRUE),
(1, 5, '08:00:00', '18:00:00', TRUE),
(1, 6, '08:00:00', '18:00:00', TRUE),
-- Staff 2
(2, 1, '08:00:00', '18:00:00', TRUE),
(2, 2, '08:00:00', '18:00:00', TRUE),
(2, 3, '08:00:00', '18:00:00', TRUE),
(2, 4, '08:00:00', '18:00:00', TRUE),
(2, 5, '08:00:00', '18:00:00', TRUE),
(2, 6, '08:00:00', '18:00:00', TRUE),
-- Staff 3
(3, 1, '08:00:00', '18:00:00', TRUE),
(3, 2, '08:00:00', '18:00:00', TRUE),
(3, 3, '08:00:00', '18:00:00', TRUE),
(3, 4, '08:00:00', '18:00:00', TRUE),
(3, 5, '08:00:00', '18:00:00', TRUE),
(3, 6, '08:00:00', '18:00:00', TRUE);

-- Khuyến mãi
INSERT INTO promotions (code, title, description, discount_type, discount_value, min_order_value, max_discount, usage_limit, start_date, end_date, status) VALUES
('WELCOME10', 'Giảm 10% cho khách hàng mới', 'Áp dụng cho lần đặt lịch đầu tiên', 'percentage', 10.00, 100000, 50000, 100, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 'active'),
('SUMMER50', 'Giảm 50K mùa hè', 'Giảm 50K cho đơn từ 300K', 'fixed', 50000, 300000, 50000, 200, '2025-06-01 00:00:00', '2025-08-31 23:59:59', 'active'),
('VIP20', 'Giảm 20% khách VIP', 'Dành cho khách hàng thân thiết', 'percentage', 20.00, 500000, 100000, 50, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 'active');

-- Cài đặt hệ thống
INSERT INTO settings (setting_key, setting_value, description) VALUES
('site_name', 'eBooking', 'Tên website'),
('site_email', 'dminhhieu240@gmail.com', 'Email liên hệ'),
('site_phone', '0976985305', 'Số điện thoại'),
('site_address', '162 ABC, Phường 5, TP Trà Vinh', 'Địa chỉ'),
('booking_advance_days', '30', 'Số ngày có thể đặt trước'),
('booking_cancel_hours', '24', 'Số giờ trước khi có thể hủy'),
('working_start_time', '08:00', 'Giờ mở cửa'),
('working_end_time', '20:00', 'Giờ đóng cửa'),
('slot_duration', '30', 'Thời gian mỗi slot (phút)'),
('currency', 'VND', 'Đơn vị tiền tệ');

-- =============================================
-- VIEWS (Các view hữu ích)
-- =============================================

-- View: Thống kê booking theo ngày
CREATE VIEW v_daily_bookings AS
SELECT 
    booking_date,
    COUNT(*) as total_bookings,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
    SUM(total_price) as total_revenue
FROM bookings
GROUP BY booking_date
ORDER BY booking_date DESC;

-- View: Thống kê nhân viên
CREATE VIEW v_staff_stats AS
SELECT 
    s.staff_id,
    u.full_name,
    u.phone,
    s.rating,
    s.total_bookings,
    COUNT(DISTINCT ss.service_id) as total_services,
    s.status
FROM staff s
JOIN users u ON s.user_id = u.user_id
LEFT JOIN staff_services ss ON s.staff_id = ss.staff_id
GROUP BY s.staff_id;

-- View: Dịch vụ phổ biến
CREATE VIEW v_popular_services AS
SELECT 
    s.service_id,
    s.service_name,
    c.category_name,
    s.price,
    COUNT(b.booking_id) as booking_count,
    AVG(r.rating) as avg_rating
FROM services s
LEFT JOIN categories c ON s.category_id = c.category_id
LEFT JOIN bookings b ON s.service_id = b.service_id
LEFT JOIN reviews r ON s.service_id = r.service_id
GROUP BY s.service_id
ORDER BY booking_count DESC;

-- =============================================
-- STORED PROCEDURES
-- =============================================

DELIMITER //

-- Procedure: Kiểm tra slot thời gian có trống không
CREATE PROCEDURE sp_check_availability(
    IN p_staff_id INT,
    IN p_booking_date DATE,
    IN p_booking_time TIME,
    IN p_duration INT
)
BEGIN
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
END //

-- Procedure: Tạo booking mới
CREATE PROCEDURE sp_create_booking(
    IN p_customer_id INT,
    IN p_service_id INT,
    IN p_staff_id INT,
    IN p_booking_date DATE,
    IN p_booking_time TIME,
    IN p_notes TEXT
)
BEGIN
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
END //

DELIMITER ;

-- =============================================
-- TRIGGERS
-- =============================================

DELIMITER //

-- Trigger: Cập nhật tổng số booking của staff
CREATE TRIGGER tr_update_staff_bookings
AFTER INSERT ON bookings
FOR EACH ROW
BEGIN
    UPDATE staff
    SET total_bookings = total_bookings + 1
    WHERE staff_id = NEW.staff_id;
END //

-- Trigger: Cập nhật rating của staff khi có review mới
CREATE TRIGGER tr_update_staff_rating
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    UPDATE staff
    SET rating = (
        SELECT AVG(rating)
        FROM reviews
        WHERE staff_id = NEW.staff_id
        AND status = 'approved'
    )
    WHERE staff_id = NEW.staff_id;
END //

-- Trigger: Cập nhật số lần sử dụng promotion
CREATE TRIGGER tr_update_promotion_usage
AFTER INSERT ON booking_promotions
FOR EACH ROW
BEGIN
    UPDATE promotions
    SET used_count = used_count + 1
    WHERE promotion_id = NEW.promotion_id;
END //

DELIMITER ;
