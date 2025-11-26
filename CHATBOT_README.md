# 🤖 Chatbot AI - Hướng Dẫn Nhanh

## ⚡ Cài Đặt Trong 3 Bước

### 1️⃣ Lấy API Key (2 phút)
```
1. Vào: https://makersuite.google.com/app/apikey
2. Đăng nhập Google
3. Click "Create API Key"
4. Copy key
```

### 2️⃣ Cấu Hình (1 phút)
Mở file: `config/chatbot-config.php`

Tìm dòng:
```php
define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY_HERE');
```

Thay bằng API key của bạn:
```php
define('GEMINI_API_KEY', 'AIzaSy...');
```

### 3️⃣ Test (1 phút)
```
1. Mở website
2. Click icon chatbot góc phải dưới
3. Gõ: "Xin chào"
4. Thành công! ✅
```

## 📁 Files Quan Trọng

```
api/
  ├── chatbot.php              ← API chat chính
  ├── chatbot-booking.php      ← API đặt lịch tự động
  └── chatbot-actions.php      ← API actions bổ sung

config/
  └── chatbot-config.php       ← ⚠️ CẦN CẬP NHẬT API KEY

assets/
  ├── css/chatbot.css          ← Styling
  └── js/chatbot.js            ← Logic

includes/
  └── chatbot-widget.php       ← Widget (đã tích hợp)
```

## 🎯 Tính Năng

✅ Trả lời câu hỏi tự nhiên (tiếng Việt)
✅ Thông tin dịch vụ, giá cả từ database
✅ Giới thiệu nhân viên
✅ **Đặt lịch tự động qua chat** (NEW!)
✅ Kiểm tra lịch trống
✅ Conversation flow thông minh
✅ Responsive (mobile-friendly)

## 🧪 Test Nhanh

Thử các câu hỏi:
```
✓ "Có những dịch vụ gì?"
✓ "Giá cắt tóc bao nhiêu?"
✓ "Nhân viên nào giỏi?"
✓ "Tôi muốn đặt lịch"
✓ "Giờ làm việc?"
✓ "Địa chỉ salon?"
```

## 📖 Tài Liệu Đầy Đủ

- **Quick Start:** `CHATBOT_QUICKSTART.md`
- **Chi tiết:** `CHATBOT_SETUP.md`
- **Checklist:** `CHATBOT_CHECKLIST.md`
- **Tổng quan:** `CHATBOT_SUMMARY.md`

## 🎮 Demo

Mở file: `chatbot-demo.html` trong trình duyệt

## ❓ Gặp Lỗi?

### Lỗi: "Xin lỗi, tôi đang gặp sự cố kỹ thuật"
→ API key không đúng. Kiểm tra lại file `config/chatbot-config.php`

### Lỗi: Chatbot không hiển thị
→ Xóa cache (Ctrl + F5) và kiểm tra console (F12)

### Lỗi: Không kết nối được
→ Kiểm tra server XAMPP đã chạy chưa

## 🔐 Bảo Mật

⚠️ **QUAN TRỌNG:**
- Không commit API key lên Git
- Thêm `config/chatbot-config.php` vào `.gitignore`
- Sử dụng file `.gitignore.chatbot` đã cung cấp

## 📊 Model AI

- **Tên:** Gemini 1.5 Flash
- **Ngày:** Mới nhất (15/11/2025)
- **Ngôn ngữ:** Tiếng Việt + English
- **Miễn phí:** 15 requests/phút, 1500/ngày

## 💡 Tips

1. Cập nhật thông tin salon trong `config/chatbot-config.php`
2. Tùy chỉnh màu sắc trong `assets/css/chatbot.css`
3. Theo dõi API usage tại Google AI Studio

## 📞 Hỗ Trợ

- Email: dminhhieu240@gmail.com
- Phone: 0976985305

---

**Chúc bạn thành công! 🎉**
