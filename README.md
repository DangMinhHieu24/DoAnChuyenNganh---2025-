# 💈 eBooking Salon - Hệ Thống Đặt Lịch Thông Minh

Hệ thống quản lý và đặt lịch salon tóc hiện đại với tích hợp AI sử dụng Google Gemini 2.0 Flash.

## ✨ Tính Năng Chính

### 🎯 Quản Lý Salon (Admin)
- ✅ Quản lý dịch vụ theo danh mục (cắt tóc, nhuộm, uốn...)
- ✅ Quản lý nhân viên và phân công dịch vụ
- ✅ Quản lý khách hàng và lịch sử booking
- ✅ Quản lý lịch hẹn (xác nhận, hủy, hoàn thành)
- ✅ Báo cáo doanh thu và thống kê
- ✅ Quản lý khuyến mãi và settings

### 📅 Đặt Lịch Online (Customer)
- ✅ Đặt lịch trực tuyến 24/7 không cần gọi điện
- ✅ Chọn dịch vụ → Nhân viên → Ngày → Giờ
- ✅ Kiểm tra lịch trống real-time
- ✅ Xem lịch sử đặt lịch của bản thân
- ✅ Hủy/đổi lịch hẹn
- ✅ Đánh giá dịch vụ sau khi hoàn thành

### 🤖 AI Chatbot (Gemini 2.0 Flash)
- ✅ Trả lời câu hỏi tự động 24/7
- ✅ Tư vấn dịch vụ, giá cả, giờ làm việc
- ✅ Kiểm tra nhân viên trống
- ✅ **Đặt lịch tự động qua chat với conversation flow**
- ✅ Hiểu ngôn ngữ tự nhiên (tiếng Việt)
- ✅ Giao diện đẹp với gradient và animations
- ✅ Quick replies và gợi ý thông minh

### 🎨 AI Tư Vấn Kiểu Tóc (Gemini Vision)
- ✅ **Upload ảnh selfie (drag & drop)**
- ✅ **AI phân tích khuôn mặt, màu da, đặc điểm**
- ✅ **Gợi ý 3-4 kiểu tóc phù hợp nhất**
- ✅ **Giải thích chi tiết lý do phù hợp**
- ✅ **Tích hợp với hệ thống đặt lịch**
- ✅ Giao diện hiện đại với glassmorphism
- ✅ Responsive trên mọi thiết bị

## 🚀 Công Nghệ Sử Dụng

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL** - Database
- **PDO** - Database abstraction

### Frontend
- **HTML5, CSS3, JavaScript**
- **Bootstrap 5** - UI Framework
- **Font Awesome** - Icons
- **AJAX** - Async requests

### AI Integration
- **Google Gemini 2.5 Flash** - Text generation & chat (multimodal)
- **Google Gemini Vision** - Image analysis
- **REST API** - API integration

## 📦 Cài Đặt

### Yêu Cầu Hệ Thống
- **PHP 7.4+** (khuyến nghị 8.0+)
- **MySQL 5.7+** hoặc MariaDB
- **Apache/Nginx** với mod_rewrite
- **cURL extension** (cho API calls)
- **GD/Imagick** (cho xử lý ảnh)
- **Gemini API Key** (miễn phí tại Google AI Studio)

### Các Bước Cài Đặt

#### 1. Clone hoặc tải project
```bash
git clone https://github.com/DangMinhHieu24/DoAnChuyenNganh---2025-
cd Website_DatLich
```

#### 2. Import database
- Tạo database mới: `salon_booking`
- Import file SQL: `database/salon_booking.sql`
```sql
CREATE DATABASE salon_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE salon_booking;
SOURCE database/salon_booking.sql;
```

#### 3. Cấu hình database
Sửa file `config/database.php`:
```php
class Database {
    private $host = "localhost";
    private $db_name = "salon_booking";  // Tên database
    private $username = "root";           // Username MySQL
    private $password = "";               // Password MySQL
    // ...
}
```

#### 4. Cấu hình base URL
Sửa file `config/config.php`:
```php
define('BASE_URL', 'http://localhost/Website_DatLich');
define('SITE_NAME', 'eBooking');
define('SITE_EMAIL', 'dminhhieu2408@gmail.com');
define('SITE_PHONE', '0976985305');
define('SITE_ADDRESS', '162 ABC, Phường 5, TP Trà Vinh');
```

