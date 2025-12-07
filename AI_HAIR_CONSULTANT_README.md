# 🎨 AI Hair Consultant - Tư Vấn Kiểu Tóc Thông Minh

Tính năng AI phân tích khuôn mặt và gợi ý kiểu tóc phù hợp sử dụng Google Gemini 2.5 Flash (multimodal).

## 📋 Tổng Quan

AI Hair Consultant là công cụ tư vấn kiểu tóc thông minh giúp khách hàng:
- 📸 Upload ảnh selfie
- 🧠 AI phân tích khuôn mặt, màu da, đặc điểm
- 💡 Nhận gợi ý 3-4 kiểu tóc phù hợp nhất
- 📝 Giải thích chi tiết lý do phù hợp
- 📅 Đặt lịch ngay với kiểu tóc đã chọn

## 🎯 Tính Năng

### 1. Upload Ảnh Thông Minh
- **Drag & Drop**: Kéo thả ảnh vào khung
- **Click to Upload**: Click chọn ảnh từ máy
- **Preview**: Xem trước ảnh đã chọn
- **Validation**: Kiểm tra định dạng và kích thước
  - Định dạng: JPG, PNG, WEBP
  - Kích thước tối đa: 5MB

### 2. AI Phân Tích Khuôn Mặt
AI sẽ phân tích:
- **Hình dạng khuôn mặt**: Tròn, vuông, dài, trái xoan, tim...
- **Đặc điểm**: Trán, má, cằm, tỷ lệ khuôn mặt
- **Màu da**: Tông da (trắng, ngăm, bánh mật...)
- **Phong cách hiện tại**: Kiểu tóc đang có (nếu thấy)

### 3. Gợi Ý Kiểu Tóc
Mỗi gợi ý bao gồm:
- **Tên kiểu tóc**: Tên cụ thể và mô tả
- **Đánh giá**: Rating từ 1-5 sao
- **Lý do phù hợp**: Giải thích chi tiết
- **Dịch vụ cần**: Cắt/Nhuộm/Uốn...
- **Độ khó**: Dễ/Trung bình/Khó

### 4. Tích Hợp Booking
- Click "Đặt Lịch Ngay" để chuyển đến trang booking
- Thông tin kiểu tóc được lưu trong session
- Dễ dàng đặt lịch với stylist

## 🔧 Cài Đặt

### 1. Gemini API Key
Tính năng này sử dụng chung API key với Chatbot:
```php
// File: config/chatbot-config.php
define('GEMINI_API_KEY', 'YOUR_API_KEY_HERE');
define('GEMINI_MODEL', 'gemini-2.5-flash'); // Model multimodal hỗ trợ Vision
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1/models/' . GEMINI_MODEL . ':generateContent');
```

**Lưu ý:** Model `gemini-2.5-flash` là multimodal, hỗ trợ cả text và image.

### 2. Không Cần Cài Đặt Thêm
- Sử dụng cùng API key với chatbot
- Không cần cấu hình riêng
- Tự động hoạt động sau khi có API key

## 📁 Cấu Trúc File

```
AI Hair Consultant/
├── pages/
│   └── ai-hair-consultant.php       # Trang chính
├── api/
│   └── ai-hair-consultant.php       # API xử lý ảnh
├── assets/
│   ├── css/
│   │   └── ai-hair-consultant.css   # Styling hiện đại
│   └── js/
│       └── ai-hair-consultant.js    # Upload & display logic
└── config/
    └── chatbot-config.php           # Cấu hình API (dùng chung)
```

## 🔄 Luồng Hoạt Động

