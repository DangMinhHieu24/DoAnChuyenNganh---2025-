# 💈 eBooking Salon - Hệ thống đặt lịch Salon tích hợp AI

## 📋 Tổng quan

**eBooking Salon** là hệ thống quản lý và đặt lịch hẹn cho salon tóc, tích hợp 3 tính năng AI thông minh sử dụng Google Gemini API.

### Thông tin dự án
- **Tên dự án**: eBooking Salon
- **Phiên bản**: 1.0.0
- **Ngôn ngữ**: PHP 7.4+, MySQL 5.7+, JavaScript (ES6+)
- **Framework**: Bootstrap 5.3, jQuery 3.6
- **AI Engine**: Google Gemini 2.5 Flash

---

## ✨ Tính năng chính

### 🎯 Quản lý cốt lõi
1. **Quản lý dịch vụ** - Thêm, sửa, xóa dịch vụ salon
2. **Quản lý nhân viên** - Quản lý thông tin, lịch làm việc
3. **Đặt lịch hẹn** - Khách hàng đặt lịch online
4. **Quản lý khách hàng** - Lưu trữ thông tin, lịch sử
5. **Báo cáo thống kê** - Doanh thu, lịch hẹn, hiệu suất

### 🤖 Tính năng AI (Google Gemini)
1. **Chatbot thông minh** - Tư vấn dịch vụ, hỗ trợ đặt lịch
2. **AI Hair Consultant** - Phân tích khuôn mặt, gợi ý kiểu tóc
3. **AI Report Analysis** - Phân tích báo cáo, đưa ra insights

---

## 🏗️ Cấu trúc dự án

```
Website_DatLich/
├── admin/                      # Admin panel
│   ├── includes/              # Admin components
│   ├── bookings.php           # Booking management
│   ├── categories.php         # Category management
│   ├── customers.php          # Customer management
│   ├── dashboard.php          # Dashboard overview
│   ├── promotions.php         # Promotion management
│   ├── reports.php            # Reports & AI Analysis
│   ├── services.php           # Service management
│   ├── settings.php           # System settings
│   └── staff.php              # Staff management
│
├── api/                       # API endpoints
│   ├── staff/                 # Staff-related APIs
│   ├── ai-hair-consultant.php # AI Hair Consultant API
│   ├── ai-report-analysis.php # AI Report Analysis API
│   ├── chatbot.php            # Chatbot API
│   ├── chatbot-actions.php    # Chatbot actions
│   ├── chatbot-booking.php    # Chatbot booking
│   ├── get-staff.php          # Get staff list
│   └── get-time-slots.php     # Get available time slots
│
├── assets/                    # Static resources
│   ├── css/                   # Stylesheets
│   ├── images/                # Images
│   └── js/                    # JavaScript files
│
├── auth/                      # Authentication
│   ├── login.php              # Login page
│   ├── logout.php             # Logout handler
│   └── register.php           # Registration page
│
├── config/                    # Configuration
│   ├── chatbot-config.example.php  # AI config template
│   ├── chatbot-config.php          # AI configuration
│   ├── config.php                  # General config
│   ├── database.php                # Database connection
│   ├── Email.php                   # Email config
│   └── functions.php               # Helper functions
│
├── database/                  # Database
│   └── salon_booking.sql      # Database schema & data
│
├── includes/                  # Shared components
│   ├── chatbot-widget.php     # Chatbot widget
│   ├── footer.php             # Footer component
│   └── header.php             # Header component
│
├── models/                    # Models (MVC)
│   ├── Booking.php            # Booking model
│   ├── Category.php           # Category model
│   ├── Promotion.php          # Promotion model
│   ├── Review.php             # Review model
│   ├── Service.php            # Service model
│   ├── Staff.php              # Staff model
│   └── User.php               # User model
│
├── pages/                     # Customer pages
│   ├── ai-hair-consultant.php # AI Hair Consultant
│   ├── booking.php            # Booking page
│   ├── change-password.php    # Change password
│   ├── contact.php            # Contact page
│   ├── my-bookings.php        # My bookings
│   ├── profile.php            # User profile
│   └── services.php           # Services list
│
├── staff/                     # Staff pages
│   ├── booking-detail.php     # Booking details
│   └── dashboard.php          # Staff dashboard
│
├── uploads/                   # File uploads
│   ├── images/                # User uploaded images
│   └── services/              # Service images
│
├── .gitignore                 # Git ignore rules
├── .htaccess                  # Apache configuration
├── index.php                  # Homepage
│
├── AI_FEATURES.md             # AI features documentation
├── API_DOCUMENTATION.md       # API documentation
├── CHANGELOG.md               # Version history
├── DATABASE_SCHEMA.md         # Database schema
├── DEPLOYMENT_GUIDE.md        # Deployment guide
├── README.md                  # This file
└── SETUP_GUIDE.md             # Setup guide
```