#### 5. Cấu hình Gemini API
- Truy cập: https://makersuite.google.com/app/apikey
- Đăng nhập Google và tạo API key mới
- Copy file example: `copy config\chatbot-config.example.php config\chatbot-config.php`
- Sửa file `config/chatbot-config.php`:
```php
define('GEMINI_API_KEY', 'AIzaSy...');  // Thay bằng API key của bạn
define('GEMINI_MODEL', 'gemini-2.5-flash');  // Model mới nhất
```

**Lưu ý:** File `config/chatbot-config.php` không được push lên Git (đã có trong `.gitignore`)

#### 6. Tạo thư mục uploads
```bash
mkdir uploads
chmod 777 uploads
```

#### 7. Chạy project
- **XAMPP**: Đặt vào `C:/xampp/htdocs/Website_DatLich`
- **WAMP**: Đặt vào `C:/wamp64/www/Website_DatLich`
- Truy cập: `http://localhost/Website_DatLich`

### Tài Khoản Mặc Định

**Admin:**
- Email: `admin@salon.com`
- Password: `admin123`
- Quyền: Quản lý toàn bộ hệ thống

**Nhân viên:**
- Email: `staff@salon.com`
- Password: `staff123`
- Quyền: Xem và quản lý lịch hẹn của mình

**Khách hàng:**
- Đăng ký tài khoản mới tại trang Register

## 📖 Hướng Dẫn Sử Dụng

### Cho Khách Hàng

1. **Đặt lịch thông thường:**
   - Vào trang "Đặt lịch"
   - Chọn dịch vụ → Nhân viên → Ngày → Giờ
   - Xác nhận đặt lịch

2. **Đặt lịch qua AI Chatbot:**
   - Click icon chat ở góc phải màn hình
   - Nói với AI: "Tôi muốn đặt lịch cắt tóc"
   - AI sẽ hướng dẫn từng bước

3. **Tư vấn kiểu tóc với AI:**
   - Vào menu "AI Tư Vấn"
   - Upload ảnh selfie rõ mặt
   - Nhận gợi ý kiểu tóc phù hợp
   - Đặt lịch ngay nếu thích

### Cho Admin

1. **Quản lý dịch vụ:**
   - Vào Admin → Dịch vụ
   - Thêm/sửa/xóa dịch vụ

2. **Quản lý nhân viên:**
   - Vào Admin → Nhân viên
   - Thêm nhân viên mới
   - Phân công dịch vụ

3. **Xem báo cáo:**
   - Vào Admin → Báo cáo
   - Xem doanh thu, booking, khách hàng

## 🤖 Chi Tiết Tính Năng AI

### 1. AI Chatbot
**File liên quan:**
- `api/chatbot.php` - API xử lý chat
- `api/chatbot-booking.php` - API đặt lịch qua chat
- `assets/js/chatbot.js` - Frontend logic
- `assets/css/chatbot.css` - Styling
- `config/chatbot-config.php` - Cấu hình

**Khả năng:**
- Trả lời câu hỏi về dịch vụ, giá cả
- Tìm kiếm nhân viên trống
- Đặt lịch tự động với conversation flow
- Hiểu ngôn ngữ tự nhiên
- Gợi ý quick replies

**Cách hoạt động:**
1. User gửi tin nhắn
2. Gửi đến Gemini API để phân tích intent
3. Nếu là booking → chuyển sang flow đặt lịch
4. Nếu là câu hỏi → trả lời từ database + AI
5. Hiển thị kết quả với UI đẹp

### 2. AI Tư Vấn Kiểu Tóc
**File liên quan:**
- `pages/ai-hair-consultant.php` - Trang chính
- `api/ai-hair-consultant.php` - API xử lý ảnh
- `assets/js/ai-hair-consultant.js` - Upload & display
- `assets/css/ai-hair-consultant.css` - Styling hiện đại

**Khả năng:**
- Upload ảnh (drag & drop hoặc click)
- Phân tích khuôn mặt với Gemini Vision
- Gợi ý 3-4 kiểu tóc phù hợp
- Giải thích chi tiết lý do
- Link đến booking

**Cách hoạt động:**
1. User upload ảnh selfie
2. Validate file (type, size)
3. Convert ảnh sang base64
4. Gửi đến Gemini Vision API với prompt
5. AI phân tích: khuôn mặt, màu da, đặc điểm
6. Trả về gợi ý kiểu tóc với lý do
7. Hiển thị kết quả đẹp mắt

