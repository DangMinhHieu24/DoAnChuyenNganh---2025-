# 📊 Hướng Dẫn Quản Lý API Quota

## 🎯 Giới Hạn Miễn Phí (Free Tier)

### Gemini 2.5 Flash
- **15 requests/phút** (RPM - Requests Per Minute)
- **1,500 requests/ngày** (RPD - Requests Per Day)
- **1 triệu tokens/ngày** (Input tokens)
- **Miễn phí hoàn toàn**

### Thời Gian Reset
- **Quota/phút**: Reset sau mỗi 60 giây
- **Quota/ngày**: Reset vào **7:00 sáng** mỗi ngày (giờ Việt Nam)

## 🔍 Kiểm Tra Quota

### Cách 1: Qua Website
1. Truy cập: https://ai.dev/usage?tab=rate-limit
2. Đăng nhập Google
3. Xem usage hiện tại

### Cách 2: Qua Error Message
Khi hết quota, API trả về lỗi 429:
```json
{
  "error": {
    "code": 429,
    "message": "You exceeded your current quota...",
    "status": "RESOURCE_EXHAUSTED"
  }
}
```

## ⚠️ Khi Hết Quota

### Triệu Chứng
- Chatbot trả lời: "Xin lỗi, tôi đang gặp sự cố kỹ thuật"
- AI Hair Consultant: "Lỗi kết nối API"
- Error log: "HTTP 429 - Quota exceeded"

### Giải Pháp

#### 1. Đợi Reset (Miễn phí)
**Hết quota/phút:**
- Đợi 1 phút
- Thử lại

**Hết quota/ngày:**
- Đợi đến 7:00 sáng hôm sau
- Quota sẽ tự động reset

#### 2. Tạo API Key Mới (Miễn phí)
**Bước 1:** Tạo Project Mới
1. Vào https://console.cloud.google.com
2. Tạo project mới
3. Enable Gemini API

**Bước 2:** Tạo API Key
1. Vào https://makersuite.google.com/app/apikey
2. Chọn project mới
3. Create API Key
4. Copy key

**Bước 3:** Cập Nhật Config
```php
// File: config/chatbot-config.php
define('GEMINI_API_KEY', 'AIzaSy_NEW_KEY_HERE');
```

**Bước 4:** Restart Apache
- Mở XAMPP Control Panel
- Stop → Start Apache

#### 3. Dùng Nhiều Model Luân Phiên
Mỗi model có quota riêng:
- `gemini-2.5-flash` - 1,500 requests/ngày
- `gemini-2.5-pro` - 1,500 requests/ngày
- `gemini-2.0-flash` - 1,500 requests/ngày

Khi 1 model hết quota, chuyển sang model khác.

#### 4. Upgrade Lên Paid Plan
**Giá:**
- $0.075 per 1M input tokens
- $0.30 per 1M output tokens

**Lợi ích:**
- Không giới hạn requests/ngày
- Quota cao hơn nhiều
- Ưu tiên xử lý

**Cách upgrade:**
1. Vào https://console.cloud.google.com
2. Enable billing
3. Thêm payment method

## 💡 Tips Tiết Kiệm Quota

### 1. Cache Responses
Lưu câu trả lời phổ biến:
```php
// Cache câu hỏi thường gặp
$cache = [
    'giá cắt tóc' => 'Giá cắt tóc từ 50,000đ...',
    'giờ làm việc' => 'Chúng tôi làm việc từ 9:00-20:00...'
];
```

### 2. Rate Limiting
Giới hạn số tin nhắn/user:
```php
// Trong config/chatbot-config.php
define('MAX_MESSAGES_PER_MINUTE', 10);
define('MAX_MESSAGES_PER_HOUR', 50);
```

### 3. Fallback Responses
Khi hết quota, trả lời từ database:
```php
if ($apiError) {
    // Trả lời từ database thay vì gọi API
    return getDatabaseResponse($question);
}
```

### 4. Optimize Prompts
- Giảm độ dài prompt
- Giảm max_tokens
- Tăng temperature (ít chính xác hơn nhưng nhanh hơn)

## 📈 Monitoring

### Check Usage
```bash
# Xem log Apache
tail -f C:\xampp\apache\logs\error.log | findstr "429"
```

### Track Requests
Thêm logging vào code:
```php
// Log mỗi API call
error_log("Gemini API called: " . date('Y-m-d H:i:s'));
```

## 🆘 Troubleshooting

### Lỗi: "Quota exceeded for metric: generate_content_free_tier_requests"
**Nguyên nhân:** Đã vượt quá 1,500 requests/ngày

**Giải pháp:**
1. Đợi đến 7:00 sáng
2. Hoặc tạo API key mới
3. Hoặc upgrade lên paid

### Lỗi: "Quota exceeded for metric: generate_content_free_tier_input_token_count"
**Nguyên nhân:** Đã vượt quá 1 triệu tokens/ngày

**Giải pháp:**
1. Giảm độ dài prompt
2. Giảm max_tokens
3. Đợi reset hoặc upgrade

### Config đã sửa nhưng vẫn lỗi
**Nguyên nhân:** Apache chưa reload config

**Giải pháp:**
1. Restart Apache trong XAMPP
2. Clear browser cache
3. Thử lại

## 📚 Tài Liệu Tham Khảo

- **Rate Limits:** https://ai.google.dev/gemini-api/docs/rate-limits
- **Pricing:** https://ai.google.dev/pricing
- **Usage Dashboard:** https://ai.dev/usage
- **API Key Management:** https://makersuite.google.com/app/apikey

---

**Cập nhật:** December 7, 2025  
**Version:** 1.0
