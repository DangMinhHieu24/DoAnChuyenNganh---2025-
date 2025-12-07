# 🔒 Hướng Dẫn Bảo Mật API Key

## ⚠️ QUAN TRỌNG: Đã làm gì?

Tôi đã thiết lập bảo mật cho project của bạn:

### 1. ✅ Tạo file `.gitignore`
File này ngăn các file nhạy cảm bị push lên GitHub:
- `config/chatbot-config.php` - Chứa GEMINI_API_KEY
- `config/config.php` - Chứa thông tin cấu hình
- `config/database.php` - Chứa thông tin database
- `uploads/*` - File upload của user

### 2. ✅ Cập nhật file example
- `config/chatbot-config.example.php` đã được làm sạch API key
- Thay API key thật bằng `YOUR_GEMINI_API_KEY_HERE`

## 📋 Các Bước Tiếp Theo

### Bước 1: Kiểm tra Git status
```bash
git status
```

### Bước 2: Xóa file nhạy cảm khỏi Git history (nếu đã commit trước đó)
```bash
# Xóa file khỏi Git nhưng giữ lại trên máy local
git rm --cached config/chatbot-config.php
git rm --cached config/config.php
git rm --cached config/database.php

# Commit thay đổi
git add .gitignore
git commit -m "Add .gitignore and remove sensitive files"
```

### Bước 3: Push lên GitHub
```bash
git push origin main
```

## 🚨 Nếu Đã Push API Key Lên GitHub

Nếu bạn đã vô tình push API key lên GitHub trước đó:

### 1. **HỦY API KEY CŨ NGAY LẬP TỨC**
- Truy cập: https://makersuite.google.com/app/apikey
- Xóa API key cũ
- Tạo API key mới

### 2. **Xóa Git History** (Tùy chọn - Nguy hiểm!)
```bash
# Cách 1: Xóa toàn bộ history (đơn giản nhưng mất lịch sử)
rm -rf .git
git init
git add .
git commit -m "Initial commit with security"
git remote add origin <your-repo-url>
git push -u --force origin main

# Cách 2: Dùng BFG Repo-Cleaner (an toàn hơn)
# Download: https://rtyley.github.io/bfg-repo-cleaner/
java -jar bfg.jar --replace-text passwords.txt
git reflog expire --expire=now --all
git gc --prune=now --aggressive
git push --force
```

## 📝 Hướng Dẫn Cho Người Khác Clone Project

Khi bạn hoặc người khác clone project về máy mới:

### 1. Clone repository
```bash
git clone <your-repo-url>
cd <project-folder>
```

### 2. Copy file example thành file config thật
```bash
# Windows (CMD)
copy config\chatbot-config.example.php config\chatbot-config.php

# Windows (PowerShell)
Copy-Item config\chatbot-config.example.php config\chatbot-config.php

# Mac/Linux
cp config/chatbot-config.example.php config/chatbot-config.php
```

### 3. Mở file `config/chatbot-config.php` và điền API key
```php
// Thay YOUR_GEMINI_API_KEY_HERE bằng API key thật của bạn
define('GEMINI_API_KEY', 'AIzaSy...');
```

### 4. Xong! Website chạy bình thường

### 3. Tạo file config.example.php (nếu chưa có)
Bạn nên tạo thêm file `config/config.example.php`:
```php
<?php
define('BASE_URL', 'http://localhost/Website_DatLich');
define('SITE_NAME', 'eBooking');
define('SITE_EMAIL', 'your-email@example.com');
define('SITE_PHONE', 'your-phone');
define('SITE_ADDRESS', 'your-address');
// ... các config khác
?>
```

## ✅ Checklist Bảo Mật

- [x] Tạo `.gitignore`
- [x] Thêm các file config vào `.gitignore`
- [x] Làm sạch API key trong file `.example.php`
- [ ] Xóa file nhạy cảm khỏi Git cache
- [ ] Commit và push `.gitignore`
- [ ] Kiểm tra GitHub - đảm bảo không thấy API key
- [ ] Tạo file `config.example.php` cho config chính
- [ ] Cập nhật README.md với hướng dẫn setup

## 🔐 Best Practices

1. **Không bao giờ** commit API key trực tiếp
2. **Luôn** dùng file `.example` cho template
3. **Kiểm tra** `git status` trước khi commit
4. **Xem lại** code trước khi push
5. **Rotate** API key định kỳ
6. **Sử dụng** environment variables cho production

## 📚 Tài Liệu Tham Khảo

- [GitHub - Removing sensitive data](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/removing-sensitive-data-from-a-repository)
- [BFG Repo-Cleaner](https://rtyley.github.io/bfg-repo-cleaner/)
- [Git - gitignore](https://git-scm.com/docs/gitignore)
