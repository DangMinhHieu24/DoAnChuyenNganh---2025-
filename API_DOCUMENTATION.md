# 📡 Tài liệu API - eBooking Salon

## 📋 Tổng quan

Tài liệu này mô tả chi tiết tất cả API endpoints của hệ thống eBooking Salon.

**Base URL**: `http://localhost/Website_DatLich/api`

**Content-Type**: `application/json`

---

## 🔐 Xác thực (Authentication)

### Xác thực dựa trên Session
Hệ thống sử dụng PHP Session để xác thực người dùng.

**Headers cần thiết:**
```
Cookie: PHPSESSID=xxx
```

**Kiểm tra đăng nhập:**
```php
if (!isLoggedIn()) {
    http_response_code(401);
    return ['success' => false, 'message' => 'Unauthorized'];
}
```

---

## 🤖 AI APIs

### 1. Chatbot API

**Endpoint**: `/api/chatbot.php`

**Method**: `POST`

**Description**: Chatbot AI hỗ trợ khách hàng, trả lời câu hỏi về dịch vụ, giá cả, nhân viên.

#### Request
```json
{
  "message": "Giá cắt tóc bao nhiêu?"
}
```

#### Response Success
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

#### Response Error
```json
{
  "success": false,
  "message": "Vui lòng nhập tin nhắn"
}
```

#### Intent Types
| Intent | Description | Keywords |
|--------|-------------|----------|
| `price_inquiry` | Hỏi về giá | giá, bao nhiêu, chi phí |
| `list_services` | Xem dịch vụ | dịch vụ nào, có những |
| `staff_inquiry` | Hỏi nhân viên | nhân viên, thợ, stylist |
| `booking` | Đặt lịch | đặt lịch, book, hẹn |
| `check_availability` | Kiểm tra lịch trống | lịch trống, giờ trống |
| `working_hours` | Giờ làm việc | giờ mở cửa, làm việc |
| `contact_info` | Thông tin liên hệ | địa chỉ, liên hệ, sđt |
| `general` | Câu hỏi chung | - |

#### Example
```javascript
fetch('/api/chatbot.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        message: "Salon có những dịch vụ gì?"
    })
})
.then(res => res.json())
.then(data => console.log(data));
```

---

### 2. AI Hair Consultant API

**Endpoint**: `/api/ai-hair-consultant.php`

**Method**: `POST` (multipart/form-data)

**Description**: Phân tích khuôn mặt qua ảnh và gợi ý kiểu tóc phù hợp.

#### Actions

##### 2.1. Analyze Face

**Action**: `analyze_face`

**Request** (multipart/form-data)
```
action: analyze_face
image: [File] (JPG/PNG/WEBP, max 5MB)
```

**Response Success**
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

**Response Error**
```json
{
  "success": false,
  "message": "Chỉ chấp nhận file JPG, PNG, WEBP"
}
```

**Validation Rules:**
- File types: `image/jpeg`, `image/jpg`, `image/png`, `image/webp`
- Max size: 5MB
- Required: `image` file

##### 2.2. Get Hairstyle Services

**Action**: `get_hairstyle_services`

**Request**
```json
{
  "action": "get_hairstyle_services",
  "hairstyle": "Undercut Fade"
}
```

**Response**
```json
{
  "success": true,
  "services": [
    {
      "id": 1,
      "name": "Cắt tóc nam",
      "price": 100000,
      "duration": 30
    }
  ]
}
```

#### Example
```javascript
const formData = new FormData();
formData.append('action', 'analyze_face');
formData.append('image', fileInput.files[0]);

fetch('/api/ai-hair-consultant.php', {
    method: 'POST',
    body: formData
})
.then(res => res.json())
.then(data => console.log(data));
```

---

### 3. AI Report Analysis API

**Endpoint**: `/api/ai-report-analysis.php`

**Method**: `GET`

**Auth**: Required (Admin only)

**Description**: Phân tích báo cáo kinh doanh tự động, đưa ra insights và gợi ý.

#### Request
```
GET /api/ai-report-analysis.php
```

**Headers:**
```
Cookie: PHPSESSID=xxx
```

#### Response Success
```json
{
  "success": true,
  "analysis": "📊 PHÂN TÍCH TÌNH HÌNH KINH DOANH...",
  "data": {
    "period": {
      "today": "2025-12-10",
      "this_month": "2025-12",
      "this_year": "2025"
    },
    "revenue": {
      "today": 150000,
      "this_month": 5000000,
      "last_month": 4500000,
      "growth_rate": 11.11
    },
    "bookings": {
      "today": 5,
      "this_month": 50,
      "last_month": 45,
      "growth_rate": 11.11
    },
    "status": {
      "pending": 5,
      "confirmed": 10,
      "completed": 30,
      "cancelled": 5,
      "completion_rate": 60,
      "cancellation_rate": 10
    },
    "top_services": [...],
    "top_staff": [...]
  }
}
```

#### Response No Data
```json
{
  "success": true,
  "analysis": "⚠️ Chưa có dữ liệu booking...",
  "data": {
    "has_data": false,
    "message": "Chưa có dữ liệu booking"
  }
}
```

#### Response Unauthorized
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

#### Example
```javascript
fetch('/api/ai-report-analysis.php', {
    method: 'GET',
    credentials: 'same-origin' // Important!
})
.then(res => res.json())
.then(data => console.log(data));
```

---

## 📅 Booking APIs

### 4. Get Staff by Service

**Endpoint**: `/api/get-staff.php`

