# 🚀 Hướng dẫn cài đặt chi tiết - eBooking Salon

## 📋 Mục lục
1. [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
2. [Cài đặt môi trường](#cài-đặt-môi-trường)
3. [Cài đặt dự án](#cài-đặt-dự-án)
4. [Cấu hình AI](#cấu-hình-ai)
5. [Kiểm tra & Test](#kiểm-tra--test)
6. [Troubleshooting](#troubleshooting)

---

## 🖥️ Yêu cầu hệ thống

### Phần mềm bắt buộc
- **PHP**: >= 7.4 (khuyến nghị 8.0+)
- **MySQL**: >= 5.7 hoặc MariaDB >= 10.2
- **Web Server**: Apache 2.4+ hoặc Nginx 1.18+
- **Composer**: Latest (optional)

### Extensions PHP cần thiết
```ini
extension=pdo_mysql
extension=mbstring
extension=curl
extension=gd
extension=fileinfo
extension=json
```

### Kiểm tra PHP version
```bash
php -v
```

### Kiểm tra extensions
```bash
php -m | grep -E "pdo_mysql|mbstring|curl|gd"
```

---

## 💻 Cài đặt môi trường

### Option 1: XAMPP (Windows/Mac/Linux)

#### Windows
1. Download XAMPP: https://www.apachefriends.org/
2. Cài đặt vào `C:\xampp`
3. Khởi động Apache và MySQL từ XAMPP Control Panel

#### Mac
```bash
# Download và cài đặt XAMPP
# Hoặc dùng Homebrew
brew install php@8.0
brew install mysql
brew services start mysql
```

#### Linux (Ubuntu/Debian)
```bash
sudo apt update
sudo apt install apache2 php php-mysql php-curl php-gd php-mbstring
sudo systemctl start apache2
sudo systemctl start mysql
```

### Option 2: Docker (Khuyến nghị cho production)

Tạo `docker-compose.yml`:
```yaml
version: '3.8'
services:
  web:
    image: php:8.0-apache
    ports:
      - "80:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
  
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: salon_booking
    ports:
      - "3306:3306"
```

Chạy:
```bash
docker-compose up -d
```

---

## 📦 Cài đặt dự án

### Bước 1: Clone/Download dự án

#### Từ Git
```bash
git clone <repository-url> Website_DatLich
cd Website_DatLich
```

#### Hoặc Download ZIP
1. Download ZIP từ repository
2. Giải nén vào thư mục web root:
   - XAMPP Windows: `C:\xampp\htdocs\Website_DatLich`
   - XAMPP Mac: `/Applications/XAMPP/htdocs/Website_DatLich`
   - Linux: `/var/www/html/Website_DatLich`

### Bước 2: Tạo database

#### Cách 1: Qua phpMyAdmin
1. Truy cập: http://localhost/phpmyadmin
2. Click "New" để tạo database mới
3. Tên database: `salon_booking`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Chọn database vừa tạo
7. Click tab "Import"
8. Chọn file `database/salon_booking.sql`
9. Click "Go"

#### Cách 2: Qua command line
```bash
# Đăng nhập MySQL
mysql -u root -p

# Tạo database
CREATE DATABASE salon_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Thoát
exit;

# Import schema
mysql -u root -p salon_booking < database/salon_booking.sql
```

### Bước 3: Cấu hình database

Chỉnh sửa `config/database.php`:
```php
<?php
class Database {
    private $host = "localhost";      // Thay đổi nếu cần
    private $db_name = "salon_booking"; // Tên database
    private $username = "root";        // Username MySQL
    private $password = "";            // Password MySQL (để trống nếu dùng XAMPP)
    private $charset = "utf8mb4";
    // ...
}
```

### Bước 4: Cấu hình ứng dụng

Chỉnh sửa `config/config.php`:
```php
<?php
// Base URL - Thay đổi theo môi trường
define('BASE_URL', 'http://localhost/Website_DatLich');

// Thông tin salon
define('SITE_NAME', 'eBooking Salon');
define('SITE_EMAIL', 'your-email@example.com');
define('SITE_PHONE', '0123456789');
define('SITE_ADDRESS', 'Địa chỉ salon của bạn');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
```

### Bước 5: Phân quyền thư mục

#### Windows (XAMPP)
```cmd
# Không cần phân quyền đặc biệt
# Đảm bảo thư mục uploads có thể ghi
```

#### Linux/Mac
```bash
cd Website_DatLich

# Phân quyền uploads
chmod 755 uploads/
chmod 755 uploads/images/
chmod 755 uploads/services/

# Nếu cần, đổi owner
sudo chown -R www-data:www-data uploads/
```

### Bước 6: Cấu hình .htaccess (Apache)

File `.htaccess` đã có sẵn, kiểm tra:
```apache
RewriteEngine On
RewriteBase /Website_DatLich/

# Redirect to HTTPS (optional, bỏ comment khi production)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Hide .php extension
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}\.php -f
RewriteRule ^(.*)$ $1.php [L]
```

---

## 🤖 Cấu hình AI (Google Gemini)

### Bước 1: Lấy API Key

1. Truy cập: https://aistudio.google.com/app/apikey
2. Đăng nhập bằng Google Account
3. Click "Create API Key"
4. Chọn project hoặc tạo mới
5. Copy API key (dạng: `AIzaSy...`)

### Bước 2: Cấu hình API Key

#### Cách 1: Sửa trực tiếp
Chỉnh sửa `config/chatbot-config.php`:
```php
<?php
// Thay YOUR_API_KEY_HERE bằng API key thực
define('GEMINI_API_KEY', 'AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
```

#### Cách 2: Dùng file example (Khuyến nghị)
```bash
# Copy file example
cp config/chatbot-config.example.php config/chatbot-config.php

# Chỉnh sửa file mới
nano config/chatbot-config.php
```

### Bước 3: Kiểm tra cấu hình AI

Truy cập: http://localhost/Website_DatLich/check-api-key.html

Nhập API key và test:
- ✅ Nếu thành công: "API Key hợp lệ!"
- ❌ Nếu lỗi: Xem phần Troubleshooting

### Bước 4: Cấu hình model (Optional)

Mặc định dùng `gemini-2.5-flash`. Nếu muốn đổi:
```php
// Trong config/chatbot-config.php
define('GEMINI_MODEL', 'gemini-2.5-flash'); // Hoặc model khác
```

Các model khả dụng:
- `gemini-2.5-flash` - Nhanh, quota cao (khuyến nghị)
- `gemini-2.5-pro` - Mạnh hơn nhưng quota thấp
- `gemini-1.5-flash` - Ổn định, quota trung bình

---

## ✅ Kiểm tra & Test

### 1. Kiểm tra cài đặt cơ bản

Truy cập: http://localhost/Website_DatLich

**Kết quả mong đợi:**
- ✅ Trang chủ hiển thị bình thường
- ✅ Không có lỗi PHP
- ✅ CSS/JS load đúng

### 2. Test đăng nhập

Truy cập: http://localhost/Website_DatLich/auth/login.php

**Tài khoản test:**
- **Admin**: 
  - Email: `admin@salon.com`
  - Password: `admin123`
- **Nhân viên**:
  - Email: `staff@salon.com`
  - Password: `staff123`
- **Khách hàng**:
  - Email: `customer@salon.com`
  - Password: `customer123`

### 3. Test tính năng AI

#### Test Chatbot
1. Vào trang chủ
2. Click icon chatbot góc dưới phải
3. Gửi tin nhắn: "Giá cắt tóc bao nhiêu?"
4. **Kết quả**: Chatbot trả lời về giá dịch vụ

#### Test Hair Consultant
1. Truy cập: http://localhost/Website_DatLich/pages/ai-hair-consultant.php
2. Upload ảnh khuôn mặt
3. Click "Phân tích ngay"
4. **Kết quả**: AI phân tích và gợi ý kiểu tóc

#### Test Report Analysis
1. Đăng nhập admin
2. Vào: http://localhost/Website_DatLich/admin/reports.php
3. Click "Phân tích AI"
4. **Kết quả**: AI phân tích báo cáo kinh doanh

### 4. Test đặt lịch

1. Đăng nhập khách hàng
2. Vào: http://localhost/Website_DatLich/pages/booking.php
3. Chọn dịch vụ, nhân viên, ngày giờ
4. Click "Đặt lịch"
5. **Kết quả**: Lịch hẹn được tạo thành công

---

## 🐛 Troubleshooting

### Lỗi 1: Không kết nối được database
```
Error: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'
```

**Giải pháp:**
1. Kiểm tra MySQL đã chạy chưa
2. Kiểm tra username/password trong `config/database.php`
3. Reset password MySQL:
```bash
# Windows (XAMPP)
C:\xampp\mysql\bin\mysql -u root

# Linux/Mac
mysql -u root -p
```

### Lỗi 2: API Key không hợp lệ
```
Error: 403 Forbidden - API key not valid
```

**Giải pháp:**
1. Kiểm tra API key đã copy đúng chưa
2. Kiểm tra API key đã được enable chưa tại Google AI Studio
3. Tạo API key mới nếu cần

### Lỗi 3: Hết quota AI
```
Error: 429 Quota exceeded
```

**Giải pháp:**
1. Đợi 1 phút (RPM reset)
2. Đợi đến ngày mai (RPD reset)
3. Tạo API key mới từ Gmail khác
4. Đổi sang model có quota cao hơn

### Lỗi 4: Upload ảnh lỗi
```
Error: Permission denied
```

**Giải pháp:**
```bash
# Linux/Mac
chmod 755 uploads/
chmod 755 uploads/images/
chmod 755 uploads/services/

# Windows: Kiểm tra User Account Control
```

### Lỗi 5: CSS/JS không load
```
Error: 404 Not Found - style.css
```

**Giải pháp:**
1. Kiểm tra `BASE_URL` trong `config/config.php`
2. Đảm bảo đúng đường dẫn:
```php
define('BASE_URL', 'http://localhost/Website_DatLich');
// KHÔNG có dấu / ở cuối
```

### Lỗi 6: Session không hoạt động
```
Error: Session not working
```

**Giải pháp:**
1. Kiểm tra `session.save_path` trong `php.ini`
2. Tạo thư mục session:
```bash
# Linux
sudo mkdir /var/lib/php/sessions
sudo chmod 777 /var/lib/php/sessions
```

---

## 🎯 Checklist hoàn thành

- [ ] PHP >= 7.4 đã cài đặt
- [ ] MySQL đã cài đặt và chạy
- [ ] Database `salon_booking` đã tạo
- [ ] Schema đã import thành công
- [ ] `config/database.php` đã cấu hình đúng
- [ ] `config/config.php` đã cấu hình đúng
- [ ] API Key đã lấy và cấu hình
- [ ] Thư mục `uploads/` có quyền ghi
- [ ] Trang chủ hiển thị bình thường
- [ ] Đăng nhập admin thành công
- [ ] Chatbot hoạt động
- [ ] Hair Consultant hoạt động
- [ ] Report Analysis hoạt động
- [ ] Đặt lịch hoạt động

---

## 📞 Hỗ trợ

Nếu gặp vấn đề không giải quyết được:
1. Kiểm tra lại từng bước trong hướng dẫn
2. Xem phần Troubleshooting
3. Liên hệ: dminhhieu2408@gmail.com

---

**Chúc bạn cài đặt thành công! 🎉**
