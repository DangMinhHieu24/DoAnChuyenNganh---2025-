# 🤖 AI Chatbot - Hướng Dẫn Chi Tiết

Chatbot AI thông minh sử dụng Google Gemini 2.5 Flash với khả năng đặt lịch tự động.

## 📋 Tổng Quan

AI Chatbot là trợ lý ảo 24/7 giúp khách hàng:
- ❓ Trả lời câu hỏi về dịch vụ, giá cả
- 👥 Tìm kiếm nhân viên và lịch trống
- 📅 **Đặt lịch tự động qua chat**
- 💬 Hiểu ngôn ngữ tự nhiên
- ⚡ Phản hồi nhanh chóng

## 🎯 Tính Năng

### 1. Trả Lời Câu Hỏi Tự Động
Chatbot có thể trả lời:
- "Salon có dịch vụ gì?"
- "Giá cắt tóc bao nhiêu?"
- "Giờ làm việc của salon?"
- "Địa chỉ salon ở đâu?"
- "Có nhân viên nào rảnh không?"

### 2. Đặt Lịch Qua Chat
**Conversation Flow:**
```
User: "Tôi muốn đặt lịch"
Bot: "Bạn muốn làm dịch vụ gì?" [Hiển thị danh sách dịch vụ]
User: [Chọn dịch vụ]
Bot: "Bạn muốn nhân viên nào?" [Hiển thị nhân viên]
User: [Chọn nhân viên]
Bot: "Chọn ngày bạn muốn đến" [Hiển thị lịch]
User: [Chọn ngày]
Bot: "Chọn giờ" [Hiển thị giờ trống]
User: [Chọn giờ]
Bot: "Xác nhận đặt lịch?" [Hiển thị tóm tắt]
User: [Xác nhận]
Bot: "Đặt lịch thành công! ✅"
```

### 3. Gợi Ý Thông Minh
- Quick replies cho câu hỏi phổ biến
- Gợi ý dịch vụ phù hợp
- Nhắc nhở thông tin thiếu

## 🔧 Cài Đặt

### 1. Lấy Gemini API Key

1. Truy cập: https://makersuite.google.com/app/apikey
2. Đăng nhập Google
3. Click "Create API Key"
4. Copy API key

**Giới hạn miễn phí:**
- **15 requests/phút** (RPM)
- **1,500 requests/ngày** (RPD)
- **1 triệu tokens/ngày**
- Quota reset vào **7:00 sáng** mỗi ngày (giờ Việt Nam)

### 2. Cấu Hình

Mở file `config/chatbot-config.php`:

```php
// Thay YOUR_API_KEY_HERE bằng API key của bạn
define('GEMINI_API_KEY', 'AIzaSy...');

// Model - Gemini 2.5 Flash (multimodal, mới nhất)
define('GEMINI_MODEL', 'gemini-2.5-flash');

// API Endpoint - Sử dụng v1 (không phải v1beta)
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1/models/' . GEMINI_MODEL . ':generateContent');

// Thông tin salon (tùy chỉnh theo salon của bạn)
define('SALON_NAME', 'eBooking Salon');
define('SALON_ADDRESS', '162 ABC, Phường 5, TP Trà Vinh');
define('SALON_PHONE', '0976985305');
define('SALON_EMAIL', 'dminhhieu2408@gmail.com');
```

### 3. Tùy Chỉnh

**Thay đổi màu sắc:**
```php
define('CHATBOT_COLOR_PRIMARY', '#667eea');
define('CHATBOT_COLOR_SECONDARY', '#764ba2');
```

**Thay đổi vị trí:**
```php
define('CHATBOT_POSITION', 'bottom-right'); // hoặc 'bottom-left'
```

**Bật/tắt tính năng:**
```php
define('ENABLE_BOOKING_VIA_CHAT', true);
define('ENABLE_AVAILABILITY_CHECK', true);
define('ENABLE_SERVICE_SEARCH', true);
```

