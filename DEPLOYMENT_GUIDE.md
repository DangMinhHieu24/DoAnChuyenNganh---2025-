# 🚀 Hướng dẫn triển khai - eBooking Salon

## 📋 Tổng quan

Hướng dẫn triển khai (deploy) dự án eBooking Salon lên môi trường production.

---

## 🎯 Danh sách kiểm tra trước khi triển khai

- [ ] Code đã test kỹ trên local
- [ ] Database schema đã hoàn thiện
- [ ] API keys đã chuẩn bị
- [ ] Backup dữ liệu quan trọng
- [ ] Domain đã mua và cấu hình DNS
- [ ] SSL certificate đã chuẩn bị
- [ ] Server đã setup xong
- [ ] Email SMTP đã cấu hình

---

## 🖥️ Yêu cầu máy chủ (Server)

### Yêu cầu tối thiểu
- **OS**: Ubuntu 20.04+ / CentOS 7+ / Windows Server 2016+
- **CPU**: 2 cores
- **RAM**: 2GB
- **Storage**: 20GB SSD
- **Bandwidth**: 100GB/tháng

### Yêu cầu khuyến nghị
- **OS**: Ubuntu 22.04 LTS
- **CPU**: 4 cores
- **RAM**: 4GB
- **Storage**: 50GB SSD
- **Bandwidth**: Unlimited

### Phần mềm cần thiết
- **Web Server**: Apache 2.4+ hoặc Nginx 1.18+
- **PHP**: 7.4+ (khuyến nghị 8.0+)
- **MySQL**: 5.7+ hoặc MariaDB 10.2+
- **SSL**: Let's Encrypt (free)

---

## 📦 Option 1: Deploy lên Shared Hosting

### Bước 1: Chuẩn bị files

```bash
# Nén dự án (loại trừ files không cần)
zip -r ebooking-salon.zip . \
  -x "*.git*" \
  -x "node_modules/*" \
  -x "*.md" \
  -x "check-*.html" \
  -x "test-*.php" \
  -x "debug-*.php"
```

### Bước 2: Upload lên hosting

**Qua FTP/SFTP:**
```
Host: ftp.yourdomain.com
Username: your_username
Password: your_password
Port: 21 (FTP) hoặc 22 (SFTP)
```

**Hoặc qua cPanel File Manager:**
1. Đăng nhập cPanel
2. Vào File Manager
3. Upload file zip
4. Extract

### Bước 3: Tạo database

**Qua cPanel:**
1. Vào MySQL Databases
2. Tạo database mới: `username_salon`
3. Tạo user mới: `username_salon_user`
4. Gán quyền ALL PRIVILEGES
5. Ghi lại thông tin:
   - Database name
   - Username
   - Password
   - Host (thường là localhost)

### Bước 4: Import database

**Qua phpMyAdmin:**
1. Chọn database vừa tạo
2. Click tab "Import"
3. Chọn file `database/salon_booking.sql`
4. Click "Go"

### Bước 5: Cấu hình

**File `config/database.php`:**
```php
private $host = "localhost";
private $db_name = "username_salon";
private $username = "username_salon_user";
private $password = "your_password";
```

**File `config/config.php`:**
```php
define('BASE_URL', 'https://yourdomain.com');
define('SITE_NAME', 'eBooking Salon');
define('SITE_EMAIL', 'contact@yourdomain.com');
```

**File `config/chatbot-config.php`:**
```php
define('GEMINI_API_KEY', 'your-production-api-key');
```

### Bước 6: Cấu hình .htaccess

