# 🗄️ Cấu trúc Database - eBooking Salon

## 📋 Tổng quan

**Tên Database**: `salon_booking`

**Charset**: `utf8mb4`

**Collation**: `utf8mb4_unicode_ci`

**Engine**: InnoDB

**Tổng số bảng**: 13

---

## 📊 Sơ đồ quan hệ thực thể (ERD)

```
users (1) ----< (N) bookings
users (1) ----< (1) staff
staff (1) ----< (N) bookings
staff (N) ----< (N) services (through staff_services)
services (1) ----< (N) bookings
services (N) ----< (1) categories
bookings (1) ----< (N) reviews
bookings (N) ----< (N) promotions (through booking_promotions)
staff (1) ----< (N) working_hours
users (1) ----< (N) notifications
```

---

## 📑 Các bảng (Tables)

### 1. users
**Mô tả**: Lưu thông tin người dùng (khách hàng, nhân viên, admin)

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| user_id | int(11) | NO | PRI | AUTO_INCREMENT | ID người dùng |
| username | varchar(50) | NO | UNI | - | Tên đăng nhập |
| email | varchar(100) | NO | UNI | - | Email |
| password | varchar(255) | NO | - | - | Mật khẩu (hashed) |
| full_name | varchar(100) | NO | - | - | Họ tên đầy đủ |
| phone | varchar(20) | YES | - | NULL | Số điện thoại |
| address | text | YES | - | NULL | Địa chỉ |
| role | enum | NO | IDX | customer | Vai trò: admin/customer/staff |
| avatar | varchar(255) | YES | - | NULL | Đường dẫn avatar |
| status | enum | NO | IDX | active | Trạng thái: active/inactive/blocked |
| created_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày cập nhật |

**Indexes:**
- PRIMARY KEY: `user_id`
- UNIQUE: `username`, `email`
- INDEX: `email`, `role`, `status`

**Sample Data:**
```sql
INSERT INTO users VALUES
(1, 'adminHieu', 'dminhhieu2408@gmail.com', '$2y$10$...', 'Đặng Minh Hiếu', '0976985305', NULL, 'admin', NULL, 'active', NOW(), NOW());
```

---

### 2. staff
**Description**: Thông tin nhân viên salon

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| staff_id | int(11) | NO | PRI | AUTO_INCREMENT | ID nhân viên |
| user_id | int(11) | NO | FK | - | ID người dùng |
| specialization | text | YES | - | NULL | Chuyên môn |
| experience_years | int(11) | NO | - | 0 | Số năm kinh nghiệm |
| rating | decimal(3,2) | NO | IDX | 5.00 | Đánh giá (1.00-5.00) |
| total_bookings | int(11) | NO | - | 0 | Tổng số lịch hẹn |
| bio | text | YES | - | NULL | Tiểu sử |
| status | enum | NO | IDX | active | Trạng thái: active/inactive/on_leave |
| created_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày cập nhật |

**Indexes:**
- PRIMARY KEY: `staff_id`
- FOREIGN KEY: `user_id` → `users(user_id)`
- INDEX: `user_id`, `status`, `rating`

**Relationships:**
- `user_id` → `users.user_id` (1:1)
- `staff_id` ← `bookings.staff_id` (1:N)
- `staff_id` ← `staff_services.staff_id` (1:N)

---

### 3. categories
**Description**: Danh mục dịch vụ

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| category_id | int(11) | NO | PRI | AUTO_INCREMENT | ID danh mục |
| category_name | varchar(100) | NO | - | - | Tên danh mục |
| description | text | YES | - | NULL | Mô tả |
| icon | varchar(100) | YES | - | NULL | Icon (FontAwesome) |
| display_order | int(11) | NO | IDX | 0 | Thứ tự hiển thị |
| status | enum | NO | IDX | active | Trạng thái: active/inactive |
| created_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày cập nhật |

**Indexes:**
- PRIMARY KEY: `category_id`
- INDEX: `status`, `display_order`

**Sample Categories:**
1. Cắt tóc (fa-scissors)
2. Nhuộm tóc (fa-palette)
3. Uốn tóc (fa-wind)
4. Chăm sóc da (fa-spa)
5. Làm móng (fa-hand-sparkles)
6. Massage (fa-hands)

