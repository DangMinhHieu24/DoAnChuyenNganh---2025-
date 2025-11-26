# Hướng Dẫn Cài Đặt Chatbot AI với Gemini

## 📋 Tổng Quan

Chatbot AI này sử dụng Google Gemini 1.5 Flash (model mới nhất tính đến 15/11/2025) để:
- Trả lời câu hỏi chung của khách hàng
- Cung cấp thông tin về dịch vụ, giá cả
- Giới thiệu nhân viên
- Hỗ trợ đặt lịch hẹn
- Kiểm tra lịch trống

## 🚀 Bước 1: Lấy API Key từ Google AI Studio

1. Truy cập: https://makersuite.google.com/app/apikey
2. Đăng nhập bằng tài khoản Google
3. Click "Create API Key"
4. Copy API key vừa tạo

## 🔧 Bước 2: Cấu Hình API Key

Mở file `api/chatbot.php` và thay thế dòng:

```php
define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY_HERE');
```

Thành:

```php
define('GEMINI_API_KEY', 'API_KEY_CỦA_BẠN');
```

## 📦 Bước 3: Tích Hợp Vào Website

### Cách 1: Thêm vào footer.php (Khuyến nghị)

Mở file `includes/footer.php` và thêm trước thẻ đóng `</body>`:

```php
<?php include 'includes/chatbot-widget.php'; ?>
```

### Cách 2: Thêm vào từng trang cụ thể

Thêm vào cuối file (trước `</body>`):

```php
<?php include 'includes/chatbot-widget.php'; ?>
```

## 🎨 Bước 4: Tùy Chỉnh (Tùy Chọn)

### Thay đổi màu sắc

Chỉnh sửa file `assets/css/chatbot.css`:

```css
/* Màu chính của chatbot */
.chatbot-toggle {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Thay đổi thành màu khác, ví dụ: */
.chatbot-toggle {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
```

### Thay đổi vị trí

Trong file `assets/css/chatbot.css`:

```css
/* Mặc định: góc phải dưới */
.chatbot-toggle {
    bottom: 20px;
    right: 20px;
}

/* Chuyển sang góc trái dưới: */
.chatbot-toggle {
    bottom: 20px;
    left: 20px;
}
```

### Tùy chỉnh thông tin liên hệ

Mở file `api/chatbot.php` và tìm phần:

```php
case 'contact_info':
    $context['contact'] = [
        'address' => '123 Đường ABC, Quận 1, TP.HCM',
        'phone' => '0123 456 789',
        'email' => 'contact@salon.com'
    ];
    break;
```

Thay đổi thông tin phù hợp với salon của bạn.

### Tùy chỉnh giờ làm việc

Trong file `api/chatbot.php`:

```php
case 'working_hours':
    $context['working_hours'] = [
        'weekday' => '9:00 - 20:00',
        'weekend' => '8:00 - 21:00'
    ];
    break;
```

## 🧪 Bước 5: Test Chatbot

1. Mở website của bạn
2. Click vào icon chatbot ở góc phải dưới
3. Thử các câu hỏi:
   - "Có những dịch vụ gì?"
   - "Giá cắt tóc bao nhiêu?"
   - "Nhân viên nào giỏi?"
   - "Tôi muốn đặt lịch"
   - "Giờ làm việc của salon?"

## 🎯 Các Tính Năng Chính

### 1. Trả lời thông minh
- Chatbot hiểu ngữ cảnh và trả lời tự nhiên
- Có thể trả lời câu hỏi ngoài luồng
- Sử dụng emoji để thân thiện

### 2. Tích hợp Database
- Lấy thông tin dịch vụ thực tế từ database
- Hiển thị danh sách nhân viên và chuyên môn
- Cung cấp giá chính xác

### 3. Quick Replies
- Gợi ý câu hỏi nhanh
- Dễ dàng điều hướng hội thoại

### 4. Responsive Design
- Hoạt động tốt trên mobile
- Giao diện đẹp, hiện đại

## 🔍 Troubleshooting

### Lỗi: "Xin lỗi, tôi đang gặp sự cố kỹ thuật"

**Nguyên nhân:** API key không hợp lệ hoặc đã hết quota

**Giải pháp:**
1. Kiểm tra API key đã nhập đúng chưa
2. Kiểm tra quota tại: https://makersuite.google.com/app/apikey
3. Tạo API key mới nếu cần

### Lỗi: Chatbot không hiển thị

**Giải pháp:**
1. Kiểm tra đã include `chatbot-widget.php` chưa
2. Xóa cache trình duyệt (Ctrl + F5)
3. Kiểm tra console browser (F12) xem có lỗi JavaScript không

### Lỗi: Không kết nối được API

**Giải pháp:**
1. Kiểm tra server có cURL enabled không
2. Kiểm tra firewall có chặn kết nối ra ngoài không
3. Test API bằng Postman

## 📊 Model Gemini 2.0 Flash

**Tại sao chọn Gemini 2.0 Flash?**

- ✅ Model mới nhất của Google (2024)
- ✅ Tốc độ phản hồi cực nhanh
- ✅ Hiểu tiếng Việt tốt
- ✅ Chi phí thấp
- ✅ Quota miễn phí: 15 requests/phút, 1500 requests/ngày

**Thông số kỹ thuật:**
- Temperature: 0.7 (cân bằng giữa sáng tạo và chính xác)
- Max tokens: 1024 (đủ cho hầu hết câu trả lời)
- Top K: 40
- Top P: 0.95

## 🔐 Bảo Mật

1. **Không lưu API key trong code công khai**
   - Nên lưu trong file config riêng
   - Thêm vào .gitignore

2. **Rate limiting**
   - Gemini có giới hạn 15 requests/phút
   - Cân nhắc thêm rate limiting phía server

3. **Validate input**
   - Code đã có sẵn validation cơ bản
   - Có thể thêm sanitization nếu cần

## 📈 Nâng Cao

### Thêm tính năng đặt lịch trực tiếp

Trong file `api/chatbot.php`, thêm logic xử lý đặt lịch:

```php
case 'booking':
    // Extract thông tin từ message
    // Tạo booking trong database
    // Trả về confirmation
    break;
```

### Lưu lịch sử chat

Thêm bảng `chat_history` trong database để lưu trữ và phân tích.

### Tích hợp với CRM

Kết nối với hệ thống CRM để theo dõi khách hàng tiềm năng.

## 📞 Hỗ Trợ

Nếu gặp vấn đề, kiểm tra:
1. Console log trong browser (F12)
2. PHP error log
3. Network tab để xem API response

## 🎉 Hoàn Thành!

Chatbot AI của bạn đã sẵn sàng hoạt động! Khách hàng giờ có thể:
- Hỏi về dịch vụ 24/7
- Nhận tư vấn tự động
- Đặt lịch dễ dàng hơn

Chúc bạn thành công! 🚀