```
1. User truy cập menu "AI Tư Vấn"
   ↓
2. Upload ảnh selfie (drag & drop hoặc click)
   ↓
3. Validate file (type, size)
   ↓
4. Preview ảnh
   ↓
5. Click "Phân Tích Ngay"
   ↓
6. JavaScript gửi FormData đến api/ai-hair-consultant.php
   ↓
7. API xử lý:
   - Validate file
   - Convert ảnh sang base64
   - Tạo prompt với thông tin salon
   - Gửi đến Gemini Vision API
   ↓
8. Gemini Vision phân tích:
   - Nhận ảnh + prompt
   - Phân tích khuôn mặt
   - Gợi ý kiểu tóc phù hợp
   ↓
9. API trả về JSON:
   {
     success: true,
     analysis: "...",
     suggestions: [...]
   }
   ↓
10. JavaScript hiển thị kết quả:
    - Ảnh của user
    - Phân tích chi tiết
    - Gợi ý kiểu tóc
    - Button đặt lịch
```

## 🎨 Giao Diện Hiện Đại

### Hero Section
- Gradient background (purple to pink)
- Radial overlays cho depth
- Floating animation
- Feature badges với glassmorphism

### Upload Area
- Glassmorphism card
- Dashed border với hover effect
- Shimmer animation
- Drag & drop support
- Preview với zoom effect

### Result Display
- Glassmorphism cards
- Gradient text headers
- Smooth animations
- Hairstyle items với số thứ tự
- Action buttons với 3D effect

### How It Works
- 4 step cards
- Floating numbers
- Hover transform effects
- Icon animations

## 🧪 Testing

### Test Upload
```
1. Vào trang AI Tư Vấn
2. Kéo thả ảnh hoặc click chọn
3. Kiểm tra preview hiển thị đúng
4. Click "Phân Tích Ngay"
5. Xem loading state
```

### Test AI Analysis
```
1. Upload ảnh selfie rõ mặt
2. Đợi AI phân tích (5-10 giây)
3. Kiểm tra kết quả:
   - Phân tích khuôn mặt
   - 3-4 gợi ý kiểu tóc
   - Mỗi gợi ý có rating và lý do
   - Button đặt lịch hoạt động
```

### Test Error Handling
```
1. Upload file không đúng định dạng → Hiện lỗi
2. Upload file quá lớn (>5MB) → Hiện lỗi
3. API key sai → Hiện lỗi kết nối
4. Network error → Hiện lỗi timeout
```

## 🐛 Xử Lý Lỗi

### Lỗi Upload
```
Error: "Chỉ chấp nhận file JPG, PNG, WEBP"
Fix: Chọn đúng định dạng ảnh
```

### Lỗi Kích Thước
```
Error: "Ảnh quá lớn. Tối đa 5MB"
Fix: Nén ảnh hoặc chọn ảnh nhỏ hơn
```

### Lỗi API
```
Error: "API trả về lỗi: 400/404/500"
Fix: 
- Kiểm tra API key trong config/chatbot-config.php
- Đảm bảo dùng model 'gemini-2.5-flash'
- Kiểm tra API endpoint dùng v1 (không phải v1beta)
- Kiểm tra network connection
```

### Lỗi 429 - Quota Exceeded
```
Error: "You exceeded your current quota"
Fix:
- API key đã hết quota miễn phí
- Đợi đến 7:00 sáng (quota reset)
- Hoặc tạo API key mới
- Xem chi tiết: API_QUOTA_GUIDE.md
```

### Lỗi Phân Tích
```
Error: "Không nhận được phân tích từ AI"
Fix:
- Thử lại với ảnh khác (ảnh rõ mặt hơn)
- Đảm bảo ảnh có khuôn mặt rõ ràng
- Kiểm tra API quota còn không
- Restart Apache sau khi sửa config
```

## ⚙️ Tùy Chỉnh

### Thay Đổi Prompt

Sửa file `api/ai-hair-consultant.php`, function `buildHairConsultantPrompt()`:

```php
$prompt = <<<PROMPT
Bạn là chuyên gia tư vấn kiểu tóc...

NHIỆM VỤ:
[Thay đổi nhiệm vụ của AI]

PHÂN TÍCH:
[Thay đổi cách phân tích]

GỢI Ý:
[Thay đổi format gợi ý]
PROMPT;
```

### Thay Đổi Số Lượng Gợi Ý

