# CHANGELOG - Lịch sử thay đổi

## [Version 1.0] - 2025-11-01

### ✨ Tính năng mới

#### Hệ thống chính
- Xây dựng website đặt lịch salon/spa hoàn chỉnh
- 3 vai trò: Customer, Staff, Admin
- Dashboard riêng cho từng vai trò
- Hệ thống đăng ký/đăng nhập với phân quyền

#### Khách hàng
- Xem danh sách dịch vụ với filter theo danh mục
- Đặt lịch trực tuyến (chọn dịch vụ, nhân viên, ngày giờ)
- Quản lý lịch hẹn của mình
- Hủy lịch (trước 24 giờ)
- Đánh giá dịch vụ sau khi hoàn thành
- Cập nhật thông tin cá nhân, đổi mật khẩu

#### Nhân viên
- Dashboard với thống kê cá nhân
- Xem lịch làm việc (hôm nay, sắp tới)
- Xác nhận/Hoàn thành lịch hẹn
- Xem chi tiết booking
- Auto-refresh mỗi 5 phút

#### Admin
- Dashboard tổng quan với thống kê đầy đủ
- Quản lý lịch hẹn (CRUD, update status)
- Quản lý dịch vụ (CRUD, upload ảnh)
- Quản lý danh mục dịch vụ
- Quản lý nhân viên
- Quản lý khách hàng
- Quản lý khuyến mãi (mã giảm giá)
- Báo cáo thống kê (ngày/tháng/năm)
- Cài đặt hệ thống

#### UI/UX
- Responsive design với Bootstrap 5
- Theme sáng, giao diện hiện đại
- Staff navbar: nền trắng, dropdown user menu
- Admin navbar: notification thông minh, user dropdown
- Icons Font Awesome 6
- Modal với thiết kế đẹp
- Star rating cho đánh giá

#### Email Notifications
- Email xác nhận đặt lịch
- Email xác nhận lịch hẹn
- Email hủy lịch
- Email nhắc nhở
- HTML template đẹp

### 🔧 Cải thiện

#### Performance
- Auto-refresh dashboard (5 phút)
- AJAX status updates không reload
- Pagination cho danh sách

#### Code Quality
- Null safety cho tất cả number_format
- Flash message system nhất quán
- Models theo chuẩn MVC
- Functions helper tập trung

#### Database
- Foreign keys đầy đủ
- Indexes tối ưu
- Constraints validation

### 🐛 Bug Fixes

#### UI/UX
- Fix dropdown bị chìm dưới alert (z-index)
- Fix navbar staff: xóa badge "STAFF", dropdown rõ ràng
- Fix admin bookings: di chuyển modal ra ngoài table
- Fix null warnings ở staff dashboard statistics

#### Structure
- Fix promotions.php: sửa lại structure admin chuẩn
- Fix HTML invalid: modal trong `<tr>`
- Fix duplicate user dropdown trong sidebar

#### Data
- Fix formatCurrency với null values
- Fix number_format deprecated warnings (PHP 8.1+)
- Fix staff rating và total_bookings null

### 📁 Files

#### Đã tạo mới
- `models/Promotion.php` - Quản lý khuyến mãi
- `models/Review.php` - Đánh giá dịch vụ
- `admin/promotions.php` - UI quản lý khuyến mãi
- `config/Email.php` - Email helper
- `api/staff/update-booking-status.php` - API cập nhật status

#### Đã cập nhật
- `admin/includes/navbar.php` - Rebuild toàn bộ
- `admin/includes/sidebar.php` - Xóa duplicate, thêm footer
- `admin/bookings.php` - Sửa modal structure
- `admin/staff.php` - Null safety
- `staff/dashboard.php` - UI cải thiện, navbar sáng
- `staff/booking-detail.php` - Navbar nhất quán
- `pages/my-bookings.php` - Thêm review modal
- `config/functions.php` - Fix formatCurrency null

#### Đã xóa
- `pages/my-bookings-old-backup.php`
- `staff/dashboard-old.php`
- `database/insert_sample_services.sql`

### 🎨 Design System

#### Colors
- Primary: #0d6efd (Blue)
- Success: #198754 (Green)
- Warning: #ffc107 (Yellow)
- Danger: #dc3545 (Red)
- Info: #0dcaf0 (Cyan)

#### Components
- Cards với shadow-sm
- Badges với bg-{color}
- Buttons với outline variants
- Modals với modal-lg
- Tables hover effect

### 📊 Database Schema

#### Tables
- users (customer, staff, admin)
- services
- categories
- bookings
- staff
- promotions
- reviews

### 🚀 Deployment

#### Requirements
- PHP >= 7.4
- MySQL >= 5.7
- Apache with mod_rewrite
- Extensions: PDO, GD, mbstring

#### Installation
1. Import `database/salon_booking.sql`
2. Cấu hình `config/config.php`
3. Tạo folders: uploads/images, uploads/avatars, uploads/services
4. Set permissions 755

---

## Ghi chú
- Phiên bản đầu tiên hoàn chỉnh
- Đã test trên XAMPP (Windows)
- Chuẩn bị cho production
