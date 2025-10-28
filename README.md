# 🎨 HỆ THỐNG ĐẶT LỊCH SALON/SPA ONLINE

Hệ thống đặt lịch làm đẹp trực tuyến hoàn chỉnh được xây dựng bằng PHP, MySQL và Bootstrap 5.

## 📋 MỤC LỤC
- [Tính năng](#tính-năng)
- [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
- [Cài đặt](#cài-đặt)
- [Cấu trúc thư mục](#cấu-trúc-thư-mục)
- [Tài khoản demo](#tài-khoản-demo)
- [Hướng dẫn sử dụng](#hướng-dẫn-sử-dụng)

## ✨ TÍNH NĂNG

### Khách hàng
- ✅ Đăng ký/Đăng nhập tài khoản
- ✅ Xem danh sách dịch vụ theo danh mục
- ✅ Tìm kiếm dịch vụ
- ✅ Đặt lịch hẹn online
- ✅ Chọn nhân viên phục vụ
- ✅ Xem lịch sử đặt lịch
- ✅ Hủy lịch hẹn
- ✅ Quản lý thông tin cá nhân
- ✅ Đổi mật khẩu

### Nhân viên
- ✅ Xem lịch làm việc
- ✅ Xem danh sách khách hàng đã đặt
- ✅ Cập nhật trạng thái lịch hẹn
- ✅ Xem thống kê cá nhân

### Quản trị viên
- ✅ Dashboard tổng quan
- ✅ Quản lý dịch vụ (CRUD)
- ✅ Quản lý danh mục dịch vụ
- ✅ Quản lý nhân viên
- ✅ Quản lý khách hàng
- ✅ Quản lý lịch hẹn
- ✅ Xác nhận/Hủy lịch hẹn
- ✅ Báo cáo thống kê
- ✅ Quản lý khuyến mãi
- ✅ Cài đặt hệ thống

### Tính năng khác
- ✅ Responsive design (Mobile-friendly)
- ✅ Kiểm tra slot thời gian trống
- ✅ Thông báo realtime
- ✅ Upload ảnh dịch vụ/avatar
- ✅ Đánh giá dịch vụ
- ✅ Hệ thống khuyến mãi
- ✅ Xuất báo cáo PDF/Excel
- ✅ Gửi email thông báo

## 💻 YÊU CẦU HỆ THỐNG

- **XAMPP** (hoặc WAMP/MAMP)
  - PHP >= 7.4
  - MySQL >= 5.7
  - Apache Server
- **Trình duyệt web** hiện đại (Chrome, Firefox, Edge, Safari)
- **Git** (tùy chọn)

## 🚀 CÀI ĐẶT

### Bước 1: Cài đặt XAMPP
1. Tải XAMPP từ [https://www.apachefriends.org](https://www.apachefriends.org)
2. Cài đặt XAMPP vào thư mục mặc định (C:\xampp)
3. Khởi động Apache và MySQL từ XAMPP Control Panel

### Bước 2: Cài đặt Database
1. Mở trình duyệt và truy cập: `http://localhost/phpmyadmin`
2. Tạo database mới tên `salon_booking`
3. Import file SQL:
   - Click vào database `salon_booking`
   - Chọn tab "Import"
   - Chọn file `database/salon_booking.sql`
   - Click "Go" để import

### Bước 3: Cấu hình ứng dụng
1. Copy toàn bộ thư mục `Website_DatLich` vào `C:\xampp\htdocs\`
2. Mở file `config/database.php` và kiểm tra cấu hình:
```php
private $host = "localhost";
private $db_name = "salon_booking";
private $username = "root";
private $password = ""; // Để trống nếu dùng XAMPP mặc định
```

3. Mở file `config/config.php` và cập nhật BASE_URL:
```php
define('BASE_URL', 'http://localhost/Website_DatLich');
```

### Bước 4: Tạo thư mục uploads
1. Tạo thư mục `uploads` trong thư mục gốc
2. Tạo các thư mục con:
   - `uploads/images/`
   - `uploads/avatars/`
   - `uploads/services/`

### Bước 5: Chạy ứng dụng
1. Mở trình duyệt
2. Truy cập: `http://localhost/Website_DatLich`
3. Đăng nhập bằng tài khoản demo (xem bên dưới)

## 📁 CẤU TRÚC THƯ MỤC

```
Website_DatLich/
│
├── admin/                  # Trang quản trị
│   ├── includes/          # Header, sidebar admin
│   ├── dashboard.php      # Dashboard admin
│   ├── bookings.php       # Quản lý lịch hẹn
│   ├── services.php       # Quản lý dịch vụ
│   ├── staff.php          # Quản lý nhân viên
│   └── ...
│
├── api/                   # API endpoints
│   ├── get-staff.php     # Lấy danh sách nhân viên
│   └── get-time-slots.php # Lấy khung giờ trống
│
├── assets/               # Tài nguyên tĩnh
│   ├── css/
│   │   └── style.css    # CSS tùy chỉnh
│   ├── js/
│   │   └── main.js      # JavaScript chính
│   └── images/          # Hình ảnh
│
├── auth/                # Xác thực
│   ├── login.php       # Đăng nhập
│   ├── register.php    # Đăng ký
│   └── logout.php      # Đăng xuất
│
├── config/             # Cấu hình
│   ├── config.php     # Cấu hình chung
│   ├── database.php   # Kết nối database
│   └── functions.php  # Hàm helper
│
├── database/          # Database
│   └── salon_booking.sql # File SQL
│
├── includes/          # Components chung
│   ├── header.php    # Header
│   └── footer.php    # Footer
│
├── models/           # Models (MVC)
│   ├── User.php     # Model User
│   ├── Service.php  # Model Service
│   ├── Booking.php  # Model Booking
│   ├── Staff.php    # Model Staff
│   └── Category.php # Model Category
│
├── pages/           # Trang người dùng
│   ├── booking.php     # Đặt lịch
│   ├── services.php    # Danh sách dịch vụ
│   ├── my-bookings.php # Lịch hẹn của tôi
│   ├── profile.php     # Thông tin cá nhân
│   └── contact.php     # Liên hệ
│
├── staff/          # Trang nhân viên
│   ├── dashboard.php    # Dashboard nhân viên
│   └── booking-detail.php # Chi tiết lịch hẹn
│
├── uploads/        # Thư mục upload
│   ├── images/
│   ├── avatars/
│   └── services/
│
├── index.php       # Trang chủ
├── .htaccess       # Apache config
└── README.md       # File này
```

## 👤 TÀI KHOẢN DEMO

**⚠️ LƯU Ý:** Tất cả tài khoản đều dùng mật khẩu: `admin123`

### Admin
- **Username:** admin
- **Password:** admin123
- **Email:** admin@salon.com
- **Quyền:** Toàn quyền quản trị hệ thống

### Khách hàng
- **Username:** customer1
- **Password:** admin123
- **Email:** customer1@gmail.com
- **Quyền:** Đặt lịch, xem lịch sử

### Nhân viên
- **Username:** staff1
- **Password:** admin123
- **Email:** staff1@salon.com
- **Quyền:** Xem lịch làm việc, cập nhật trạng thái

**💡 Mẹo:** Có thể đăng nhập bằng username HOẶC email

## 📖 HƯỚNG DẪN SỬ DỤNG

### Dành cho Khách hàng

#### 1. Đăng ký tài khoản
1. Click "Đăng ký" ở góc phải header
2. Điền đầy đủ thông tin
3. Click "Đăng ký"
4. Đăng nhập bằng tài khoản vừa tạo

#### 2. Đặt lịch hẹn
1. Click "Đặt lịch" hoặc chọn dịch vụ từ trang chủ
2. Chọn dịch vụ muốn sử dụng
3. Chọn nhân viên phục vụ
4. Chọn ngày và giờ
5. Thêm ghi chú (nếu có)
6. Click "Xác nhận đặt lịch"

#### 3. Quản lý lịch hẹn
1. Click vào avatar → "Lịch hẹn của tôi"
2. Xem danh sách lịch hẹn
3. Click "Xem" để xem chi tiết
4. Click "Hủy" để hủy lịch (trước 24h)

### Dành cho Admin

#### 1. Quản lý dịch vụ
1. Đăng nhập với tài khoản admin
2. Vào "Quản lý dịch vụ"
3. Click "Thêm dịch vụ mới"
4. Điền thông tin và upload ảnh
5. Click "Lưu"

#### 2. Quản lý lịch hẹn
1. Vào "Quản lý lịch hẹn"
2. Xem danh sách lịch hẹn
3. Click "Xác nhận" để xác nhận lịch
4. Click "Hoàn thành" khi khách đã sử dụng dịch vụ

#### 3. Quản lý nhân viên
1. Vào "Quản lý nhân viên"
2. Click "Thêm nhân viên"
3. Tạo tài khoản user với role "staff"
4. Thêm thông tin nhân viên
5. Gán dịch vụ cho nhân viên

## 🔧 CẤU HÌNH NÂNG CAO

### Thay đổi cài đặt đặt lịch
Mở file `config/config.php`:

```php
// Số ngày có thể đặt trước
define('BOOKING_ADVANCE_DAYS', 30);

// Số giờ trước khi có thể hủy
define('BOOKING_CANCEL_HOURS', 24);

// Giờ làm việc
define('WORKING_START_TIME', '08:00');
define('WORKING_END_TIME', '20:00');

// Thời gian mỗi slot (phút)
define('SLOT_DURATION', 30);
```

### Cấu hình email
Để gửi email thông báo, cấu hình SMTP trong file `config/config.php`:

```php
// SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-password');
```

## 🐛 XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi kết nối database
- Kiểm tra MySQL đã chạy trong XAMPP
- Kiểm tra thông tin kết nối trong `config/database.php`
- Đảm bảo database `salon_booking` đã được tạo

### Lỗi 404 Not Found
- Kiểm tra BASE_URL trong `config/config.php`
- Kiểm tra file `.htaccess` có tồn tại
- Bật mod_rewrite trong Apache

### Lỗi upload ảnh
- Kiểm tra thư mục `uploads/` có quyền ghi (chmod 777)
- Kiểm tra dung lượng file upload trong `php.ini`

### Lỗi session
- Kiểm tra thư mục session có quyền ghi
- Xóa cache trình duyệt
- Khởi động lại Apache

## 📝 CHANGELOG

### Version 1.0.0 (2025-10-10)
- ✅ Phát hành phiên bản đầu tiên
- ✅ Đầy đủ tính năng cơ bản
- ✅ Responsive design
- ✅ Admin dashboard
- ✅ Hệ thống đặt lịch

## 🤝 ĐÓNG GÓP

Mọi đóng góp đều được chào đón! Vui lòng:
1. Fork dự án
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit thay đổi (`git commit -m 'Add some AmazingFeature'`)
4. Push lên branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📄 GIẤY PHÉP

Dự án này được phát hành dưới giấy phép MIT. Xem file `LICENSE` để biết thêm chi tiết.

## 📞 LIÊN HỆ

- **Email:** dminhhieu240@gmail.com
- **Website:** http://localhost/Website_DatLich
- **Hotline:** 0976985305

## 🙏 LỜI CẢM ƠN

- Bootstrap 5
- Font Awesome
- PHP & MySQL
- XAMPP

---

**Chúc bạn sử dụng hệ thống thành công! 🎉**

Nếu gặp vấn đề, vui lòng tạo issue trên GitHub hoặc liên hệ qua email.
