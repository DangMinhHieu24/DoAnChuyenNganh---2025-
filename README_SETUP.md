# 🚀 Hướng Dẫn Cài Đặt Project

## 📋 Yêu Cầu
- **XAMPP** (hoặc WAMP/LAMP) - Đã cài PHP 7.4+ và MySQL
- **Gemini API Key** - Miễn phí tại https://makersuite.google.com/app/apikey

## 🔧 Các Bước Cài Đặt

### 1️⃣ Clone Project về máy

```bash
# Clone về thư mục htdocs của XAMPP
cd C:\xampp\htdocs

# Clone project
git clone https://github.com/DangMinhHieu24/DoAnChuyenNganh---2025-.git

# Đổi tên folder (tùy chọn)
rename DoAnChuyenNganh---2025- Website_DatLich
```

### 2️⃣ Tạo File Config API

```bash
# Vào thư mục project
cd Website_DatLich

# Copy file example thành file thật
copy config\chatbot-config.example.php config\chatbot-config.php
```

**Mở file `config/chatbot-config.php`** và thay đổi:

```php
// Dòng 11: Thay YOUR_GEMINI_API_KEY_HERE bằng API key của bạn
define('GEMINI_API_KEY', 'AIzaSy...');  // ← Điền API key ở đây
```

### 3️⃣ Tạo Database

**Cách 1: Dùng phpMyAdmin (Dễ nhất)**

1. Mở XAMPP Control Panel → Start **Apache** và **MySQL**
2. Mở trình duyệt: http://localhost/phpmyadmin
3. Click **New** (Tạo database mới)
4. Tên database: `salon_booking`
5. Collation: `utf8mb4_unicode_ci`
6. Click **Create**
7. Vào database `salon_booking` → Tab **Import**
8. Chọn file: `database/salon_booking.sql`
9. Click **Go**

**Cách 2: Dùng Command Line**

```bash
# Mở CMD và chạy
mysql -u root -p

# Tạo database
CREATE DATABASE salon_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Thoát
exit

# Import file SQL
mysql -u root -p salon_booking < database/salon_booking.sql
```

### 4️⃣ Cấu Hình Database (Nếu cần)

Mở file `config/database.php` và kiểm tra:

```php
private $host = "localhost";      // ✅ Giữ nguyên
private $db_name = "salon_booking"; // ✅ Giữ nguyên
private $username = "root";        // ✅ Giữ nguyên
private $password = "";            // ⚠️ Nếu MySQL có password thì điền vào
```

### 5️⃣ Cấu Hình Base URL (Nếu cần)

Mở file `config/config.php` và kiểm tra:

```php
// Nếu folder của bạn tên khác, sửa lại cho đúng
define('BASE_URL', 'http://localhost/Website_DatLich');
```

### 6️⃣ Chạy Website

1. **Bật XAMPP**: Mở XAMPP Control Panel → Start **Apache** và **MySQL**

2. **Truy cập website**: 
   ```
   http://localhost/Website_DatLich
   ```

3. **Kiểm tra chatbot**: 
   - Icon chatbot xuất hiện góc dưới phải ✅
   - Click vào và gửi tin nhắn thử

## ✅ Checklist Kiểm Tra

- [ ] XAMPP Apache và MySQL đang chạy
- [ ] Database `salon_booking` đã được tạo và import
- [ ] File `config/chatbot-config.php` đã có API key
- [ ] Website hiển thị bình thường tại http://localhost/Website_DatLich
- [ ] Chatbot icon xuất hiện và hoạt động
- [ ] Có thể đăng nhập admin

## 👤 Tài Khoản Mặc Định

**Admin:**
- Email: `dminhhieu2408@gmail.com`
- Password: `123456` (hoặc check trong database)

**Khách hàng:**
- Email: `DangThiMinhNgoc@gmail.com`
- Password: `123456` (hoặc check trong database)

## 🔑 Cách Lấy Gemini API Key

1. Truy cập: https://makersuite.google.com/app/apikey
2. Đăng nhập bằng tài khoản Google
3. Click **"Create API Key"** hoặc **"Get API Key"**
4. Copy API key (dạng: `AIzaSy...`)
5. Paste vào file `config/chatbot-config.php` dòng 12

**Giới hạn miễn phí:**
- 15 requests/phút
- 1,500 requests/ngày
- 1 triệu tokens/ngày
- Quota reset vào 7:00 sáng mỗi ngày (giờ Việt Nam)

## ❓ Xử Lý Lỗi Thường Gặp

### 🔴 Lỗi: "GEMINI_API_KEY not configured"
**Nguyên nhân:** Chưa tạo file `config/chatbot-config.php` hoặc chưa điền API key

**Giải pháp:**
```bash
# Copy file example
copy config\chatbot-config.example.php config\chatbot-config.php

# Mở file và điền API key vào dòng 11
```

### 🔴 Lỗi: "Database connection failed"
**Nguyên nhân:** MySQL chưa chạy hoặc database chưa tạo

**Giải pháp:**
1. Mở XAMPP → Start MySQL
2. Vào phpMyAdmin → Tạo database `salon_booking`
3. Import file `database/salon_booking.sql`
4. Kiểm tra password MySQL trong `config/database.php`

### 🔴 Lỗi: "404 Not Found"
**Nguyên nhân:** BASE_URL không đúng

**Giải pháp:**
- Mở `config/config.php`
- Sửa `BASE_URL` cho đúng với tên folder của bạn
- Ví dụ: `http://localhost/TenFolderCuaBan`

### 🔴 Chatbot không hiện
**Nguyên nhân:** Lỗi JavaScript hoặc file thiếu

**Giải pháp:**
1. Nhấn F12 → Tab Console
2. Xem lỗi gì
3. Kiểm tra file `assets/js/chatbot.js` có tồn tại không

### 🔴 API trả về lỗi 400/401
**Nguyên nhân:** API key sai hoặc hết hạn

**Giải pháp:**
- Tạo API key mới tại https://makersuite.google.com/app/apikey
- Thay vào `config/chatbot-config.php`

### 🔴 Lỗi 429 - Quota Exceeded
**Nguyên nhân:** Đã vượt quá giới hạn miễn phí (15 requests/phút hoặc 1500 requests/ngày)

**Giải pháp:**
1. **Đợi reset:** Quota reset vào 7:00 sáng hôm sau
2. **Tạo key mới:** Tạo project mới và API key mới
3. **Upgrade:** Nâng cấp lên paid plan ($0.075/1M tokens)

### 🔴 Config đã sửa nhưng vẫn lỗi
**Nguyên nhân:** Apache chưa reload config mới

**Giải pháp:**
1. Mở XAMPP Control Panel
2. Click **Stop** Apache
3. Click **Start** Apache
4. Thử lại

### 🔴 Lỗi: "Access denied for user 'root'@'localhost'"
**Nguyên nhân:** MySQL có password

**Giải pháp:**
- Mở `config/database.php`
- Sửa dòng: `private $password = "your_mysql_password";`

## 📚 Tài Liệu Khác

- `CHATBOT_README.md` - Hướng dẫn sử dụng chatbot
- `AI_HAIR_CONSULTANT_README.md` - Hướng dẫn AI tư vấn tóc
- `SECURITY_GUIDE.md` - Hướng dẫn bảo mật

## 🆘 Cần Hỗ Trợ?

Mở issue trên GitHub hoặc liên hệ: dminhhieu2408@gmail.com