---

### 4. services
**Description**: Dịch vụ salon

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| service_id | int(11) | NO | PRI | AUTO_INCREMENT | ID dịch vụ |
| category_id | int(11) | NO | FK | - | ID danh mục |
| service_name | varchar(150) | NO | - | - | Tên dịch vụ |
| description | text | YES | - | NULL | Mô tả chi tiết |
| price | decimal(10,2) | NO | IDX | - | Giá dịch vụ (VNĐ) |
| duration | int(11) | NO | - | - | Thời gian (phút) |
| image | varchar(255) | YES | - | NULL | Hình ảnh |
| status | enum | NO | IDX | active | Trạng thái: active/inactive |
| created_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày cập nhật |

**Indexes:**
- PRIMARY KEY: `service_id`
- FOREIGN KEY: `category_id` → `categories(category_id)`
- INDEX: `category_id`, `status`, `price`

**Price Range:**
- Min: 80,000đ (Cắt tóc trẻ em)
- Max: 600,000đ (Highlight/Ombre)

---

### 5. bookings
**Description**: Lịch hẹn

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| booking_id | int(11) | NO | PRI | AUTO_INCREMENT | ID lịch hẹn |
| customer_id | int(11) | NO | FK | - | ID khách hàng |
| service_id | int(11) | NO | FK | - | ID dịch vụ |
| staff_id | int(11) | NO | FK | - | ID nhân viên |
| booking_date | date | NO | IDX | - | Ngày hẹn |
| booking_time | time | NO | - | - | Giờ hẹn |
| duration | int(11) | NO | - | - | Thời gian (phút) |
| total_price | decimal(10,2) | NO | - | - | Tổng tiền |
| status | enum | NO | IDX | pending | pending/confirmed/completed/cancelled/no_show |
| payment_status | enum | NO | IDX | unpaid | unpaid/paid/refunded |
| payment_method | enum | NO | - | cash | cash/card/transfer/momo/zalopay |
| notes | text | YES | - | NULL | Ghi chú |
| cancellation_reason | text | YES | - | NULL | Lý do hủy |
| created_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày cập nhật |

**Indexes:**
- PRIMARY KEY: `booking_id`
- FOREIGN KEY: `customer_id` → `users(user_id)`
- FOREIGN KEY: `service_id` → `services(service_id)`
- FOREIGN KEY: `staff_id` → `staff(staff_id)`
- INDEX: `customer_id`, `staff_id`, `booking_date`, `status`, `payment_status`

**Status Flow:**
```
pending → confirmed → completed
   ↓
cancelled
   ↓
no_show
```

**Triggers:**
- `tr_update_staff_bookings`: Tự động tăng `staff.total_bookings` khi có booking mới

---

### 6. reviews
**Description**: Đánh giá dịch vụ

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| review_id | int(11) | NO | PRI | AUTO_INCREMENT | ID đánh giá |
| booking_id | int(11) | NO | UNI | - | ID lịch hẹn |
| customer_id | int(11) | NO | FK | - | ID khách hàng |
| staff_id | int(11) | NO | FK | - | ID nhân viên |
| service_id | int(11) | NO | FK | - | ID dịch vụ |
| rating | tinyint(4) | NO | IDX | - | Đánh giá (1-5) |
| comment | text | YES | - | NULL | Nhận xét |
| status | enum | NO | - | pending | pending/approved/rejected |
| created_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày cập nhật |

**Indexes:**
- PRIMARY KEY: `review_id`
- UNIQUE: `booking_id` (1 booking chỉ có 1 review)
- FOREIGN KEY: `customer_id`, `staff_id`, `service_id`
- INDEX: `staff_id`, `service_id`, `rating`

**Constraints:**
- `rating` BETWEEN 1 AND 5

**Triggers:**
- `tr_update_staff_rating`: Tự động cập nhật `staff.rating` khi có review mới

---

