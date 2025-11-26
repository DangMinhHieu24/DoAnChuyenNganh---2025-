# 🤖 Chatbot AI System - Tổng Quan

## 📌 Giới Thiệu

Hệ thống Chatbot AI được tích hợp vào dự án Salon Booking, sử dụng **Google Gemini 2.0 Flash** - model AI mới nhất (tính đến 15/11/2025) để cung cấp trợ lý ảo thông minh 24/7.

## 🎯 Mục Đích

- Tự động trả lời câu hỏi khách hàng
- Giảm tải công việc cho nhân viên
- Tăng trải nghiệm người dùng
- **Đặt lịch tự động qua chat** (không cần chuyển trang)
- Cung cấp thông tin chính xác từ database
- Conversation flow thông minh

## 🏗️ Kiến Trúc Hệ Thống

```
┌─────────────────┐
│   User (Web)    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Chatbot Widget │ (chatbot.js + chatbot.css)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  API Endpoint   │ (api/chatbot.php)
└────────┬────────┘
         │
    ┌────┴────┐
    ▼         ▼
┌────────┐ ┌──────────┐
│Database│ │ Gemini   │
│ MySQL  │ │ API      │
└────────┘ └──────────┘
```

## 📦 Các Thành Phần

### 1. Frontend (Giao diện)
- **chatbot.css** - Styling, animation, responsive, booking UI
- **chatbot.js** - Logic xử lý chat, UI interaction, booking flow
- **chatbot-widget.php** - Include widget vào trang

### 2. Backend (Xử lý)
- **chatbot.php** - API chính, xử lý tin nhắn với Gemini
- **chatbot-booking.php** - API đặt lịch tự động (NEW!)
- **chatbot-actions.php** - API phụ (check slot, get info...)
- **chatbot-config.php** - Cấu hình hệ thống

### 3. AI Integration
- **Gemini 1.5 Flash** - Model AI xử lý ngôn ngữ tự nhiên
- **Intent Analysis** - Phân tích ý định người dùng
- **Context Management** - Quản lý ngữ cảnh hội thoại

### 4. Database Integration
- **Service Model** - Lấy thông tin dịch vụ
- **Staff Model** - Lấy thông tin nhân viên
- **Booking Model** - Xử lý đặt lịch

## 🔄 Luồng Hoạt Động

### 1. User gửi tin nhắn
```
User: "Giá cắt tóc bao nhiêu?"
```

### 2. Frontend xử lý
- Validate input
- Hiển thị tin nhắn user
- Hiển thị typing indicator
- Gọi API

### 3. Backend phân tích
- Nhận tin nhắn
- Phân tích intent → "price_inquiry"
- Lấy data từ database → Danh sách dịch vụ cắt tóc
- Tạo system prompt với context

### 4. Gọi Gemini API
```
System Prompt: "Bạn là trợ lý AI... [context về dịch vụ]"
User Message: "Giá cắt tóc bao nhiêu?"
```

### 5. Gemini trả lời
```
"Chúng tôi có các dịch vụ cắt tóc:
- Cắt tóc nam: 100,000đ (30 phút)
- Cắt tóc nữ: 150,000đ (45 phút)
- Cắt tóc trẻ em: 80,000đ (20 phút)
Bạn muốn đặt lịch dịch vụ nào? 💇‍♂️"
```

### 6. Frontend hiển thị
- Ẩn typing indicator
- Hiển thị tin nhắn bot
- Hiển thị quick replies

## 🎨 Tính Năng Nổi Bật

### 1. Intent Recognition (Nhận diện ý định)
Chatbot tự động nhận diện các intent:
- `price_inquiry` - Hỏi về giá
- `list_services` - Xem dịch vụ
- `staff_inquiry` - Hỏi về nhân viên
- `booking` - Đặt lịch
- `check_availability` - Kiểm tra lịch trống
- `working_hours` - Giờ làm việc
- `contact_info` - Thông tin liên hệ
- `general` - Câu hỏi chung

### 2. Context-Aware Responses
- Lấy dữ liệu thực tế từ database
- Trả lời chính xác về giá, dịch vụ
- Cung cấp thông tin nhân viên cụ thể

### 3. Natural Language Understanding
- Hiểu tiếng Việt tự nhiên
- Xử lý nhiều cách hỏi khác nhau
- Trả lời câu hỏi ngoài luồng

### 4. Quick Replies
- Gợi ý câu hỏi tiếp theo
- Dễ dàng điều hướng hội thoại
- Tăng tương tác người dùng

### 5. Responsive Design
- Hoạt động tốt trên mọi thiết bị
- Animation mượt mà
- UI/UX hiện đại