Trong prompt, thay đổi:
```
Đưa ra 3-4 kiểu tóc phù hợp nhất
→
Đưa ra 5-6 kiểu tóc phù hợp nhất
```

### Thay Đổi Màu Sắc

Sửa file `assets/css/ai-hair-consultant.css`:

```css
/* Gradient chính */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Thay đổi thành màu khác */
background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
```

### Thêm Validation

Sửa file `assets/js/ai-hair-consultant.js`:

```javascript
function validateAndPreviewImage(file) {
    // Thêm validation mới
    if (file.size < 100 * 1024) {
        showAlert('Ảnh quá nhỏ. Tối thiểu 100KB', 'error');
        return;
    }
    
    // ... code hiện tại
}
```

## 📊 API Response Format

### Success Response
```json
{
  "success": true,
  "analysis": "**PHÂN TÍCH KHUÔN MẶT:**\n...\n\n**GỢI Ý KIỂU TÓC:**\n...",
  "suggestions": [
    {
      "name": "Tóc bob ngắn",
      "icon": "💇‍♀️"
    }
  ],
  "message": "Phân tích thành công! 🎨"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Vui lòng upload ảnh"
}
```

## 🎯 Best Practices

### Cho User
1. **Ảnh chất lượng**: Chụp ảnh rõ mặt, ánh sáng tốt
2. **Không che mặt**: Không đeo kính, mũ, khẩu trang
3. **Góc chụp**: Chụp thẳng mặt, không nghiêng quá
4. **Background**: Nền đơn giản, không rối
5. **Kích thước**: Ảnh không quá nhỏ (>500KB tốt nhất)

### Cho Developer
1. **Error Handling**: Luôn có fallback response
2. **Loading State**: Hiển thị rõ ràng khi đang xử lý
3. **Validation**: Validate cả client và server side
4. **Security**: Không lưu ảnh user lâu dài
5. **Performance**: Optimize ảnh trước khi gửi API

## 🔐 Bảo Mật

- ✅ File validation (type, size)
- ✅ Base64 encoding an toàn
- ✅ Không lưu ảnh vào server
- ✅ Session-based tracking
- ✅ API key không expose ra client

## 💡 Tips

1. **Ảnh tốt = Kết quả tốt**: Khuyến khích user upload ảnh chất lượng
2. **Prompt Engineering**: Điều chỉnh prompt để có kết quả tốt hơn
3. **Cache Results**: Lưu kết quả vào session để tránh gọi API lại
4. **Rate Limiting**: Giới hạn số lần phân tích/user/ngày
5. **Analytics**: Track usage để cải thiện

## 🚀 Nâng Cấp Tương Lai

- [ ] Multiple image upload (front, side views)
- [ ] Virtual try-on (AR)
- [ ] Save favorite hairstyles
- [ ] Share results on social media
- [ ] Compare before/after
- [ ] AI-generated hairstyle images
- [ ] Skin tone analysis for hair color
- [ ] Face shape detection with ML

## 📞 Hỗ Trợ

Nếu gặp vấn đề:
1. Kiểm tra console log (F12)
2. Kiểm tra Network tab để xem API response
3. Kiểm tra PHP error log
4. Test với ảnh khác
5. Verify API key còn quota

---

## 📚 Tài Liệu Liên Quan

- **API_QUOTA_GUIDE.md** - Hướng dẫn quản lý quota
- **CHATBOT_README.md** - Hướng dẫn chatbot (dùng chung API key)
- **README_SETUP.md** - Hướng dẫn cài đặt chi tiết

## 🔗 Links Hữu Ích

- **Gemini Vision API:** https://ai.google.dev/tutorials/vision_quickstart
- **API Key Management:** https://makersuite.google.com/app/apikey
- **Model List:** https://ai.google.dev/models/gemini
- **Usage Dashboard:** https://ai.dev/usage

---

**Model:** Gemini 2.5 Flash (Multimodal - Text + Image)  
**API Version:** v1  
**Free Tier:** 15 RPM, 1,500 RPD  
**Cập nhật:** December 7, 2025