### 7. promotions
**Description**: Mã khuyến mãi

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| promotion_id | int(11) | NO | PRI | AUTO_INCREMENT | ID khuyến mãi |
| code | varchar(50) | NO | UNI | - | Mã code |
| title | varchar(200) | NO | - | - | Tiêu đề |
| description | text | YES | - | NULL | Mô tả |
| discount_type | enum | NO | - | percentage | percentage/fixed |
| discount_value | decimal(10,2) | NO | - | - | Giá trị giảm |
| min_order_value | decimal(10,2) | NO | - | 0.00 | Giá trị đơn tối thiểu |
| max_discount | decimal(10,2) | YES | - | NULL | Giảm tối đa |
| usage_limit | int(11) | NO | - | 0 | Giới hạn sử dụng (0=unlimited) |
| used_count | int(11) | NO | - | 0 | Số lần đã dùng |
| start_date | datetime | NO | IDX | - | Ngày bắt đầu |
| end_date | datetime | NO | IDX | - | Ngày kết thúc |
| status | enum | NO | IDX | active | active/inactive/expired |
| created_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày cập nhật |

**Indexes:**
- PRIMARY KEY: `promotion_id`
- UNIQUE: `code`
- INDEX: `code`, `status`, `start_date`, `end_date`

**Sample Promotions:**
- WELCOME10: Giảm 10% cho khách mới
- SUMMER50: Giảm 50K cho đơn từ 300K
- VIP20: Giảm 20% khách VIP

---

### 8. booking_promotions
**Description**: Liên kết booking và promotion

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| booking_promotion_id | int(11) | NO | PRI | AUTO_INCREMENT | ID |
| booking_id | int(11) | NO | FK | - | ID lịch hẹn |
| promotion_id | int(11) | NO | FK | - | ID khuyến mãi |
| discount_amount | decimal(10,2) | NO | - | - | Số tiền giảm |
| created_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày tạo |

**Indexes:**
- PRIMARY KEY: `booking_promotion_id`
- FOREIGN KEY: `booking_id`, `promotion_id`
- INDEX: `booking_id`, `promotion_id`

**Triggers:**
- `tr_update_promotion_usage`: Tự động tăng `promotions.used_count`

---

### 9. staff_services
**Description**: Liên kết nhân viên và dịch vụ (N:N)

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| staff_service_id | int(11) | NO | PRI | AUTO_INCREMENT | ID |
| staff_id | int(11) | NO | FK | - | ID nhân viên |
| service_id | int(11) | NO | FK | - | ID dịch vụ |
| created_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày tạo |

**Indexes:**
- PRIMARY KEY: `staff_service_id`
- UNIQUE: `(staff_id, service_id)`
- FOREIGN KEY: `staff_id`, `service_id`
- INDEX: `staff_id`, `service_id`

---

### 10. working_hours
**Description**: Lịch làm việc của nhân viên

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| working_hour_id | int(11) | NO | PRI | AUTO_INCREMENT | ID |
| staff_id | int(11) | NO | FK | - | ID nhân viên |
| day_of_week | tinyint(4) | NO | IDX | - | Thứ (0=CN, 1=T2, ..., 6=T7) |
| start_time | time | NO | - | - | Giờ bắt đầu |
| end_time | time | NO | - | - | Giờ kết thúc |
| is_available | tinyint(1) | NO | - | 1 | Có làm việc không |
| created_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày cập nhật |

**Indexes:**
- PRIMARY KEY: `working_hour_id`
- FOREIGN KEY: `staff_id` → `staff(staff_id)`
- INDEX: `staff_id`, `day_of_week`

**Default Working Hours**: 08:00 - 18:00 (T2-T7)

---

### 11. notifications
**Description**: Thông báo cho người dùng

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| notification_id | int(11) | NO | PRI | AUTO_INCREMENT | ID thông báo |
| user_id | int(11) | NO | FK | - | ID người dùng |
| title | varchar(200) | NO | - | - | Tiêu đề |
| message | text | NO | - | - | Nội dung |
| type | enum | NO | - | system | booking/reminder/promotion/system |
| is_read | tinyint(1) | NO | IDX | 0 | Đã đọc chưa |
| created_at | timestamp | NO | IDX | CURRENT_TIMESTAMP | Ngày tạo |

**Indexes:**
- PRIMARY KEY: `notification_id`
- FOREIGN KEY: `user_id` → `users(user_id)`
- INDEX: `user_id`, `is_read`, `created_at`

---

### 12. settings
**Description**: Cấu hình hệ thống