**Prompt Engineering:**
- Phân tích khuôn mặt (hình dạng, tỷ lệ)
- Xác định màu da
- Gợi ý kiểu tóc phù hợp
- Giải thích lý do cụ thể
- Liên kết với dịch vụ salon

## 🎨 Giao Diện

- **Responsive Design** - Hoạt động tốt trên mọi thiết bị
- **Modern UI** - Gradient, glassmorphism, animations
- **User-Friendly** - Dễ sử dụng, trực quan
- **Fast Loading** - Tối ưu performance

## 📱 Tương Thích

- ✅ Desktop (Windows, Mac, Linux)
- ✅ Tablet (iPad, Android)
- ✅ Mobile (iOS, Android)
- ✅ Browsers: Chrome, Firefox, Safari, Edge

## � eBảo Mật

- Password hashing với `password_hash()`
- Prepared statements (PDO) chống SQL Injection
- XSS protection
- CSRF protection
- Session security
- Input validation

## 📊 Cấu Trúc Thư Mục

```
Website_DatLich/
├── admin/                          # Trang quản trị (Admin Panel)
│   ├── bookings.php               # Quản lý lịch hẹn
│   ├── categories.php             # Quản lý danh mục dịch vụ
│   ├── customers.php              # Quản lý khách hàng
│   ├── dashboard.php              # Dashboard thống kê
│   ├── promotions.php             # Quản lý khuyến mãi
│   ├── reports.php                # Báo cáo doanh thu
│   ├── services.php               # Quản lý dịch vụ
│   ├── settings.php               # Cài đặt hệ thống
│   ├── staff.php                  # Quản lý nhân viên
│   └── includes/                  # Header, sidebar admin
│
├── api/                            # API Endpoints
│   ├── chatbot.php                # API chatbot chính
│   ├── chatbot-booking.php        # API đặt lịch qua chat
│   ├── chatbot-actions.php        # Actions bổ sung
│   ├── ai-hair-consultant.php     # API tư vấn kiểu tóc
│   ├── get-staff.php              # Lấy danh sách nhân viên
│   ├── get-time-slots.php         # Lấy giờ trống
│   └── staff/                     # API cho nhân viên
│
├── assets/                         # Static Resources
│   ├── css/
│   │   ├── style.css              # CSS chính
│   │   ├── chatbot.css            # CSS chatbot
│   │   └── ai-hair-consultant.css # CSS tư vấn kiểu tóc
│   ├── js/
│   │   ├── main.js                # JavaScript chính
│   │   ├── chatbot.js             # Logic chatbot
│   │   └── ai-hair-consultant.js  # Logic tư vấn kiểu tóc
│   └── images/                    # Hình ảnh tĩnh
│
├── auth/                           # Authentication
│   ├── login.php                  # Đăng nhập
│   ├── register.php               # Đăng ký
│   └── logout.php                 # Đăng xuất
│
├── config/                         # Configuration
│   ├── config.php                 # Cấu hình chính
│   ├── database.php               # Kết nối database
│   ├── functions.php              # Helper functions
│   └── chatbot-config.php         # Cấu hình AI (Gemini)
│
├── database/                       # Database
│   └── salon_booking.sql          # SQL schema & data
│
├── includes/                       # Shared Components
│   ├── header.php                 # Header chung
│   ├── footer.php                 # Footer chung
│   └── chatbot-widget.php         # Widget chatbot
│
├── models/                         # PHP Classes (OOP)
│   ├── Booking.php                # Model đặt lịch
│   ├── Category.php               # Model danh mục
│   ├── Promotion.php              # Model khuyến mãi
│   ├── Review.php                 # Model đánh giá
│   ├── Service.php                # Model dịch vụ
│   ├── Staff.php                  # Model nhân viên
│   └── User.php                   # Model người dùng
│
├── pages/                          # User Pages
│   ├── booking.php                # Trang đặt lịch
│   ├── services.php               # Danh sách dịch vụ
│   ├── contact.php                # Liên hệ
│   ├── profile.php                # Trang cá nhân
│   ├── my-bookings.php            # Lịch hẹn của tôi
│   ├── change-password.php        # Đổi mật khẩu
│   └── ai-hair-consultant.php     # AI tư vấn kiểu tóc
│
├── staff/                          # Staff Panel
│   └── dashboard.php              # Dashboard nhân viên
│
├── uploads/                        # User Uploads
│   ├── avatars/                   # Avatar người dùng
│   ├── services/                  # Hình dịch vụ
│   └── temp/                      # File tạm
│
├── .htaccess                       # Apache config
├── index.php                       # Trang chủ
├── README.md                       # Documentation chính
├── CHATBOT_README.md              # Hướng dẫn Chatbot
└── AI_HAIR_CONSULTANT_README.md   # Hướng dẫn AI Tư Vấn
```

