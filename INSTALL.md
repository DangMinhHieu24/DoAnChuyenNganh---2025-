# 🚀 HƯỚNG DẪN CÀI ĐẶT NHANH

## Bước 1: Cài đặt XAMPP
1. Tải XAMPP từ: https://www.apachefriends.org
2. Cài đặt vào `C:\xampp`
3. Khởi động **Apache** và **MySQL** từ XAMPP Control Panel

## Bước 2: Tạo Database
1. Mở trình duyệt, truy cập: `http://localhost/phpmyadmin`
2. Click "New" để tạo database mới
3. Tên database: `salon_booking`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

## Bước 3: Import Database
1. Click vào database `salon_booking` vừa tạo
2. Chọn tab "Import"
3. Click "Choose File" và chọn file `database/salon_booking.sql`
4. Click "Go" để import
5. Đợi đến khi thấy thông báo "Import has been successfully finished"

## Bước 4: Copy Files
1. Copy toàn bộ thư mục `Website_DatLich` vào `C:\xampp\htdocs\`
2. Đường dẫn cuối cùng: `C:\xampp\htdocs\Website_DatLich\`

## Bước 5: Tạo Thư Mục Uploads
1. Vào thư mục `C:\xampp\htdocs\Website_DatLich\`
2. Tạo thư mục `uploads`
3. Trong thư mục `uploads`, tạo các thư mục con:
   - `images`
   - `avatars`
   - `services`

## Bước 6: Cấu Hình (Tùy chọn)
Nếu bạn thay đổi tên database hoặc mật khẩu MySQL:

1. Mở file `config/database.php`
2. Sửa thông tin kết nối:
```php
private $host = "localhost";
private $db_name = "salon_booking";  // Tên database
private $username = "root";          // Username MySQL
private $password = "";              // Password MySQL (mặc định trống)
```

## Bước 7: Chạy Website
1. Mở trình duyệt
2. Truy cập: `http://localhost/Website_DatLich`
3. Website sẽ hiển thị trang chủ

## Bước 8: Đăng Nhập
Sử dụng các tài khoản demo:

**⚠️ LƯU Ý:** Tất cả tài khoản đều dùng password: **`admin123`**

### 🔐 Admin (Quản trị viên)
- **URL:** http://localhost/Website_DatLich/auth/login.php
- **Username:** `adminHieu`
- **Email:** dminhhieu2408@gmail.com
- **Password:** `admin123`
- **Họ tên:** Đặng Minh Hiếu
- **Quyền:** Toàn quyền quản trị hệ thống

### 👥 Khách hàng
**Khách hàng 1:**
- **Username:** `Dangthiminhngoc`
- **Email:** DangThiMinhNgoc@gmail.com
- **Password:** `admin123`
- **Họ tên:** Đặng Thị Minh Ngọc

**Khách hàng 2:**
- **Username:** `Nguyenthikimngan`
- **Email:** NguyenThiKimNgan@gmail.com
- **Password:** `admin123`
- **Họ tên:** Nguyễn Thị Kim Ngân

### 💼 Nhân viên
**Nhân viên 1:**
- **Username:** `Lethichau`
- **Email:** LeThiChau@gmail.com
- **Password:** `admin123`
- **Họ tên:** Lê Thị Châu

**Nhân viên 2:**
- **Username:** `Phamvanduoc`
- **Email:** PhamVanDuoc@gmail.com
- **Password:** `admin123`
- **Họ tên:** Phạm Văn Được

**Nhân viên 3:**
- **Username:** `Hoangthiem`
- **Email:** HoangThiEm@gmail.com
- **Password:** `admin123`
- **Họ tên:** Hoàng Thị Em

**💡 Mẹo:** Có thể đăng nhập bằng username HOẶC email

## ✅ Kiểm Tra Cài Đặt

### Kiểm tra kết nối database:
- Truy cập: `http://localhost/Website_DatLich`
- Nếu thấy trang chủ → Thành công ✅
- Nếu báo lỗi kết nối → Kiểm tra lại MySQL và database

### Kiểm tra đăng nhập:
- Truy cập: `http://localhost/Website_DatLich/auth/login.php`
- Đăng nhập bằng tài khoản admin
- Nếu vào được dashboard → Thành công ✅

### Kiểm tra đặt lịch:
- Đăng nhập bằng tài khoản customer1
- Click "Đặt lịch"
- Chọn dịch vụ, nhân viên, ngày giờ
- Nếu đặt lịch thành công → Thành công ✅

## 🐛 Xử Lý Lỗi

### Lỗi: "Access denied for user 'root'@'localhost'"
**Nguyên nhân:** Sai username/password MySQL
**Giải pháp:** 
- Mở `config/database.php`
- Kiểm tra lại username và password
- Mặc định XAMPP: username=`root`, password=`` (trống)

### Lỗi: "Unknown database 'salon_booking'"
**Nguyên nhân:** Chưa tạo database hoặc sai tên
**Giải pháp:**
- Vào phpMyAdmin
- Tạo database tên `salon_booking`
- Import lại file SQL

### Lỗi: "404 Not Found"
**Nguyên nhân:** Sai đường dẫn hoặc Apache chưa chạy
**Giải pháp:**
- Kiểm tra Apache đã chạy trong XAMPP
- Kiểm tra đường dẫn: `http://localhost/Website_DatLich`
- Đảm bảo thư mục nằm trong `htdocs`

### Lỗi: Không upload được ảnh
**Nguyên nhân:** Chưa tạo thư mục uploads
**Giải pháp:**
- Tạo thư mục `uploads` trong thư mục gốc
- Tạo các thư mục con: `images`, `avatars`, `services`

### Lỗi: Không hiển thị ảnh
**Nguyên nhân:** Chưa có ảnh mặc định
**Giải pháp:**
- Tạo file ảnh mặc định trong `assets/images/`
- Hoặc upload ảnh mới từ admin panel

## 📞 Hỗ Trợ

Nếu gặp vấn đề:
1. Kiểm tra lại từng bước
2. Đảm bảo Apache và MySQL đang chạy
3. Xem log lỗi trong XAMPP Control Panel
4. Kiểm tra file `config/database.php`

## 🎉 Hoàn Thành!

Website đã sẵn sàng sử dụng!

**Các tính năng chính:**
- ✅ Đăng ký/Đăng nhập
- ✅ Xem dịch vụ
- ✅ Đặt lịch online
- ✅ Quản lý lịch hẹn
- ✅ Admin dashboard
- ✅ Quản lý dịch vụ, nhân viên, khách hàng
- ✅ Báo cáo thống kê

**Bắt đầu sử dụng:**
1. Đăng nhập admin: `http://localhost/Website_DatLich/auth/login.php`
2. Thêm dịch vụ mới
3. Thêm nhân viên
4. Khách hàng có thể đặt lịch ngay!

---
**Chúc bạn sử dụng thành công! 🎊**