```apache
RewriteEngine On
RewriteBase /

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Hide .php extension
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}\.php -f
RewriteRule ^(.*)$ $1.php [L]

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Disable directory listing
Options -Indexes

# Protect config files
<FilesMatch "^(config|database)\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### Bước 7: Phân quyền

```bash
# Qua SSH hoặc File Manager
chmod 755 uploads/
chmod 755 uploads/images/
chmod 755 uploads/services/
chmod 644 config/*.php
```

### Bước 8: Test

1. Truy cập: https://yourdomain.com
2. Test đăng nhập
3. Test đặt lịch
4. Test 3 tính năng AI
5. Kiểm tra email notifications

---

## 🐳 Option 2: Deploy với Docker

### Bước 1: Tạo Dockerfile

**File `Dockerfile`:**
```dockerfile
FROM php:8.0-apache

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache modules
RUN a2enmod rewrite headers

# Copy application
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html/uploads
RUN chmod -R 755 /var/www/html/uploads

EXPOSE 80
```

### Bước 2: Tạo docker-compose.yml

```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "80:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_NAME=salon_booking
      - DB_USER=root
      - DB_PASS=rootpassword

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: salon_booking
    volumes:
      - db_data:/var/lib/mysql
      - ./database/salon_booking.sql:/docker-entrypoint-initdb.d/init.sql
    ports:
      - "3306:3306"

  phpmyadmin:
    image: phpmyadmin:latest
    ports:
      - "8080:80"
    environment:
      - PMA_HOST=db
      - PMA_USER=root
      - PMA_PASSWORD=rootpassword

volumes:
  db_data:
```

### Bước 3: Deploy

```bash
# Build và start
docker-compose up -d

# Check logs
docker-compose logs -f

# Stop
docker-compose down
```

### Bước 4: Truy cập

- **Website**: http://localhost
- **phpMyAdmin**: http://localhost:8080

---

## ☁️ Option 3: Deploy lên VPS (Ubuntu)

### Bước 1: Kết nối VPS

```bash
ssh root@your-server-ip
```

### Bước 2: Update system

```bash
apt update && apt upgrade -y
```

### Bước 3: Install LAMP Stack

```bash
# Apache
apt install apache2 -y
systemctl start apache2
systemctl enable apache2

# MySQL
apt install mysql-server -y
mysql_secure_installation

# PHP
apt install php php-mysql php-curl php-gd php-mbstring php-xml php-zip -y

# Restart Apache
systemctl restart apache2
```

### Bước 4: Cấu hình MySQL

```bash
mysql -u root -p

CREATE DATABASE salon_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'salon_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON salon_booking.* TO 'salon_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Bước 5: Upload code

```bash
# Tạo thư mục
cd /var/www/html
mkdir ebooking-salon
cd ebooking-salon

# Upload qua Git
git clone <your-repo-url> .

# Hoặc upload qua SCP từ local
scp -r /path/to/project/* root@your-server-ip:/var/www/html/ebooking-salon/
```

### Bước 6: Import database

```bash
mysql -u salon_user -p salon_booking < database/salon_booking.sql
```

### Bước 7: Cấu hình Apache

**File `/etc/apache2/sites-available/ebooking-salon.conf`:**
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/html/ebooking-salon

    <Directory /var/www/html/ebooking-salon>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/ebooking-error.log
    CustomLog ${APACHE_LOG_DIR}/ebooking-access.log combined
</VirtualHost>
```

**Enable site:**
```bash
a2ensite ebooking-salon.conf
a2enmod rewrite
systemctl restart apache2
```

### Bước 8: Cài SSL (Let's Encrypt)

```bash
# Install Certbot
apt install certbot python3-certbot-apache -y

# Get certificate
certbot --apache -d yourdomain.com -d www.yourdomain.com

# Auto-renewal
certbot renew --dry-run
```

### Bước 9: Phân quyền

```bash
cd /var/www/html/ebooking-salon
chown -R www-data:www-data .
chmod -R 755 .
chmod -R 775 uploads/
```

### Bước 10: Cấu hình Firewall

```bash
# UFW
ufw allow 'Apache Full'
ufw allow OpenSSH
ufw enable
ufw status
```

---

## 🔒 Security Hardening

### 1. Ẩn thông tin PHP

**File `/etc/php/8.0/apache2/php.ini`:**
```ini
expose_php = Off
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

### 2. Disable dangerous functions

```ini
disable_functions = exec,passthru,shell_exec,system,proc_open,popen
```

### 3. Giới hạn upload

```ini
upload_max_filesize = 5M
post_max_size = 10M
max_execution_time = 30
```

### 4. Bảo vệ config files

```bash
chmod 600 config/*.php
```

### 5. Fail2Ban (chống brute force)

```bash
apt install fail2ban -y
systemctl start fail2ban
systemctl enable fail2ban
```

---

## 📊 Monitoring & Logging

### 1. Setup Error Logging

**File `config/config.php`:**
```php
// Production mode
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
```

### 2. Apache Logs

```bash
# Access log
tail -f /var/log/apache2/ebooking-access.log

# Error log
tail -f /var/log/apache2/ebooking-error.log
```

### 3. MySQL Slow Query Log

```sql
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
SET GLOBAL slow_query_log_file = '/var/log/mysql/slow-query.log';
```

---

## 🔄 Backup Strategy

### 1. Database Backup (Daily)

**Script `backup-db.sh`:**
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/database"
DB_NAME="salon_booking"
DB_USER="salon_user"
DB_PASS="your_password"

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/backup_$DATE.sql.gz

# Xóa backup cũ hơn 30 ngày
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete
```

**Cron job:**
```bash
crontab -e

# Backup daily at 2 AM
0 2 * * * /path/to/backup-db.sh
```

### 2. Files Backup (Weekly)

```bash
#!/bin/bash
DATE=$(date +%Y%m%d)
tar -czf /backups/files/ebooking_$DATE.tar.gz /var/www/html/ebooking-salon
```

---

## 🚀 Performance Optimization

### 1. Enable OPcache

**File `php.ini`:**
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### 2. Enable Gzip Compression

**File `.htaccess`:**
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### 3. Browser Caching

```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 4. MySQL Optimization

```sql
-- Add indexes
ALTER TABLE bookings ADD INDEX idx_date_status (booking_date, status);
ALTER TABLE services ADD INDEX idx_category_status (category_id, status);

-- Optimize tables
OPTIMIZE TABLE bookings, services, users;
```

---

## 📈 Scaling

### Horizontal Scaling

**Load Balancer (Nginx):**
```nginx
upstream backend {
    server 192.168.1.10:80;
    server 192.168.1.11:80;
    server 192.168.1.12:80;
}

server {
    listen 80;
    server_name yourdomain.com;

    location / {
        proxy_pass http://backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

### Database Replication

**Master-Slave Setup:**
```sql
-- Master
CHANGE MASTER TO
    MASTER_HOST='master-ip',
    MASTER_USER='repl_user',
    MASTER_PASSWORD='password',
    MASTER_LOG_FILE='mysql-bin.000001',
    MASTER_LOG_POS=107;

START SLAVE;
```

---

## 🐛 Troubleshooting

### Lỗi 500 Internal Server Error
```bash
# Check Apache error log
tail -f /var/log/apache2/error.log

# Check PHP error log
tail -f /var/log/php_errors.log

# Check permissions
ls -la /var/www/html/ebooking-salon
```

### Database connection failed
```bash
# Test MySQL connection
mysql -u salon_user -p salon_booking

# Check MySQL status
systemctl status mysql

# Check config
cat config/database.php
```

### SSL certificate issues
```bash
# Renew certificate
certbot renew

# Check certificate
certbot certificates
```

---

## ✅ Post-Deployment Checklist

- [ ] Website accessible via HTTPS
- [ ] All pages load correctly
- [ ] Login/Register works
- [ ] Booking system works
- [ ] AI features work (Chatbot, Hair Consultant, Report Analysis)
- [ ] Email notifications work
- [ ] File uploads work
- [ ] Database backup scheduled
- [ ] Monitoring setup
- [ ] SSL certificate auto-renewal
- [ ] Firewall configured
- [ ] Error logging enabled
- [ ] Performance optimized

---

## 📞 Support

Nếu gặp vấn đề khi deploy:
- Email: dminhhieu2408@gmail.com
- Phone: 0976985305

---

**Last Updated**: December 10, 2025