## 🐛 Troubleshooting

### Lỗi Kết Nối Database
```
Error: "Lỗi kết nối database"
Fix: 
- Kiểm tra MySQL đã chạy chưa
- Kiểm tra username/password trong config/database.php
- Kiểm tra tên database đã tạo chưa
```

### Lỗi Chatbot Không Hoạt Động
```
Error: "API returned null" hoặc "Lỗi kết nối API"
Fix:
- Kiểm tra GEMINI_API_KEY trong config/chatbot-config.php
- Verify API key còn quota (15 requests/phút, 1500 requests/ngày)
- Nếu hết quota: Đợi reset (7:00 sáng) hoặc tạo API key mới
- Kiểm tra cURL extension đã enable
- Restart Apache sau khi sửa config
```

### Lỗi 429 - Quota Exceeded
```
Error: "You exceeded your current quota"
Fix:
- API key đã hết quota miễn phí
- Giải pháp 1: Đợi đến 7:00 sáng hôm sau (quota reset)
- Giải pháp 2: Tạo API key mới tại https://makersuite.google.com/app/apikey
- Giải pháp 3: Upgrade lên paid plan ($0.075/1M tokens)
```

### Lỗi Upload Ảnh
```
Error: "Failed to upload"
Fix:
- Kiểm tra thư mục uploads/ có quyền write (chmod 777)
- Kiểm tra php.ini: upload_max_filesize, post_max_size
```

### Lỗi Session
```
Error: "Session expired"
Fix:
- Kiểm tra session_start() trong các file
- Clear browser cookies
- Kiểm tra session.save_path trong php.ini
```

## 📚 Documentation

- **README.md** - Tài liệu chính (file này)
- **CHATBOT_README.md** - Hướng dẫn chi tiết về AI Chatbot
- **AI_HAIR_CONSULTANT_README.md** - Hướng dẫn chi tiết về AI Tư Vấn Kiểu Tóc

## 🆘 Hỗ Trợ

**Thông tin salon:**
- 📍 Địa chỉ: 162 ABC, Phường 5, TP Trà Vinh
- 📞 Điện thoại: 0976985305
- 📧 Email: dminhhieu2408@gmail.com
- 🌐 Website: http://localhost/Website_DatLich

**Hỗ trợ kỹ thuật:**
- Mở issue trên GitHub
- Email: dminhhieu2408@gmail.com

## 📝 License

MIT License - Free to use for educational purposes

## 🙏 Credits

**AI & APIs:**
- **Google Gemini 2.5 Flash** - Text generation & chat (multimodal)
- **Google Gemini Vision** - Image analysis
- **Google AI Studio** - API key management
- **Gemini API v1** - REST API endpoint

**Frontend:**
- **Bootstrap 5** - UI Framework
- **Font Awesome 6** - Icons
- **Unsplash** - Stock images

**Backend:**
- **PHP** - Server-side language
- **MySQL** - Database
- **PDO** - Database abstraction

## 👨‍💻 Developer

**Phát triển bởi:** Đặng Minh Hiếu  
**Email:** dminhhieu2408@gmail.com  
**Phiên bản:** 2.1.0  
**Cập nhật:** December 7, 2025

## 🌟 Features Highlight

Dự án này nổi bật với:
1. **AI Chatbot thông minh** - Đặt lịch tự động qua chat
2. **AI Tư vấn kiểu tóc** - Phân tích ảnh và gợi ý
3. **Giao diện hiện đại** - Glassmorphism, gradients, animations
4. **Responsive design** - Hoạt động tốt trên mọi thiết bị
5. **Real-time booking** - Kiểm tra lịch trống tức thì

---

🌟 **Star project nếu bạn thấy hữu ích!**  
🐛 **Report bugs** qua GitHub Issues  
💡 **Suggestions** welcome!
