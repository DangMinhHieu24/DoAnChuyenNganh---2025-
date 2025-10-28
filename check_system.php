<?php
/**
 * System Check - Kiểm tra hệ thống
 * Chạy file này để kiểm tra cài đặt
 */

$errors = [];
$warnings = [];
$success = [];

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    $success[] = "✓ PHP Version: " . PHP_VERSION;
} else {
    $errors[] = "✗ PHP Version quá thấp. Cần >= 7.4.0, hiện tại: " . PHP_VERSION;
}

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'mysqli', 'gd', 'mbstring'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        $success[] = "✓ Extension: $ext";
    } else {
        $errors[] = "✗ Extension thiếu: $ext";
    }
}

// Check config files
$config_files = [
    'config/config.php',
    'config/database.php',
    'config/functions.php'
];
foreach ($config_files as $file) {
    if (file_exists($file)) {
        $success[] = "✓ File: $file";
    } else {
        $errors[] = "✗ File thiếu: $file";
    }
}

// Check directories
$required_dirs = [
    'uploads',
    'uploads/images',
    'uploads/avatars',
    'uploads/services',
    'assets',
    'assets/css',
    'assets/js',
    'assets/images'
];
foreach ($required_dirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            $success[] = "✓ Thư mục: $dir (có quyền ghi)";
        } else {
            $warnings[] = "⚠ Thư mục: $dir (không có quyền ghi)";
        }
    } else {
        $warnings[] = "⚠ Thư mục thiếu: $dir";
    }
}

// Check database connection
try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    if ($db) {
        $success[] = "✓ Kết nối database thành công";
        
        // Check tables
        $tables = ['users', 'categories', 'services', 'staff', 'bookings'];
        foreach ($tables as $table) {
            $stmt = $db->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $success[] = "✓ Bảng: $table";
            } else {
                $errors[] = "✗ Bảng thiếu: $table";
            }
        }
    } else {
        $errors[] = "✗ Không thể kết nối database";
    }
} catch (Exception $e) {
    $errors[] = "✗ Lỗi database: " . $e->getMessage();
}

// Display results
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm tra hệ thống</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .section {
            margin: 20px 0;
        }
        .success {
            color: #10b981;
            padding: 5px 0;
        }
        .error {
            color: #ef4444;
            padding: 5px 0;
            font-weight: bold;
        }
        .warning {
            color: #f59e0b;
            padding: 5px 0;
        }
        .summary {
            background: #f0f9ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Kiểm tra hệ thống</h1>
        
        <div class="summary">
            <h3>Tóm tắt</h3>
            <p>✓ Thành công: <?php echo count($success); ?></p>
            <p>⚠ Cảnh báo: <?php echo count($warnings); ?></p>
            <p>✗ Lỗi: <?php echo count($errors); ?></p>
        </div>

        <?php if (!empty($success)): ?>
        <div class="section">
            <h3>✓ Thành công</h3>
            <?php foreach ($success as $msg): ?>
                <div class="success"><?php echo $msg; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($warnings)): ?>
        <div class="section">
            <h3>⚠ Cảnh báo</h3>
            <?php foreach ($warnings as $msg): ?>
                <div class="warning"><?php echo $msg; ?></div>
            <?php endforeach; ?>
            <p><small>Cảnh báo không ảnh hưởng nghiêm trọng nhưng nên khắc phục.</small></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="section">
            <h3>✗ Lỗi</h3>
            <?php foreach ($errors as $msg): ?>
                <div class="error"><?php echo $msg; ?></div>
            <?php endforeach; ?>
            <p><strong>Vui lòng khắc phục các lỗi trên trước khi sử dụng!</strong></p>
        </div>
        <?php endif; ?>

        <div class="section">
            <h3>Bước tiếp theo</h3>
            <?php if (empty($errors)): ?>
                <p>✅ Hệ thống đã sẵn sàng!</p>
                <a href="index.php" class="btn">Về trang chủ</a>
                <a href="auth/login.php" class="btn">Đăng nhập</a>
            <?php else: ?>
                <p>❌ Vui lòng khắc phục các lỗi trên.</p>
                <p>Xem hướng dẫn trong file <strong>INSTALL.md</strong></p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h3>Thông tin hệ thống</h3>
            <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
            <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
            <p><strong>Current Path:</strong> <?php echo __DIR__; ?></p>
        </div>
    </div>
</body>
</html>
