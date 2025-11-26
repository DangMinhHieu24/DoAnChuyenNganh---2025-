# 🚀 Chatbot AI - Quick Start Guide

## Bắt đầu nhanh trong 5 phút!

### Bước 1: Lấy API Key (2 phút)
1. Mở: https://makersuite.google.com/app/apikey
2. Đăng nhập Google
3. Click "Create API Key"
4. Copy API key

### Bước 2: Cấu hình (1 phút)
Mở file `config/chatbot-config.php`, tìm dòng:
```php
define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY_HERE');
```

Thay bằng:
```php
define('GEMINI_API_KEY', 'API_KEY_VỪA_COPY');
```

### Bước 3: Test (2 phút)
1. Mở website: `http://localhost/Website_DatLich`
2. Click icon chatbot góc phải dưới
3. Gõ: "Xin chào"
4. Thành công! ✅

## 🎯 Câu hỏi mẫu để test

```
✅ "Có những dịch vụ gì?"
✅ "Giá cắt tóc bao nhiêu?"
✅ "Nhân viên nào giỏi?"
✅ "Tôi muốn đặt lịch" → Đặt lịch tự động!
✅ "Giờ làm việc?"
✅ "Địa chỉ salon?"
```

## 🆕 Tính năng đặt lịch tự động

Click nút **"📅 Đặt lịch"** hoặc gõ "Tôi muốn đặt lịch"

Chatbot sẽ hướng dẫn từng bước:
1. ✅ Chọn dịch vụ
2. ✅ Chọn nhân viên  
3. ✅ Chọn ngày
4. ✅ Chọn giờ (chỉ slot trống)
5. ✅ Xác nhận → Hoàn tất!

**Lưu ý:** Cần đăng nhập để đặt lịch.

## 🔧 Tùy chỉnh nhanh

### Thay đổi thông tin salon
File: `config/chatbot-config.php`
```php
define('SALON_NAME', 'Tên salon của bạn');
define('SALON_ADDRESS', 'Địa chỉ của bạn');
define('SALON_PHONE', '0123456789');
```

### Thay đổi màu sắc
File: `assets/css/chatbot.css`
```css
.chatbot-toggle {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

## ❓ Gặp lỗi?

### Lỗi: "Xin lỗi, tôi đang gặp sự cố kỹ thuật"
**Nguyên nhân:** API key không đúng

**Giải pháp:**
1. Kiểm tra lại API key
2. Tạo API key mới
3. Kiểm tra quota: https://makersuite.google.com/app/apikey

### Lỗi: Chatbot không hiển thị
**Giải pháp:**
1. Xóa cache (Ctrl + F5)
2. Kiểm tra console (F12)
3. Kiểm tra file `includes/chatbot-widget.php` đã được include chưa

## 📚 Tài liệu đầy đủ
Xem file: [CHATBOT_SETUP.md](CHATBOT_SETUP.md)

## 🎮 Demo
Mở file: `chatbot-demo.html` trong trình duyệt

---

**Chúc bạn thành công! 🎉**

Có vấn đề? Email: dminhhieu240@gmail.com
