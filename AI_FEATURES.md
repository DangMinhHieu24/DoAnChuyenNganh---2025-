# 🤖 Tài liệu tính năng AI - eBooking Salon

## 📋 Tổng quan

Dự án tích hợp 3 tính năng AI sử dụng **Google Gemini 2.5 Flash API**:
1. **Chatbot thông minh** - Tư vấn và hỗ trợ khách hàng
2. **AI Hair Consultant** - Phân tích khuôn mặt, gợi ý kiểu tóc
3. **AI Report Analysis** - Phân tích báo cáo kinh doanh

---

## 🎯 1. Chatbot thông minh

### Mô tả
Chatbot AI hỗ trợ khách hàng 24/7, trả lời câu hỏi về dịch vụ, giá cả, nhân viên và hỗ trợ đặt lịch hẹn.

### Files liên quan
- **API**: `api/chatbot.php`
- **Widget**: `includes/chatbot-widget.php`
- **Config**: `config/chatbot-config.php`

### Tính năng
- ✅ Trả lời câu hỏi về dịch vụ
- ✅ Cung cấp thông tin giá cả
- ✅ Giới thiệu nhân viên
- ✅ Hướng dẫn đặt lịch
- ✅ Trả lời câu hỏi chung về salon

### Intent Detection
Chatbot tự động phát hiện ý định người dùng:

| Intent | Trigger Keywords | Ví dụ |
|--------|------------------|-------|
| `price_inquiry` | giá, bao nhiêu, chi phí | "Giá cắt tóc bao nhiêu?" |
| `list_services` | dịch vụ nào, có những | "Salon có những dịch vụ gì?" |
| `staff_inquiry` | nhân viên, thợ, stylist | "Nhân viên nào giỏi?" |
| `booking` | đặt lịch, book, hẹn | "Tôi muốn đặt lịch" |
| `check_availability` | lịch trống, giờ trống | "Còn lịch trống không?" |
| `working_hours` | giờ mở cửa, làm việc | "Salon mở cửa lúc mấy giờ?" |
| `contact_info` | địa chỉ, liên hệ, sđt | "Địa chỉ salon ở đâu?" |

### API Request
```javascript
fetch('/api/chatbot.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        message: "Giá cắt tóc bao nhiêu?"
    })
})
```

### API Response
```json
{
    "success": true,
    "message": "Giá cắt tóc nam từ 100,000đ, cắt tóc nữ từ 150,000đ...",
    "intent": "price_inquiry",
    "context": {
        "services": [...]
    }
}
```

### Cấu hình
```php
// config/chatbot-config.php
define('CHATBOT_NAME', 'Trợ lý AI Salon');
define('CHATBOT_AVATAR', '🤖');
define('CHATBOT_WELCOME_MESSAGE', 'Xin chào! Tôi có thể giúp gì cho bạn?');
define('AI_TEMPERATURE', 0.7);
define('AI_MAX_TOKENS', 1024);
```

### Quota Usage
- **Model**: `gemini-2.5-flash`
- **Ước tính**: ~500 requests/ngày
- **Tokens/request**: ~500 tokens
- **Tổng tokens**: ~250,000/ngày

---

## 💇 2. AI Hair Consultant

### Mô tả
Tính năng phân tích khuôn mặt qua ảnh và gợi ý kiểu tóc phù hợp sử dụng Gemini Vision API.

### Files liên quan
- **API**: `api/ai-hair-consultant.php`
- **Page**: `pages/ai-hair-consultant.php`
- **Config**: `config/chatbot-config.php`

### Tính năng
- ✅ Upload ảnh khuôn mặt
- ✅ Phân tích hình dạng khuôn mặt
- ✅ Phân tích màu da, đặc điểm
- ✅ Gợi ý 3-4 kiểu tóc phù hợp
- ✅ Đề xuất dịch vụ cần làm
- ✅ Ước tính thời gian và giá

### Quy trình hoạt động
```
1. User upload ảnh
   ↓
2. Validate ảnh (type, size)
   ↓
3. Convert ảnh sang base64
   ↓
4. Gửi đến Gemini Vision API
   ↓
5. AI phân tích khuôn mặt
   ↓
6. AI gợi ý kiểu tóc
   ↓
7. Trả về kết quả + dịch vụ
```

### API Request
```javascript
const formData = new FormData();
formData.append('action', 'analyze_face');
formData.append('image', fileInput.files[0]);

fetch('/api/ai-hair-consultant.php', {
    method: 'POST',
    body: formData
})
```

### API Response
```json
{
    "success": true,
    "analysis": "**PHÂN TÍCH KHUÔN MẶT:**\n- Hình dạng: Oval\n...",
    "suggestions": [
        {
            "name": "Tóc Undercut Fade",
            "icon": "💇‍♀️"
        }
    ],
    "message": "Phân tích thành công! 🎨"
}
```

