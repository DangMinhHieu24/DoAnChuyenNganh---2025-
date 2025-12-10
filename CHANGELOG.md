# 📝 Lịch sử thay đổi - eBooking Salon

Tất cả các thay đổi quan trọng của dự án sẽ được ghi lại trong file này.

Định dạng dựa trên [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
và dự án tuân theo [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Chưa phát hành]

### Tính năng đang lên kế hoạch
- [ ] Hệ thống xoay vòng API key
- [ ] Cache response cho AI
- [ ] Thông báo qua Email
- [ ] Thông báo qua SMS
- [ ] Hỗ trợ đa ngôn ngữ
- [ ] Ứng dụng di động (React Native)
- [ ] Tích hợp cổng thanh toán (MoMo, ZaloPay, VNPay)
- [ ] Chương trình khách hàng thân thiết
- [ ] Thẻ quà tặng
- [ ] Theo dõi hoa hồng nhân viên

---

## [1.0.0] - 2025-12-10

### 🎉 Phiên bản đầu tiên

#### Đã thêm - Tính năng cốt lõi
- ✅ Hệ thống xác thực người dùng (Đăng nhập/Đăng ký/Đăng xuất)
- ✅ Phân quyền theo vai trò (Admin/Nhân viên/Khách hàng)
- ✅ Quản lý dịch vụ (Thêm/Sửa/Xóa/Xem)
- ✅ Quản lý danh mục
- ✅ Quản lý nhân viên
- ✅ Quản lý khách hàng
- ✅ Hệ thống đặt lịch với chọn khung giờ
- ✅ Theo dõi trạng thái lịch hẹn (chờ/xác nhận/hoàn thành/hủy)
- ✅ Hệ thống đánh giá và xếp hạng
- ✅ Hệ thống khuyến mãi và mã giảm giá
- ✅ Dashboard với thống kê
- ✅ Báo cáo và phân tích

#### Đã thêm - Tính năng AI
- ✅ **Chatbot AI** (Google Gemini 2.5 Flash)
  - Nhận diện ý định (8 loại)
  - Phản hồi theo ngữ cảnh
  - Thông tin dịch vụ
  - Thông tin nhân viên
  - Hỗ trợ đặt lịch
  - Thông tin giờ làm việc
  - Thông tin liên hệ

- ✅ **AI Hair Consultant** (Gemini Vision)
  - Phân tích hình dạng khuôn mặt
  - Gợi ý kiểu tóc (3-4 kiểu)
  - Đề xuất dịch vụ
  - Hướng dẫn chăm sóc
  - Upload ảnh (JPG/PNG/WEBP, tối đa 5MB)

- ✅ **AI Report Analysis** (Gemini)
  - Phân tích doanh thu
  - Thống kê lịch hẹn
  - Tính toán tỷ lệ tăng trưởng
  - Phân tích tỷ lệ hủy lịch
  - Xác định dịch vụ phổ biến
  - Hiệu suất nhân viên
  - Gợi ý hành động (3-5 gợi ý)
  - Dự báo xu hướng

#### Đã thêm - Database
- ✅ 13 bảng với quan hệ đầy đủ
- ✅ 3 views cho thống kê
- ✅ 2 stored procedures
- ✅ 3 triggers tự động hóa
- ✅ Indexes tối ưu hiệu suất
- ✅ Dữ liệu mẫu để test

#### Đã thêm - Tài liệu
- ✅ README.md - Tổng quan dự án
- ✅ SETUP_GUIDE.md - Hướng dẫn cài đặt
- ✅ AI_FEATURES.md - Tài liệu tính năng AI
- ✅ API_DOCUMENTATION.md - Tài liệu API endpoints
- ✅ DATABASE_SCHEMA.md - Cấu trúc database
- ✅ DEPLOYMENT_GUIDE.md - Hướng dẫn triển khai
- ✅ CHANGELOG.md - Lịch sử phiên bản

#### Đã thêm - Bảo mật
- ✅ Mã hóa mật khẩu (bcrypt)
- ✅ Phòng chống SQL injection (PDO prepared statements)
- ✅ Phòng chống XSS (htmlspecialchars)
- ✅ Bảo vệ CSRF
- ✅ Quản lý Session
- ✅ Kiểm tra dữ liệu đầu vào
- ✅ Kiểm tra file upload

#### Đã thêm - Giao diện UI/UX
- ✅ Thiết kế responsive (Bootstrap 5.3)
- ✅ Giao diện gradient hiện đại
- ✅ Hiệu ứng mượt mà
- ✅ Trạng thái loading
- ✅ Xử lý lỗi
- ✅ Thông báo thành công
- ✅ Hộp thoại modal
- ✅ Widget chatbot

#### Công nghệ sử dụng
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB 10.2+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework**: Bootstrap 5.3
- **Libraries**: jQuery 3.6, Font Awesome 6
- **AI Engine**: Google Gemini 2.5 Flash API
- **Version Control**: Git

---

## [0.9.0] - 2025-12-08

### Đã thêm
- Cấu trúc dự án ban đầu
- Các thao tác CRUD cơ bản
- Thiết kế schema database
- Giao diện admin panel

### Đã thay đổi
- Chuyển từ Gemini 2.0 sang Gemini 2.5 Flash
- Gộp các model AI (3 models → 1 model)

### Đã sửa
- Vấn đề quota API
- Tương thích phiên bản model

---

## [0.8.0] - 2025-12-05

### Đã thêm
- Tính năng AI phân tích báo cáo
- Dashboard báo cáo
- Tính toán thống kê

### Đã sửa
- Vấn đề xác thực trong AI API
- Cắt ngắn văn bản trong phân tích
- Vấn đề hiển thị CSS

---

## [0.7.0] - 2025-12-03

### Đã thêm
- Tính năng AI tư vấn kiểu tóc
- Chức năng upload ảnh
- Phân tích khuôn mặt với Gemini Vision

### Đã sửa
- Cắt ngắn response (tăng maxOutputTokens lên 4096)
- Vấn đề chặn nội dung (thêm safety settings)
- Cải thiện xử lý lỗi

---

## [0.6.0] - 2025-12-01

### Đã thêm
- Tính năng Chatbot AI
- Hệ thống nhận diện ý định
- Phản hồi theo ngữ cảnh

### Đã thay đổi
- Cải thiện prompt chatbot
- Nâng cao thông báo lỗi

---

## [0.5.0] - 2025-11-28

### Đã thêm
- Hệ thống đặt lịch
- Chọn khung giờ
- Kiểm tra nhân viên rảnh

### Đã sửa
- Phòng chống đặt trùng lịch
- Tính toán khung giờ

---

## [0.4.0] - 2025-11-25

### Đã thêm
- Hệ thống đánh giá và xếp hạng
- Quản lý khuyến mãi
- Áp dụng mã giảm giá

---

## [0.3.0] - 2025-11-20

### Đã thêm
- Quản lý nhân viên
- Phân công dịch vụ
- Quản lý giờ làm việc

---

## [0.2.0] - 2025-11-15

### Đã thêm
- Quản lý dịch vụ
- Quản lý danh mục
- Upload ảnh cho dịch vụ

---

## [0.1.0] - 2025-11-10

### Đã thêm
- Xác thực người dùng
- Phân quyền theo vai trò
- Admin panel cơ bản

---

## Tóm tắt lịch sử phiên bản

| Phiên bản | Ngày | Mô tả |
|---------|------|-------------|
| 1.0.0 | 2025-12-10 | Phiên bản đầu tiên với đầy đủ tính năng |
| 0.9.0 | 2025-12-08 | Cấu trúc dự án và CRUD cơ bản |
| 0.8.0 | 2025-12-05 | AI Report Analysis |
| 0.7.0 | 2025-12-03 | AI Hair Consultant |
| 0.6.0 | 2025-12-01 | Chatbot AI |
| 0.5.0 | 2025-11-28 | Hệ thống đặt lịch |
| 0.4.0 | 2025-11-25 | Đánh giá và khuyến mãi |
| 0.3.0 | 2025-11-20 | Quản lý nhân viên |
| 0.2.0 | 2025-11-15 | Quản lý dịch vụ |
| 0.1.0 | 2025-11-10 | Xác thực người dùng |

---

## Thay đổi quan trọng (Breaking Changes)

### v1.0.0
- **Thay đổi AI Model**: Chuyển từ nhiều models sang 1 model `gemini-2.5-flash`
  - **Ảnh hưởng**: Cấu hình API cũ cần cập nhật
  - **Nâng cấp**: Cập nhật `config/chatbot-config.php` với tên model mới

- **Phiên bản API**: Đổi từ `v1` sang `v1beta`
  - **Ảnh hưởng**: API endpoints đã thay đổi
  - **Nâng cấp**: Cập nhật tất cả API calls sang v1beta

---

## Tính năng không còn sử dụng (Deprecated)

### v1.0.0
- ❌ `GEMINI_CHATBOT_MODEL_OLD` - Dùng `GEMINI_MODEL` thay thế
- ❌ `GEMINI_HAIR_MODEL_OLD` - Dùng `GEMINI_MODEL` thay thế
- ❌ `GEMINI_REPORT_MODEL_OLD` - Dùng `GEMINI_MODEL` thay thế

---

## Vấn đề đã biết

### v1.0.0
- ⚠️ Giới hạn quota AI (15 RPM, 1,500 RPD)
  - **Giải pháp**: Triển khai rate limiting hoặc dùng nhiều API keys
  
- ⚠️ Upload ảnh lớn có thể timeout
  - **Giải pháp**: Nén ảnh trước khi upload hoặc tăng PHP timeout

- ⚠️ Thông báo Email chưa được triển khai
  - **Trạng thái**: Dự kiến trong v1.1.0

---

## Hướng dẫn nâng cấp

### Từ 0.9.0 lên 1.0.0

#### 1. Cập nhật file Config
```php
// OLD (config/chatbot-config.php)
define('GEMINI_CHATBOT_MODEL', 'gemini-2.0-flash-exp');
define('GEMINI_HAIR_MODEL', 'gemini-2.0-flash-exp');
define('GEMINI_REPORT_MODEL', 'gemini-2.5-pro');

// NEW
define('GEMINI_MODEL', 'gemini-2.5-flash');
define('GEMINI_CHATBOT_MODEL', GEMINI_MODEL);
define('GEMINI_HAIR_MODEL', GEMINI_MODEL);
define('GEMINI_REPORT_MODEL', GEMINI_MODEL);
```

#### 2. Cập nhật phiên bản API
```php
// OLD
define('GEMINI_API_VERSION', 'v1');

// NEW
define('GEMINI_API_VERSION', 'v1beta');
```

#### 3. Cập nhật Database
```sql
-- Không cần thay đổi database
```

#### 4. Xóa Cache
```bash
# Clear PHP OPcache
php -r "opcache_reset();"

# Clear browser cache
# Ctrl + Shift + Delete
```

---

## Lộ trình phát triển

### v1.1.0 (Quý 1/2026)
- [ ] Thông báo Email (xác nhận đặt lịch, nhắc nhở)
- [ ] Thông báo SMS
- [ ] Hệ thống xoay vòng API key
- [ ] Cache response cho AI
- [ ] Cải thiện hiệu suất

### v1.2.0 (Quý 2/2026)
- [ ] Tích hợp cổng thanh toán
- [ ] Hỗ trợ đa ngôn ngữ (Tiếng Anh, Tiếng Việt)
- [ ] Cải thiện responsive mobile
- [ ] Hỗ trợ PWA

### v1.3.0 (Quý 3/2026)
- [ ] Ứng dụng di động (React Native)
- [ ] Thông báo đẩy (Push notifications)
- [ ] Chương trình khách hàng thân thiết
- [ ] Thẻ quà tặng

### v2.0.0 (Quý 4/2026)
- [ ] Kiến trúc Microservices
- [ ] GraphQL API
- [ ] Chat thời gian thực
- [ ] Tư vấn qua video
- [ ] Tối ưu lịch hẹn bằng AI

---

## Người đóng góp

### v1.0.0
- **Đặng Minh Hiếu** (@dminhhieu2408) - Lập trình viên chính
  - Phát triển tính năng cốt lõi
  - Tích hợp AI
  - Thiết kế database
  - Viết tài liệu

---

## Lời cảm ơn

### Công nghệ sử dụng
- **Google Gemini AI** - AI Engine
- **Bootstrap** - UI Framework
- **Font Awesome** - Icons
- **jQuery** - JavaScript Library

### Nguồn cảm hứng
- Hệ thống đặt lịch salon hiện đại
- Dịch vụ khách hàng tích hợp AI
- Nhu cầu thị trường Việt Nam

---

## Bản quyền

Dự án này được phát triển cho mục đích học tập và thương mại.

Bản quyền © 2025 Đặng Minh Hiếu. Giữ toàn quyền.

---

## Liên hệ

- **Lập trình viên**: Đặng Minh Hiếu
- **Email**: dminhhieu2408@gmail.com
- **Phone**: 0976985305
- **Địa chỉ**: 162 ABC, Phường 5, TP Trà Vinh

---

## Liên kết

- **Repository**: [GitHub](https://github.com/yourusername/Website_DatLich)
- **Tài liệu**: [Docs](https://yourdomain.com/docs)
- **Demo**: [Live Demo](https://yourdomain.com)
- **Báo lỗi**: [GitHub Issues](https://github.com/yourusername/Website_DatLich/issues)

---

**Cập nhật lần cuối**: 10 tháng 12, 2025

---

## Ghi chú

### Quy tắc đánh số phiên bản
```
MAJOR.MINOR.PATCH

MAJOR: Thay đổi lớn (không tương thích ngược)
MINOR: Tính năng mới (tương thích ngược)
PATCH: Sửa lỗi (tương thích ngược)
```

### Chu kỳ phát hành
- **Phiên bản lớn (Major)**: Hàng năm
- **Phiên bản nhỏ (Minor)**: Hàng quý
- **Bản vá lỗi (Patch)**: Khi cần thiết

### Chính sách hỗ trợ
- **Phiên bản hiện tại (1.0.x)**: Hỗ trợ đầy đủ
- **Phiên bản trước (0.x)**: Chỉ sửa lỗi bảo mật
- **Phiên bản cũ hơn**: Không hỗ trợ

---

[Unreleased]: https://github.com/yourusername/Website_DatLich/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/yourusername/Website_DatLich/releases/tag/v1.0.0
[0.9.0]: https://github.com/yourusername/Website_DatLich/releases/tag/v0.9.0
[0.8.0]: https://github.com/yourusername/Website_DatLich/releases/tag/v0.8.0
[0.7.0]: https://github.com/yourusername/Website_DatLich/releases/tag/v0.7.0
[0.6.0]: https://github.com/yourusername/Website_DatLich/releases/tag/v0.6.0
[0.5.0]: https://github.com/yourusername/Website_DatLich/releases/tag/v0.5.0
[0.4.0]: https://github.com/yourusername/Website_DatLich/releases/tag/v0.4.0
[0.3.0]: https://github.com/yourusername/Website_DatLich/releases/tag/v0.3.0
[0.2.0]: https://github.com/yourusername/Website_DatLich/releases/tag/v0.2.0
[0.1.0]: https://github.com/yourusername/Website_DatLich/releases/tag/v0.1.0
