# ✅ Chatbot AI - Installation Checklist

## 📋 Danh sách kiểm tra cài đặt

### 1. Files đã được tạo
- [x] `api/chatbot.php` - API xử lý chat
- [x] `api/chatbot-actions.php` - API actions bổ sung
- [x] `assets/css/chatbot.css` - Styling chatbot
- [x] `assets/js/chatbot.js` - Logic chatbot
- [x] `config/chatbot-config.php` - Cấu hình chatbot
- [x] `config/chatbot-config.example.php` - File mẫu
- [x] `includes/chatbot-widget.php` - Widget include
- [x] `chatbot-demo.html` - Trang demo
- [x] `test-chatbot-api.php` - File test API
- [x] `CHATBOT_SETUP.md` - Hướng dẫn chi tiết
- [x] `CHATBOT_QUICKSTART.md` - Hướng dẫn nhanh

### 2. Cấu hình cần làm

#### ✅ Bắt buộc
- [ ] Lấy Gemini API Key từ https://makersuite.google.com/app/apikey
- [ ] Cập nhật `GEMINI_API_KEY` trong `config/chatbot-config.php`
- [ ] Kiểm tra chatbot đã hiển thị trên website

#### 🎨 Tùy chọn (Khuyến nghị)
- [ ] Cập nhật `SALON_NAME` - Tên salon của bạn
- [ ] Cập nhật `SALON_ADDRESS` - Địa chỉ
- [ ] Cập nhật `SALON_PHONE` - Số điện thoại
- [ ] Cập nhật `SALON_EMAIL` - Email liên hệ
- [ ] Cập nhật `WORKING_HOURS_WEEKDAY` - Giờ làm việc ngày thường
- [ ] Cập nhật `WORKING_HOURS_WEEKEND` - Giờ làm việc cuối tuần

#### 🎨 Tùy chỉnh giao diện (Tùy chọn)
- [ ] Thay đổi màu sắc trong `assets/css/chatbot.css`
- [ ] Thay đổi vị trí chatbot (bottom-right/bottom-left)
- [ ] Thay đổi kích thước chatbot

### 3. Testing

#### Test cơ bản
- [ ] Mở website
- [ ] Click icon chatbot
- [ ] Gửi tin nhắn "Xin chào"
- [ ] Nhận được phản hồi từ AI

#### Test các tính năng
- [ ] Hỏi về dịch vụ: "Có những dịch vụ gì?"
- [ ] Hỏi về giá: "Giá cắt tóc bao nhiêu?"
- [ ] Hỏi về nhân viên: "Nhân viên nào giỏi?"
- [ ] **Đặt lịch tự động:** Click "📅 Đặt lịch" hoặc gõ "Tôi muốn đặt lịch"
  - [ ] Chọn dịch vụ
  - [ ] Chọn nhân viên
  - [ ] Chọn ngày
  - [ ] Chọn giờ
  - [ ] Xác nhận thành công
- [ ] Hỏi về giờ làm việc: "Giờ làm việc?"
- [ ] Hỏi về địa chỉ: "Địa chỉ salon?"

#### Test trên các thiết bị
- [ ] Desktop/Laptop
- [ ] Tablet
- [ ] Mobile phone

### 4. Tối ưu hóa (Tùy chọn)

#### Performance
- [ ] Kiểm tra tốc độ phản hồi
- [ ] Kiểm tra rate limiting
- [ ] Monitor API quota

#### Security
- [ ] Thêm `config/chatbot-config.php` vào `.gitignore`
- [ ] Không commit API key lên Git
- [ ] Kiểm tra input validation

#### Analytics (Nếu cần)
- [ ] Bật chat logging
- [ ] Theo dõi số lượng tin nhắn
- [ ] Phân tích câu hỏi phổ biến

### 5. Troubleshooting

Nếu gặp vấn đề, kiểm tra:
- [ ] API key đã đúng chưa
- [ ] Quota API còn không (15 requests/phút)
- [ ] Console browser có lỗi không (F12)
- [ ] PHP error log
- [ ] Network tab trong DevTools

### 6. Documentation

- [ ] Đọc `CHATBOT_SETUP.md` - Hướng dẫn chi tiết
- [ ] Đọc `CHATBOT_QUICKSTART.md` - Bắt đầu nhanh
- [ ] Xem `chatbot-demo.html` - Demo trực quan
- [ ] Chạy `test-chatbot-api.php` - Test API

## 🎯 Mục tiêu hoàn thành

### Mức độ cơ bản (Bắt buộc)
✅ Chatbot hiển thị và hoạt động
✅ Trả lời được câu hỏi cơ bản
✅ Tích hợp với database

### Mức độ nâng cao (Khuyến nghị)
✅ Thông tin salon đã được cập nhật
✅ Giao diện đã được tùy chỉnh
✅ **Đặt lịch tự động hoạt động**
✅ Test trên nhiều thiết bị

### Mức độ chuyên nghiệp (Tùy chọn)
✅ Có logging và analytics
✅ Đã tối ưu performance
✅ Đã bảo mật API key
✅ Session management ổn định

## 📊 Kết quả mong đợi

Sau khi hoàn thành checklist này, bạn sẽ có:

✅ Chatbot AI hoạt động 24/7
✅ Trả lời tự động câu hỏi khách hàng
✅ Hỗ trợ đặt lịch qua chat
✅ Tích hợp hoàn hảo với website
✅ Giao diện đẹp, responsive
✅ Trải nghiệm người dùng tốt

## 🚀 Bước tiếp theo

Sau khi chatbot hoạt động:

1. **Thu thập feedback** từ người dùng
2. **Phân tích** các câu hỏi phổ biến
3. **Cải thiện** system prompt để trả lời tốt hơn
4. **Mở rộng** tính năng (đặt lịch tự động, thanh toán...)
5. **Tối ưu** hiệu suất và chi phí API

## 📞 Cần hỗ trợ?

- 📧 Email: dminhhieu240@gmail.com
- 📱 Hotline: 0976985305
- 📖 Docs: Xem các file CHATBOT_*.md

---

**Chúc bạn thành công! 🎉**

*Đánh dấu ✅ vào các mục đã hoàn thành*