### Prompt Structure
```
Bạn là chuyên gia tư vấn kiểu tóc của salon eBooking.

PHÂN TÍCH KHUÔN MẶT:
- Hình dạng khuôn mặt
- Đặc điểm nổi bật
- Kiểu tóc hiện tại

GỢI Ý KIỂU TÓC (3 kiểu):
1. [Tên kiểu tóc] ⭐⭐⭐⭐⭐
   - Mô tả
   - Phù hợp vì
   - Dịch vụ cần
   - Thời gian

DỊCH VỤ TẠI SALON:
[Danh sách dịch vụ]

LƯU Ý CHĂM SÓC:
[Gợi ý chăm sóc]
```

### Validation
```php
// File type
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

// File size (max 5MB)
$maxSize = 5 * 1024 * 1024;

// Validate
if (!in_array($file['type'], $allowedTypes)) {
    return error('Chỉ chấp nhận JPG, PNG, WEBP');
}

if ($file['size'] > $maxSize) {
    return error('Ảnh quá lớn. Tối đa 5MB');
}
```

### Quota Usage
- **Model**: `gemini-2.5-flash` (Vision)
- **Ước tính**: ~100 requests/ngày
- **Tokens/request**: ~2,000 tokens (bao gồm ảnh)
- **Tổng tokens**: ~200,000/ngày

---

## 📊 3. AI Report Analysis

### Mô tả
Phân tích báo cáo kinh doanh tự động, đưa ra insights và gợi ý cải thiện cho admin.

### Files liên quan
- **API**: `api/ai-report-analysis.php`
- **Page**: `admin/reports.php`
- **Config**: `config/chatbot-config.php`

### Tính năng
- ✅ Phân tích doanh thu
- ✅ So sánh với tháng trước
- ✅ Đánh giá tỷ lệ hủy lịch
- ✅ Phân tích hiệu suất nhân viên
- ✅ Gợi ý 3-5 hành động cải thiện
- ✅ Dự báo xu hướng

### Quy trình hoạt động
```
1. Admin click "Phân tích AI"
   ↓
2. Lấy dữ liệu từ database
   - Doanh thu hôm nay/tháng/năm
   - Số lịch hẹn
   - Top dịch vụ
   - Top nhân viên
   ↓
3. Tính toán metrics
   - Tỷ lệ tăng trưởng
   - Tỷ lệ hủy lịch
   - Tỷ lệ hoàn thành
   ↓
4. Tạo prompt cho AI
   ↓
5. Gửi đến Gemini API
   ↓
6. AI phân tích và đưa ra insights
   ↓
7. Hiển thị kết quả
```

### Data Structure
```php
$report_data = [
    'period' => [
        'today' => '2025-12-10',
        'this_month' => '2025-12',
        'this_year' => '2025'
    ],
    'revenue' => [
        'today' => 150000,
        'this_month' => 5000000,
        'last_month' => 4500000,
        'growth_rate' => 11.11
    ],
    'bookings' => [
        'today' => 5,
        'this_month' => 50,
        'last_month' => 45,
        'growth_rate' => 11.11
    ],
    'status' => [
        'pending' => 5,
        'confirmed' => 10,
        'completed' => 30,
        'cancelled' => 5,
        'completion_rate' => 60,
        'cancellation_rate' => 10
    ],
    'top_services' => [...],
    'top_staff' => [...]
];
```

### Prompt Structure
```
Bạn là chuyên gia phân tích kinh doanh cho salon tóc.

📊 DỮ LIỆU THÁNG 12/2025:

💰 DOANH THU:
- Hôm nay: 150,000 VNĐ
- Tháng này: 5,000,000 VNĐ
- Tháng trước: 4,500,000 VNĐ
- Tăng trưởng: 11.11%

📅 LỊCH HẸN:
- Tổng lịch: 50
- Hoàn thành: 30 (60%)
- Đã hủy: 5 (10%)

🏆 TOP DỊCH VỤ:
1. Cắt tóc nam - 20 lượt
2. Nhuộm tóc - 15 lượt

👥 TOP NHÂN VIÊN:
1. Nguyễn Văn A - 25 lượt

📋 YÊU CẦU PHÂN TÍCH:
1. Đánh giá tổng quan
2. Phân tích xu hướng
3. Đánh giá tỷ lệ hủy
4. Nhận xét nhân viên
5. Gợi ý 3-5 hành động
6. Dự báo tháng tới
```

### API Response
```json
{
    "success": true,
    "analysis": "📊 PHÂN TÍCH TÌNH HÌNH KINH DOANH...",
    "data": {
        "period": {...},
        "revenue": {...},
        "bookings": {...}
    }
}
```