| Column | Type | Null | Key | Default | Description |
|--------|------|------|-----|---------|-------------|
| setting_id | int(11) | NO | PRI | AUTO_INCREMENT | ID |
| setting_key | varchar(100) | NO | UNI | - | Key |
| setting_value | text | YES | - | NULL | Value |
| description | varchar(255) | YES | - | NULL | Mô tả |
| updated_at | timestamp | NO | - | CURRENT_TIMESTAMP | Ngày cập nhật |

**Indexes:**
- PRIMARY KEY: `setting_id`
- UNIQUE: `setting_key`
- INDEX: `setting_key`

**System Settings:**
- `site_name`: Tên website
- `site_email`: Email liên hệ
- `site_phone`: Số điện thoại
- `site_address`: Địa chỉ
- `booking_advance_days`: Số ngày đặt trước (30)
- `booking_cancel_hours`: Số giờ trước khi hủy (24)
- `working_start_time`: Giờ mở cửa (08:00)
- `working_end_time`: Giờ đóng cửa (20:00)
- `slot_duration`: Thời gian mỗi slot (30 phút)
- `currency`: Đơn vị tiền tệ (VND)

---

## 📊 Views

### 1. v_daily_bookings
**Description**: Thống kê booking theo ngày

```sql
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
```

---

### 2. v_popular_services
**Description**: Dịch vụ phổ biến

```sql
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
```

---

### 3. v_staff_stats
**Description**: Thống kê nhân viên

```sql
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
```

---

## 🔧 Stored Procedures

### 1. sp_check_availability
**Description**: Kiểm tra nhân viên có rảnh không

```sql
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
END
```

**Usage:**
```sql
CALL sp_check_availability(1, '2025-12-15', '10:00:00', 30);
```

---

### 2. sp_create_booking
**Description**: Tạo booking mới

```sql
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
END
```

**Usage:**
```sql
CALL sp_create_booking(2, 1, 1, '2025-12-15', '10:00:00', 'Cắt ngắn');
```

---

## 🔄 Triggers

### 1. tr_update_staff_bookings
**Table**: `bookings`
**Event**: AFTER INSERT
**Action**: Tăng `staff.total_bookings`

```sql
CREATE TRIGGER tr_update_staff_bookings 
AFTER INSERT ON bookings
FOR EACH ROW
BEGIN
    UPDATE staff
    SET total_bookings = total_bookings + 1
    WHERE staff_id = NEW.staff_id;
END
```

---

### 2. tr_update_staff_rating
**Table**: `reviews`
**Event**: AFTER INSERT
**Action**: Cập nhật `staff.rating`

```sql
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
END
```

---

### 3. tr_update_promotion_usage
**Table**: `booking_promotions`
**Event**: AFTER INSERT
**Action**: Tăng `promotions.used_count`

```sql
CREATE TRIGGER tr_update_promotion_usage 
AFTER INSERT ON booking_promotions
FOR EACH ROW
BEGIN
    UPDATE promotions
    SET used_count = used_count + 1
    WHERE promotion_id = NEW.promotion_id;
END
```

---

## 📈 Statistics

### Database Size
- **Tables**: 13
- **Views**: 3
- **Stored Procedures**: 2
- **Triggers**: 3
- **Indexes**: 40+

### Sample Data Count
- **Users**: 6 (1 admin, 2 customers, 3 staff)
- **Services**: 18
- **Categories**: 6
- **Bookings**: 8
- **Reviews**: 1
- **Promotions**: 3

---

## 🔐 Security

### Password Hashing
```php
$password = password_hash($plain_password, PASSWORD_BCRYPT);
```

### SQL Injection Prevention
```php
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

### XSS Prevention
```php
$safe_output = htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

---

## 🛠️ Maintenance

### Backup Database
```bash
mysqldump -u root -p salon_booking > backup_$(date +%Y%m%d).sql
```

### Restore Database
```bash
mysql -u root -p salon_booking < backup_20251210.sql
```

### Optimize Tables
```sql
OPTIMIZE TABLE bookings, services, users;
```

### Check Table Status
```sql
CHECK TABLE bookings;
```

---

## 📞 Support

Nếu cần hỗ trợ về database:
- Email: dminhhieu2408@gmail.com
- Phone: 0976985305

---

**Last Updated**: December 10, 2025