## 📊 Thông Số Kỹ Thuật

### Model AI
- **Name:** Gemini 2.0 Flash
- **Provider:** Google AI
- **Version:** Latest (Nov 2025)
- **Language:** Vietnamese + English

### API Configuration
- **Temperature:** 0.7 (Cân bằng sáng tạo/chính xác)
- **Max Tokens:** 1024
- **Top K:** 40
- **Top P:** 0.95

### Rate Limits
- **Free Tier:** 15 requests/minute, 1500 requests/day
- **Response Time:** ~1-3 seconds
- **Timeout:** 30 seconds

### Performance
- **Widget Load:** < 100ms
- **API Response:** 1-3s (tùy Gemini)
- **Database Query:** < 50ms
- **Total Response:** 1-4s

## 🔐 Bảo Mật

### API Key Protection
- Lưu trong file config riêng
- Không commit lên Git
- Sử dụng .gitignore

### Input Validation
- Sanitize user input
- Validate message length
- Rate limiting

### Error Handling
- Graceful degradation
- User-friendly error messages
- Logging errors

## 💰 Chi Phí

### Gemini API (Free Tier)
- **Miễn phí:** 15 requests/phút
- **Giới hạn:** 1500 requests/ngày
- **Ước tính:** Đủ cho ~500 khách/ngày

### Nâng cấp (Nếu cần)
- **Pay-as-you-go:** $0.00025/request
- **Ước tính:** $0.25 cho 1000 requests

## 📈 Khả Năng Mở Rộng

### Hiện tại
✅ Trả lời câu hỏi
✅ Cung cấp thông tin
✅ **Đặt lịch tự động hoàn toàn** (NEW!)
✅ Conversation flow thông minh
✅ Kiểm tra slot trống real-time
✅ Session management

### Tương lai (Có thể phát triển)
🔄 Tích hợp thanh toán online
🔄 Gửi email/SMS xác nhận tự động
🔄 Chatbot voice (speech-to-text)
🔄 Multi-language support
🔄 Sentiment analysis
🔄 Personalized recommendations
🔄 AI-powered rescheduling

## 📚 Tài Liệu

### Hướng dẫn
- `CHATBOT_QUICKSTART.md` - Bắt đầu nhanh (5 phút)
- `CHATBOT_SETUP.md` - Hướng dẫn chi tiết
- `CHATBOT_CHECKLIST.md` - Checklist cài đặt

### Demo & Test
- `chatbot-demo.html` - Trang demo
- `test-chatbot-api.php` - Test API

### Configuration
- `config/chatbot-config.php` - File cấu hình chính
- `config/chatbot-config.example.php` - File mẫu

## 🎓 Best Practices

### 1. System Prompt Design
- Rõ ràng, cụ thể
- Cung cấp đủ context
- Hướng dẫn tone & style

### 2. Error Handling
- Luôn có fallback response
- User-friendly messages
- Log errors để debug

### 3. Performance
- Cache responses nếu có thể
- Optimize database queries
- Monitor API usage

### 4. User Experience
- Fast response time
- Clear, concise answers
- Helpful quick replies

## 🔍 Monitoring & Analytics

### Metrics cần theo dõi
- Số lượng tin nhắn/ngày
- Response time trung bình
- API success rate
- Các câu hỏi phổ biến
- User satisfaction

### Tools
- Google Analytics (nếu tích hợp)
- Custom logging
- API usage dashboard

## 🚀 Deployment

### Development
```bash
# Local testing
http://localhost/Website_DatLich
```

### Production
- Cấu hình HTTPS
- Bảo mật API key
- Monitor performance
- Backup configuration

## 📞 Support

### Documentation
- Đọc các file CHATBOT_*.md
- Xem code comments
- Check examples

### Contact
- Email: dminhhieu240@gmail.com
- Phone: 0976985305

### Community
- GitHub Issues
- Stack Overflow
- Google AI Community

## 🎉 Kết Luận

Chatbot AI này là một giải pháp hoàn chỉnh, hiện đại và dễ sử dụng cho hệ thống Salon Booking. Với sự kết hợp của:

✅ **AI mạnh mẽ** (Gemini 1.5 Flash)
✅ **Database tích hợp** (MySQL)
✅ **UI/UX đẹp** (Modern design)
✅ **Dễ cài đặt** (5 phút setup)
✅ **Miễn phí** (Free tier đủ dùng)

Chatbot sẽ giúp nâng cao trải nghiệm khách hàng và giảm tải công việc cho nhân viên một cách đáng kể.

---

**Version:** 1.0.0
**Last Updated:** 16/11/2025
**Author:** Đặng Minh Hiếu