## 📁 Cấu Trúc File

```
Chatbot System/
├── api/
│   ├── chatbot.php              # API chính - xử lý chat
│   ├── chatbot-booking.php      # API đặt lịch qua chat
│   └── chatbot-actions.php      # Actions bổ sung
├── assets/
│   ├── css/
│   │   └── chatbot.css          # Styling chatbot
│   └── js/
│       └── chatbot.js           # Frontend logic
├── config/
│   └── chatbot-config.php       # Cấu hình chatbot
└── includes/
    └── chatbot-widget.php       # Widget include
```

## 🔄 Luồng Hoạt Động

### Chat Thông Thường

```
1. User nhập tin nhắn
   ↓
2. chatbot.js gửi AJAX đến api/chatbot.php
   ↓
3. chatbot.php phân tích intent:
   - Kiểm tra từ khóa (giá, dịch vụ, nhân viên...)
   - Query database nếu cần
   - Gửi đến Gemini API
   ↓
4. Gemini trả về response
   ↓
5. Format response + quick replies
   ↓
6. Trả về JSON cho frontend
   ↓
7. chatbot.js hiển thị tin nhắn
```

### Đặt Lịch Qua Chat

```
1. User: "Tôi muốn đặt lịch"
   ↓
2. Detect intent = "booking"
   ↓
3. Chuyển sang api/chatbot-booking.php
   ↓
4. Lưu state vào session:
   {
     step: 'select_service',
     service_id: null,
     staff_id: null,
     date: null,
     time: null
   }
   ↓
5. Hiển thị UI chọn dịch vụ
   ↓
6. User chọn → Update state → Next step
   ↓
7. Lặp lại cho: staff → date → time
   ↓
8. Confirm → Insert vào database
   ↓
9. Thành công → Reset state
```

## 🎨 Giao Diện

### Widget Button
- Vị trí: Góc phải/trái màn hình
- Icon: 💬 hoặc 🤖
- Gradient background
- Pulse animation
- Badge thông báo

### Chat Window
- Kích thước: 380px × 550px (desktop)
- Responsive trên mobile
- Header với gradient
- Message bubbles
- Quick replies buttons
- Input với emoji support
- Typing indicator

### Message Types
- **Text message**: Tin nhắn văn bản
- **Service cards**: Hiển thị dịch vụ với giá
- **Staff cards**: Hiển thị nhân viên với avatar
- **Date picker**: Chọn ngày
- **Time slots**: Chọn giờ
- **Confirmation**: Xác nhận booking

## 🧪 Testing

### Test Chat Cơ Bản
```
User: "Xin chào"
Bot: "Xin chào! Tôi là trợ lý AI..."

User: "Salon có dịch vụ gì?"
Bot: [Liệt kê dịch vụ từ database]

User: "Giá cắt tóc bao nhiêu?"
Bot: "Dịch vụ cắt tóc của chúng tôi..."
```

### Test Đặt Lịch
```
User: "Đặt lịch"
Bot: [Hiển thị danh sách dịch vụ]

User: [Click chọn "Cắt tóc nam"]
Bot: [Hiển thị danh sách nhân viên]

User: [Click chọn nhân viên]
Bot: [Hiển thị lịch chọn ngày]

User: [Chọn ngày]
Bot: [Hiển thị giờ trống]

User: [Chọn giờ]
Bot: [Hiển thị xác nhận]

User: [Click "Xác nhận"]
Bot: "Đặt lịch thành công! ✅"
```

## 🐛 Xử Lý Lỗi

### Lỗi API Key
```
Error: "API returned null"
Fix: Kiểm tra GEMINI_API_KEY trong config
```

### Lỗi Model Not Found
```
Error: "models/xxx is not found for API version v1beta"
Fix: 
- Đảm bảo dùng API v1 (không phải v1beta)
- Dùng model 'gemini-2.5-flash' hoặc 'gemini-2.0-flash'
```

