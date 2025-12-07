# 🚀 Hướng Dẫn Cài Đặt Project

## 📋 Yêu Cầu
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- Gemini API Key (miễn phí tại https://makersuite.google.com/app/apikey)

## 🔧 Các Bước Cài Đặt

### 1️⃣ Clone Project
```bash
git clone <your-repo-url>
cd <project-folder>
```

### 2️⃣ Tạo File Config và Điền API Key
```bash
# Windows (CMD)
copy config\chatbot-config.example.php config\chatbot-config.php

# Windows (PowerShell)  
Copy-Item config\chatbot-config.example.php config\chatbot-config.php

# Mac/Linux
cp config/chatbot-config.example.php config/chatbot-config.php
```

Sau đó mở `config/chatbot-config.php` và thay:
```php
define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY_HERE');
```
thành API key thật của bạn.

### 3️⃣ Import Database

```bash
# Vào phpMyAdmin hoặc dùng command line
mysql -u root -p

# Tạo database
CREATE DATABASE salon_booking;

# Import file SQL
mysql -u root -p salon_booking < database/salon_booking.sql
```

### 4️⃣ Chạy Website

Truy cập: `http://localhost/your-folder-name`

## ✅ Kiểm Tra

- [ ] Website hiển thị bình thường
- [ ] Chatbot icon xuất hiện góc dưới phải
- [ ] Click chatbot và gửi tin nhắn thử
- [ ] Đăng nhập admin: `/admin/dashboard.php`

## 🔑 Lấy Gemini API Key

1. Truy cập: https://makersuite.google.com/app/apikey
2. Đăng nhập Google
3. Click "Create API Key"
4. Copy key và paste vào `config/chatbot-config.php`

## ❓ Troubleshooting

### Lỗi: "GEMINI_API_KEY not configured"
→ Bạn chưa tạo file `config/chatbot-config.php` hoặc chưa điền API key

### Lỗi: "Database connection failed"
→ Kiểm tra lại thông tin database trong `config/database.php`

### Chatbot không hiện
→ Kiểm tra console browser (F12) xem có lỗi JavaScript không

### API trả về lỗi 400/401
→ API key không đúng hoặc đã hết hạn, tạo key mới

## 📚 Tài Liệu Khác

- `CHATBOT_README.md` - Hướng dẫn sử dụng chatbot
- `AI_HAIR_CONSULTANT_README.md` - Hướng dẫn AI tư vấn tóc
- `SECURITY_GUIDE.md` - Hướng dẫn bảo mật

## 🆘 Cần Hỗ Trợ?

Mở issue trên GitHub hoặc liên hệ: dminhhieu2408@gmail.com
