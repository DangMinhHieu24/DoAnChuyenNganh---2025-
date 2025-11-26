# HƯỚNG DẪN CHUYỂN DỰ ÁN SANG MÁY KHÁC

## 📋 CHUẨN BỊ TRÊN MÁY CŨ (Test XAMPP)

### Bước 1: Export Database
```
1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Chọn database: salon_booking
3. Tab "Export"
4. Method: Quick
5. Format: SQL
6. Click "Go"
7. Lưu file: salon_booking.sql
```

### Bước 2: Copy toàn bộ source code
```
Copy folder:
d:\Website_DatLich\

Bao gồm:
✅ admin/
✅ api/
✅ assets/
✅ auth/
✅ config/
✅ database/
✅ includes/
✅ models/
✅ pages/
✅ staff/
✅ uploads/           ← QUAN TRỌNG: Có ảnh dịch vụ!
✅ index.php
✅ .htaccess
✅ check_system.php
✅ README.md
```

**⚠️ LƯU Ý QUAN TRỌNG:**
- **Folder `uploads/` PHẢI COPY** (có ảnh dịch vụ, avatars...)
- **File `.htaccess` PHẢI COPY** (cấu hình URL rewrite)

---

## 🚀 CÀI ĐẶT TRÊN MÁY MỚI

### Bước 1: Yêu cầu hệ thống
```
✅ PHP >= 7.4
✅ MySQL/MariaDB >= 5.7
✅ Apache với mod_rewrite
✅ Extensions: PDO, GD, mbstring
```

### Bước 2: Copy source code
```
1. Cài XAMPP (nếu chưa có)
2. Copy folder Website_DatLich vào:
   C:\xampp\htdocs\Website_DatLich\
```

### Bước 3: Import Database
```
1. Mở phpMyAdmin trên máy mới
2. Tạo database mới: salon_booking
3. Tab "Import"
4. Choose File: salon_booking.sql
5. Click "Go"
6. ✅ Xong!
```

### Bước 4: Cấu hình config
```
Mở file: config/config.php

Kiểm tra/Sửa:
define('DB_HOST', 'localhost');          ← OK
define('DB_NAME', 'salon_booking');      ← OK
define('DB_USER', 'root');               ← OK
define('DB_PASS', '');                   ← OK với XAMPP

define('BASE_URL', 'http://localhost/Website_DatLich');  ← Check
```

### Bước 5: Set quyền folder
```
Folder uploads/ cần quyền ghi:

Windows XAMPP:
- Tự động OK (755)

Linux/Mac:
chmod -R 755 uploads/
chown -R www-data:www-data uploads/
```

### Bước 6: Test
```
Vào: http://localhost/Website_DatLich/check_system.php
→ Xem tất cả requirements OK
```

---

## 🎯 CHECKLIST HOÀN CHỈNH:

### ✅ Files phải copy:
- [x] **Toàn bộ source code** (admin, api, assets...)
- [x] **Folder uploads/** (có 11 ảnh dịch vụ)
- [x] **File .htaccess** (URL rewrite)
- [x] **File database SQL** (salon_booking.sql)

### ✅ Cấu hình phải check:
- [x] Database connection (config/config.php)
- [x] BASE_URL (http://localhost/Website_DatLich)
- [x] Folder uploads/ có quyền ghi

### ✅ Test sau khi chuyển:
- [x] http://localhost/Website_DatLich/ → Trang chủ hiển thị
- [x] Ảnh dịch vụ hiển thị đúng
- [x] Đăng nhập admin: admin@example.com / admin123
- [x] Xem danh sách dịch vụ
- [x] Đặt lịch test

---

## 📂 CẤU TRÚC FOLDER QUAN TRỌNG:

```
Website_DatLich/
├── uploads/                    ← PHẢI COPY!
│   ├── services/               ← 11 ảnh dịch vụ
│   │   ├── cattoc-1.jpg
│   │   ├── cattoc-nu-1.jpg
│   │   ├── curling-hair-man-1.jpg
│   │   └── ...
│   ├── avatars/                ← Ảnh đại diện users
│   └── images/                 ← Banner, poster
│       └── Slide-poster-1.png
├── config/
│   └── config.php              ← CHECK lại BASE_URL
├── database/
│   └── salon_booking.sql       ← File SQL để import
└── .htaccess                   ← PHẢI COPY!
```

---

## 🔧 SỬA LỖI THƯỜNG GẶP:

### Lỗi 1: Ảnh không hiển thị
```
Nguyên nhân: Chưa copy folder uploads/
Giải pháp: Copy lại folder uploads/ từ máy cũ
```

### Lỗi 2: 404 Not Found
```
Nguyên nhân: 
- Thiếu .htaccess
- mod_rewrite chưa bật

Giải pháp:
1. Copy file .htaccess
2. Bật mod_rewrite trong httpd.conf
3. Restart Apache
```

### Lỗi 3: Database connection failed
```
Nguyên nhân: Sai thông tin database
Giải pháp: Sửa config/config.php
```

### Lỗi 4: CSS/JS không load
```
Nguyên nhân: Sai BASE_URL
Giải pháp: Sửa BASE_URL trong config/config.php
```

---

## 💡 GỢI Ý TỐI ƯU:

### 1. Tạo gói đầy đủ để chuyển:
```
Nén toàn bộ thành file ZIP:
Website_DatLich.zip
├── source_code/           (toàn bộ files)
├── database.sql          (file SQL)
└── INSTALL.md            (hướng dẫn)

→ Copy file ZIP qua máy mới
→ Giải nén
→ Import SQL
→ Xong!
```

### 2. Sử dụng Git (Khuyến nghị):
```bash
# Máy cũ:
git init
git add .
git commit -m "Initial commit"
git push origin main

# Máy mới:
git clone <repository>
# Import SQL
# Done!

⚠️ Lưu ý: Thêm uploads/ vào .gitignore nếu ảnh nhiều
```

### 3. Deploy lên Production:
```
Khi lên production (server thật):

1. Đổi BASE_URL:
   define('BASE_URL', 'https://yourdomain.com');

2. Đổi database info:
   define('DB_HOST', 'production_host');
   define('DB_USER', 'production_user');
   define('DB_PASS', 'strong_password');

3. Set quyền:
   chmod 755 uploads/
   
4. Disable debug:
   error_reporting(0);
```

---

## 🎯 TÓM TẮT NHANH:

```bash
TRÊN MÁY CŨ:
1. Export database SQL
2. Copy toàn bộ folder Website_DatLich

TRÊN MÁY MỚI:
1. Copy vào htdocs/
2. Import SQL
3. Check config/config.php
4. Test: http://localhost/Website_DatLich/
5. ✅ XONG!
```

**Thời gian: ~5-10 phút**

---

## 📞 QUAN TRỌNG:

✅ **Folder uploads/ là BẮT BUỘC** - Chứa ảnh dịch vụ thực tế
✅ **File .htaccess là BẮT BUỘC** - Cấu hình URL
✅ **Database SQL match với files** - Đường dẫn ảnh đúng

**Chuyển dự án = Copy source + Import SQL + Check config!**