### Lỗi 429 - Quota Exceeded
```
Error: "You exceeded your current quota"
Fix:
- Đợi quota reset (7:00 sáng)
- Hoặc tạo API key mới
- Xem chi tiết: API_QUOTA_GUIDE.md
```

### Lỗi Timeout
```
Error: "Connection timeout"
Fix: Tăng API_TIMEOUT trong config
```

### Lỗi Session
```
Error: "Session expired"
Fix: Kiểm tra session_start() trong các file API
```

## ⚙️ Tùy Chỉnh Nâng Cao

### Thêm Intent Mới

Sửa file `api/chatbot.php`:

```php
// Thêm intent detection
if (stripos($message, 'khuyến mãi') !== false) {
    $intent = 'promotion_inquiry';
}

// Xử lý intent
if ($intent === 'promotion_inquiry') {
    // Query promotions từ database
    $promotions = getPromotions($db);
    // Format response
    $response = formatPromotions($promotions);
}
```

### Thêm Quick Reply

Sửa file `api/chatbot.php`:

```php
$quickReplies = [
    ['text' => '📋 Xem dịch vụ', 'action' => 'list_services'],
    ['text' => '💰 Xem giá', 'action' => 'price_inquiry'],
    ['text' => '📅 Đặt lịch', 'action' => 'booking'],
    ['text' => '🎁 Khuyến mãi', 'action' => 'promotions'], // Mới
];
```

### Thay Đổi Prompt

Sửa file `config/chatbot-config.php`:

```php
define('SYSTEM_PROMPT_BASE', 
    "Bạn là trợ lý AI của salon. " .
    "Hãy trả lời thân thiện, chuyên nghiệp. " .
    "Sử dụng emoji phù hợp. " .
    "Trả lời ngắn gọn nhưng đầy đủ thông tin."
);
```

## 📊 Analytics

Chatbot tự động log:
- Số lượng tin nhắn
- Intent phổ biến
- Booking success rate
- Response time

Xem logs trong database table `chat_logs` (nếu enable).

## 🔐 Bảo Mật

- ✅ Rate limiting (10 msg/phút, 50 msg/giờ)
- ✅ Input validation & sanitization
- ✅ XSS protection
- ✅ SQL injection prevention (PDO)
- ✅ Session security

## 💡 Tips & Best Practices

1. **API Key**: Không commit API key lên Git
2. **Error Handling**: Luôn có fallback response
3. **User Experience**: Giữ conversation flow ngắn gọn
4. **Performance**: Cache responses phổ biến
5. **Testing**: Test trên nhiều scenarios

## 🚀 Nâng Cấp Tương Lai

- [ ] Voice input/output
- [ ] Multi-language support
- [ ] Sentiment analysis
- [ ] Chatbot analytics dashboard
- [ ] Integration với Facebook Messenger
- [ ] AI recommendations based on history

## 📞 Hỗ Trợ

Nếu gặp vấn đề:
1. Kiểm tra console log (F12)
2. Kiểm tra PHP error log
3. Test API endpoint trực tiếp
4. Đọc error message từ response

---

## 📚 Tài Liệu Liên Quan

- **API_QUOTA_GUIDE.md** - Hướng dẫn quản lý quota
- **README_SETUP.md** - Hướng dẫn cài đặt chi tiết
- **SECURITY_GUIDE.md** - Hướng dẫn bảo mật API key

## 🔗 Links Hữu Ích

- **Gemini API Docs:** https://ai.google.dev/docs
- **API Key Management:** https://makersuite.google.com/app/apikey
- **Rate Limits:** https://ai.google.dev/gemini-api/docs/rate-limits
- **Usage Dashboard:** https://ai.dev/usage

---

**Model:** Gemini 2.5 Flash (Multimodal)  
**API Version:** v1  
**Free Tier:** 15 RPM, 1,500 RPD  
**Cập nhật:** December 7, 2025