**Method**: `GET`

**Description**: Lấy danh sách nhân viên có thể thực hiện dịch vụ.

#### Request
```
GET /api/get-staff.php?service_id=1
```

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| service_id | int | Yes | ID của dịch vụ |

#### Response Success
```json
{
  "success": true,
  "staff": [
    {
      "staff_id": 1,
      "user_id": 4,
      "full_name": "Lê Thị Châu",
      "phone": "0934567890",
      "specialization": "Cắt tóc, Nhuộm tóc",
      "experience_years": 5,
      "rating": 4.80,
      "total_bookings": 150,
      "status": "active"
    }
  ]
}
```

#### Response Error
```json
{
  "success": false,
  "message": "Invalid service ID"
}
```

#### Example
```javascript
fetch('/api/get-staff.php?service_id=1')
.then(res => res.json())
.then(data => console.log(data.staff));
```

---

### 5. Get Available Time Slots

**Endpoint**: `/api/get-time-slots.php`

**Method**: `GET`

**Description**: Lấy các khung giờ trống của nhân viên trong ngày.

#### Request
```
GET /api/get-time-slots.php?staff_id=1&date=2025-12-15&duration=30
```

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| staff_id | int | Yes | ID nhân viên |
| date | string | Yes | Ngày (YYYY-MM-DD) |
| duration | int | No | Thời gian dịch vụ (phút), default: 30 |

#### Response Success
```json
{
  "success": true,
  "slots": [
    {
      "time": "08:00",
      "available": true
    },
    {
      "time": "08:30",
      "available": true
    },
    {
      "time": "09:00",
      "available": false
    }
  ]
}
```

#### Response Error
```json
{
  "success": false,
  "message": "Invalid date format"
}
```

**Validation:**
- Date format: `YYYY-MM-DD`
- Date không được trong quá khứ
- staff_id > 0

#### Example
```javascript
fetch('/api/get-time-slots.php?staff_id=1&date=2025-12-15&duration=60')
.then(res => res.json())
.then(data => {
    const availableSlots = data.slots.filter(s => s.available);
    console.log(availableSlots);
});
```

---

## 📊 Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Not logged in |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error |

---

## ⚠️ Error Handling

### Standard Error Response
```json
{
  "success": false,
  "message": "Error description",
  "error_code": "ERROR_CODE" // Optional
}
```

### Common Errors

#### 1. Authentication Error
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

#### 2. Validation Error
```json
{
  "success": false,
  "message": "Vui lòng nhập tin nhắn"
}
```

#### 3. AI API Error
```json
{
  "success": false,
  "message": "Xin lỗi, tôi đang gặp sự cố kỹ thuật. Vui lòng thử lại sau."
}
```

#### 4. Quota Exceeded
```json
{
  "success": false,
  "message": "API quota exceeded. Please try again later."
}
```

---

## 🔄 Rate Limiting

### Chatbot API
- **Limit**: 10 requests/phút, 50 requests/giờ
- **Header**: `X-RateLimit-Remaining`

### Hair Consultant API
- **Limit**: 5 requests/phút
- **File size**: Max 5MB

### Report Analysis API
- **Limit**: 10 requests/phút (Admin only)

---

## 🧪 Testing

### Test với cURL

#### Chatbot
```bash
curl -X POST http://localhost/Website_DatLich/api/chatbot.php \
  -H "Content-Type: application/json" \
  -d '{"message":"Giá cắt tóc bao nhiêu?"}'
```

#### Hair Consultant
```bash
curl -X POST http://localhost/Website_DatLich/api/ai-hair-consultant.php \
  -F "action=analyze_face" \
  -F "image=@/path/to/image.jpg"
```

#### Get Staff
```bash
curl http://localhost/Website_DatLich/api/get-staff.php?service_id=1
```

#### Get Time Slots
```bash
curl "http://localhost/Website_DatLich/api/get-time-slots.php?staff_id=1&date=2025-12-15&duration=30"
```

---

## 📝 Best Practices

### 1. Always check success flag
```javascript
if (data.success) {
    // Handle success
} else {
    // Handle error
    console.error(data.message);
}
```

### 2. Handle network errors
```javascript
fetch('/api/chatbot.php', {...})
.then(res => res.json())
.then(data => {...})
.catch(error => {
    console.error('Network error:', error);
});
```

### 3. Validate input before sending
```javascript
if (!message.trim()) {
    alert('Vui lòng nhập tin nhắn');
    return;
}
```

### 4. Show loading state
```javascript
button.disabled = true;
button.textContent = 'Đang xử lý...';

fetch('/api/chatbot.php', {...})
.finally(() => {
    button.disabled = false;
    button.textContent = 'Gửi';
});
```

---

## 🔧 Troubleshooting

### API không trả về dữ liệu
```
Kiểm tra:
1. URL đúng chưa
2. Method đúng chưa (GET/POST)
3. Headers đúng chưa
4. Console có lỗi gì không
```

### CORS Error
```
Giải pháp:
1. Kiểm tra Access-Control-Allow-Origin header
2. Sử dụng credentials: 'same-origin'
3. Kiểm tra .htaccess
```

### 401 Unauthorized
```
Giải pháp:
1. Kiểm tra đã đăng nhập chưa
2. Session có còn hiệu lực không
3. Cookie có được gửi không
```

---

## 📞 Support

Nếu gặp vấn đề với API:
- Email: dminhhieu2408@gmail.com
- Phone: 0976985305

---

**Cập nhật lần cuối**: 26 tháng 12, 2025
