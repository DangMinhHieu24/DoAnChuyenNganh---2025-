# 🚀 Hướng Dẫn Push Lên GitHub

## Bước 1: Xóa file nhạy cảm khỏi Git cache

```bash
git rm --cached config/chatbot-config.php
```

## Bước 2: Kiểm tra trạng thái

```bash
git status
```

Bạn sẽ thấy:
- ✅ File mới: `.gitignore`, `SECURITY_GUIDE.md`, `README_SETUP.md`
- ✅ File đã xóa khỏi Git: `config/chatbot-config.php`
- ✅ File đã sửa: `config/chatbot-config.example.php`

## Bước 3: Add tất cả thay đổi

```bash
git add .
```

## Bước 4: Commit

```bash
git commit -m "Secure API key and update documentation"
```

## Bước 5: Push lên GitHub (thay thế code cũ)

```bash
git push origin main
```

Hoặc nếu branch của bạn là `master`:

```bash
git push origin master
```

## ✅ Kiểm tra trên GitHub

Sau khi push, vào GitHub và kiểm tra:

1. ✅ File `config/chatbot-config.php` **KHÔNG** có trên GitHub
2. ✅ File `config/chatbot-config.example.php` có và **KHÔNG** chứa API key thật
3. ✅ File `.gitignore` có và chặn `config/chatbot-config.php`

## 🔍 Nếu vẫn thấy API key trên GitHub

Nếu bạn đã push API key trước đó và vẫn thấy nó trong history:

### Cách 1: Xóa toàn bộ history (Đơn giản - Mất lịch sử commit)

```bash
# Backup code trước
# Xóa .git folder
rmdir /s /q .git

# Tạo repo mới
git init
git add .
git commit -m "Initial commit - Secured"

# Link với GitHub repo
git remote add origin https://github.com/username/repo-name.git

# Force push (ghi đè)
git push -u --force origin main
```

### Cách 2: Giữ history nhưng xóa file nhạy cảm

```bash
# Cài BFG Repo Cleaner
# Download: https://rtyley.github.io/bfg-repo-cleaner/

# Tạo file chứa API key cần xóa
echo AIzaSyBsYxlje5AFMNbIfwVnwi6AdYp5nCuODG4 > api-keys.txt

# Chạy BFG
java -jar bfg.jar --replace-text api-keys.txt

# Clean up
git reflog expire --expire=now --all
git gc --prune=now --aggressive

# Force push
git push --force
```

## ⚠️ QUAN TRỌNG

Sau khi push, **HỦY API KEY CŨ** và tạo mới tại:
https://makersuite.google.com/app/apikey

## 📝 Ghi chú

- File `config/chatbot-config.php` vẫn còn trên máy local của bạn
- Chỉ không push lên GitHub thôi
- Khi clone về máy khác, copy từ `.example.php` và điền API key mới