---

## 🚀 Cài đặt

### Yêu cầu hệ thống
- PHP >= 7.4
- MySQL >= 5.7 hoặc MariaDB >= 10.2
- Apache/Nginx với mod_rewrite
- Composer (optional)

### Bước 1: Clone dự án
```bash
git clone <repository-url>
cd Website_DatLich
```

### Bước 2: Cấu hình database
1. Tạo database mới:
```sql
CREATE DATABASE salon_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import schema:
```bash
mysql -u root -p salon_booking < database/salon_booking.sql
```

3. Cấu hình kết nối trong `config/database.php`:
```php
private $host = "localhost";
private $db_name = "salon_booking";
private $username = "root";
private $password = "";
```

### Bước 3: Cấu hình ứng dụng
Chỉnh sửa `config/config.php`:
```php
define('BASE_URL', 'http://localhost/Website_DatLich');
define('SITE_NAME', 'eBooking Salon');
define('SITE_EMAIL', 'your-email@example.com');
define('SITE_PHONE', '0123456789');
```

### Bước 4: Cấu hình AI (Gemini API)
1. Lấy API key tại: https://aistudio.google.com/app/apikey
2. Cấu hình trong `config/chatbot-config.php`:
```php
define('GEMINI_API_KEY', 'your-api-key-here');
```

### Bước 5: Phân quyền thư mục
```bash
chmod 755 uploads/
chmod 755 uploads/images/
chmod 755 uploads/services/
```

### Bước 6: Truy cập
- **Trang chủ**: http://localhost/Website_DatLich
- **Admin**: http://localhost/Website_DatLich/admin
- **Đăng nhập admin**: 
  - Email: admin@salon.com
  - Password: admin123

---

## 🤖 Tính năng AI

### 1. Chatbot thông minh
**File**: `api/chatbot.php`, `includes/chatbot-widget.php`

**Chức năng**:
- Trả lời câu hỏi về dịch vụ, giá cả
- Gợi ý dịch vụ phù hợp
- Hỗ trợ đặt lịch hẹn
- Cung cấp thông tin nhân viên

**Model**: `gemini-2.5-flash`
**Quota**: 15 RPM, 1,500 RPD

### 2. AI Hair Consultant
**File**: `api/ai-hair-consultant.php`, `pages/ai-hair-consultant.php`

**Chức năng**:
- Upload ảnh khuôn mặt
- Phân tích hình dạng khuôn mặt
- Gợi ý 3-4 kiểu tóc phù hợp
- Đề xuất dịch vụ cần làm

**Model**: `gemini-2.5-flash` (Vision)
**Quota**: 15 RPM, 1,500 RPD

### 3. AI Report Analysis
**File**: `api/ai-report-analysis.php`, `admin/reports.php`

**Chức năng**:
- Phân tích doanh thu, lịch hẹn
- So sánh với tháng trước
- Đánh giá hiệu suất nhân viên
- Đưa ra gợi ý cải thiện

**Model**: `gemini-2.5-flash`
**Quota**: 15 RPM, 1,500 RPD

---

## 📊 Database Schema

### Bảng chính

#### `users`
Lưu thông tin người dùng (khách hàng, nhân viên, admin)
```sql
- user_id (PK)
- email
- password
- full_name
- phone
- role (customer/staff/admin)
- created_at
```

#### `services`
Danh sách dịch vụ salon
```sql
- service_id (PK)
- category_id (FK)
- service_name
- description
- price
- duration (phút)
- image
```

#### `staff`
Thông tin nhân viên
```sql
- staff_id (PK)
- user_id (FK)
- specialization
- experience_years
- rating
- is_available
```

#### `bookings`
Lịch hẹn
```sql
- booking_id (PK)
- customer_id (FK)
- service_id (FK)
- staff_id (FK)
- booking_date
- booking_time
- duration
- total_price
- status (pending/confirmed/completed/cancelled)
- payment_status
```

---

## 🔧 API Documentation

### Chatbot API
**Endpoint**: `/api/chatbot.php`
**Method**: POST
**Request**:
```json
{
  "message": "Giá cắt tóc bao nhiêu?"
}
```
**Response**:
```json
{
  "success": true,
  "message": "Giá cắt tóc nam từ 100,000đ...",
  "intent": "price_inquiry"
}
```

### Hair Consultant API
**Endpoint**: `/api/ai-hair-consultant.php`
**Method**: POST (multipart/form-data)
**Request**:
```
action: analyze_face
image: [file]
```
**Response**:
```json
{
  "success": true,
  "analysis": "Phân tích chi tiết...",
  "suggestions": [...]
}
```

### Report Analysis API
**Endpoint**: `/api/ai-report-analysis.php`
**Method**: GET
**Auth**: Required (Admin only)
**Response**:
```json
{
  "success": true,
  "analysis": "Phân tích báo cáo...",
  "data": {...}
}
```

---

## 🔐 Bảo mật

### API Key
- **KHÔNG** commit API key vào Git
- Sử dụng `.gitignore` để loại trừ `config/chatbot-config.php`
- Tạo file `config/chatbot-config.example.php` làm template

### Session & Authentication
- Session timeout: 30 phút
- Password hash: `password_hash()` với BCRYPT
- CSRF protection: Token validation

### SQL Injection
- Sử dụng PDO Prepared Statements
- Validate & sanitize input

---

## 📈 Performance & Quota

### AI Quota (gemini-2.5-flash)
- **RPM**: 15 requests/phút
- **RPD**: 1,500 requests/ngày
- **TPM**: 4,000,000 tokens/phút

### Ước tính sử dụng
- Chatbot: ~500 requests/ngày
- Hair Consultant: ~100 requests/ngày
- Report Analysis: ~20 requests/ngày
- **Tổng**: ~620/1,500 (41% quota)

### Tối ưu hóa
- Cache response AI (đang phát triển)
- Rate limiting cho user
- Lazy loading cho ảnh

---

## 🐛 Troubleshooting

### Lỗi kết nối database
```
Lỗi: SQLSTATE[HY000] [1045] Access denied
Giải pháp: Kiểm tra username/password trong config/database.php
```

### AI không hoạt động
```
Lỗi: 429 Quota exceeded
Giải pháp: Đợi quota reset hoặc tạo API key mới
```

### Upload ảnh lỗi
```
Lỗi: Permission denied
Giải pháp: chmod 755 uploads/
```

---

## 👥 Đóng góp

Mọi đóng góp đều được hoan nghênh! Vui lòng:
1. Fork dự án
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

---

## 📄 License

Dự án này được phát triển cho mục đích học tập và thương mại.

---

## 📞 Liên hệ

- **Email**: dminhhieu2408@gmail.com
- **Phone**: 0976985305
- **Address**: 162 ABC, Phường 5, TP Trà Vinh

---

## 🙏 Credits

- **Google Gemini AI** - AI Engine
- **Bootstrap** - UI Framework
- **Font Awesome** - Icons
- **jQuery** - JavaScript Library
