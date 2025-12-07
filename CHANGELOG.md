# 📝 Changelog - Lịch Sử Cập Nhật

## [2.1.0] - December 7, 2025

### ✨ Tính Năng Mới
- Nâng cấp lên **Gemini 2.5 Flash** (model multimodal mới nhất)
- Cải thiện AI Hair Consultant với gợi ý dịch vụ cụ thể
- Thêm phần gợi ý đặt lịch vào kết quả phân tích

### 🔧 Cải Tiến
- Chuyển từ API v1beta sang **API v1** (ổn định hơn)
- Cải thiện error handling và logging
- Tối ưu prompt engineering cho kết quả tốt hơn
- Thêm validation và error messages rõ ràng hơn

### 🐛 Sửa Lỗi
- Fix lỗi 429 Quota Exceeded
- Fix lỗi model not found với v1beta
- Fix lỗi AI không gợi ý dịch vụ salon
- Fix lỗi config không reload sau khi sửa

### 📚 Documentation
- Thêm **API_QUOTA_GUIDE.md** - Hướng dẫn quản lý quota
- Cập nhật **README.md** với thông tin model mới
- Cập nhật **CHATBOT_README.md** với API v1
- Cập nhật **AI_HAIR_CONSULTANT_README.md**
- Cải thiện **README_SETUP.md** với troubleshooting
- Cập nhật **SECURITY_GUIDE.md**

### 🔐 Bảo Mật
- Thêm `.gitignore` để bảo vệ API key
- Tạo `chatbot-config.example.php` làm template
- Hướng dẫn chi tiết về bảo mật API key

### 🗑️ Dọn Dẹp
- Xóa các file test không cần thiết
- Xóa file `PUSH_TO_GITHUB.md` tạm thời
- Xóa file `RESTART_APACHE.md` tạm thời

---

## [2.0.0] - November 2024

### ✨ Tính Năng Chính
- **AI Chatbot** với Gemini 2.0 Flash
- **AI Hair Consultant** với Gemini Vision
- Hệ thống đặt lịch online
- Admin panel quản lý
- Staff dashboard
- Customer portal

### 🎨 Giao Diện
- Modern UI với glassmorphism
- Gradient backgrounds
- Smooth animations
- Responsive design

### 🔧 Công Nghệ
- PHP 7.4+ với PDO
- MySQL database
- Bootstrap 5
- Vanilla JavaScript
- Google Gemini API

---

## [1.0.0] - October 2024

### 🚀 Ra Mắt
- Phiên bản đầu tiên
- Tính năng đặt lịch cơ bản
- Admin panel đơn giản
- Không có AI

---

## 📋 Kế Hoạch Tương Lai

### Version 2.2.0 (Q1 2026)
- [ ] Fallback system tự động chuyển model
- [ ] Cache responses để tiết kiệm quota
- [ ] Rate limiting per user
- [ ] Analytics dashboard
- [ ] Email notifications
- [ ] SMS notifications

### Version 3.0.0 (Q2 2026)
- [ ] Mobile app (React Native)
- [ ] Voice assistant
- [ ] Multi-language support
- [ ] Payment integration
- [ ] Loyalty program
- [ ] Social media integration

---

**Maintained by:** Đặng Minh Hiếu  
**Email:** dminhhieu2408@gmail.com  
**Repository:** https://github.com/DangMinhHieu24/DoAnChuyenNganh---2025-