### Fallback khi không có dữ liệu
```php
if ($month_stats['total_bookings'] == 0) {
    return [
        'success' => true,
        'analysis' => "⚠️ Chưa có dữ liệu booking...\n\n" .
                     "GỢI Ý HÀNH ĐỘNG:\n" .
                     "1. Marketing & Quảng bá\n" .
                     "2. Thu hút khách hàng đầu tiên\n" .
                     "3. Tối ưu hệ thống\n"
    ];
}
```

### Quota Usage
- **Model**: `gemini-2.5-flash`
- **Ước tính**: ~20 requests/ngày
- **Tokens/request**: ~3,000 tokens
- **Tổng tokens**: ~60,000/ngày

---

## ⚙️ Cấu hình chung

### Model Configuration
```php
// config/chatbot-config.php

// Model chung cho tất cả tính năng
define('GEMINI_MODEL', 'gemini-2.5-flash');
define('GEMINI_API_VERSION', 'v1beta');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent');

// Alias
define('GEMINI_CHATBOT_MODEL', GEMINI_MODEL);
define('GEMINI_HAIR_MODEL', GEMINI_MODEL);
define('GEMINI_REPORT_MODEL', GEMINI_MODEL);
```

### AI Parameters
```php
'generationConfig' => [
    'temperature' => 0.7,      // Độ sáng tạo (0.0-1.0)
    'topK' => 40,              // Top K sampling
    'topP' => 0.95,            // Top P sampling
    'maxOutputTokens' => 8192  // Độ dài response tối đa
]
```

### Safety Settings (Hair Consultant)
```php
'safetySettings' => [
    [
        'category' => 'HARM_CATEGORY_HARASSMENT',
        'threshold' => 'BLOCK_NONE'
    ],
    // ... các category khác
]
```

---

## 📊 Quota Management

### Quota Limits (gemini-2.5-flash)
- **RPM**: 15 requests/phút
- **RPD**: 1,500 requests/ngày
- **TPM**: 4,000,000 tokens/phút

### Ước tính sử dụng hàng ngày
| Tính năng | Requests | Tokens/req | Tổng tokens |
|-----------|----------|------------|-------------|
| Chatbot | 500 | 500 | 250,000 |
| Hair Consultant | 100 | 2,000 | 200,000 |
| Report Analysis | 20 | 3,000 | 60,000 |
| **TỔNG** | **620** | - | **510,000** |

### So với quota
- **RPM**: ~10/15 (67%) ✅
- **RPD**: 620/1,500 (41%) ✅
- **TPM**: 510k/4M (13%) ✅

### Khi hết quota
```
Lỗi 429: Quota exceeded

Giải pháp:
1. Đợi 1 phút (RPM reset)
2. Đợi 24h từ lúc hết (RPD reset)
3. Tạo API key mới
4. Implement caching
5. Rate limiting
```

---

## 🔧 Troubleshooting

### Chatbot không trả lời
```
Kiểm tra:
1. API key đúng chưa
2. Model name đúng chưa
3. Database có dữ liệu chưa
4. Console có lỗi gì không
```

### Hair Consultant lỗi
```
Kiểm tra:
1. Ảnh đúng format chưa (JPG/PNG/WEBP)
2. Ảnh < 5MB chưa
3. Safety settings đã tắt chưa
4. maxOutputTokens đủ lớn chưa (8192)
```

### Report Analysis không hiển thị
```
Kiểm tra:
1. Đã đăng nhập admin chưa
2. Session có được pass qua fetch không
3. Có dữ liệu booking chưa
4. CSS có scroll không
```

---

## 🚀 Tối ưu hóa

### 1. Caching (Đang phát triển)
```php
// Cache response AI
$cacheKey = md5($prompt);
if (isset($_SESSION['ai_cache'][$cacheKey])) {
    return $_SESSION['ai_cache'][$cacheKey];
}
```

### 2. Rate Limiting
```php
// Giới hạn user
if ($_SESSION['ai_calls'] > 5) {
    return error('Bạn đang gọi quá nhanh');
}
```

### 3. Lazy Loading
```javascript
// Load chatbot khi cần
document.getElementById('chatbot-btn').addEventListener('click', () => {
    loadChatbot();
});
```

---

## 📝 Best Practices

### 1. Prompt Engineering
- ✅ Rõ ràng, cụ thể
- ✅ Có ví dụ
- ✅ Định dạng output
- ❌ Quá dài, phức tạp

### 2. Error Handling
```php
try {
    $response = callGeminiAPI($prompt);
} catch (Exception $e) {
    error_log($e->getMessage());
    return fallbackResponse();
}
```

### 3. Security
- ✅ Validate input
- ✅ Sanitize output
- ✅ Rate limiting
- ✅ API key trong .gitignore

---

## 📞 Hỗ trợ

Nếu gặp vấn đề với AI features:
1. Kiểm tra API key
2. Kiểm tra quota
3. Xem error logs
4. Liên hệ: dminhhieu2408@gmail.com


---

**Cập nhật lần cuối**: 26 tháng 12, 2025
