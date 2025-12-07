<?php
/**
 * Logout - Đăng xuất
 */
require_once '../config/config.php';

// Xóa tất cả session
session_unset();
session_destroy();

// Xóa cookie remember me
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, '/');
}

// Redirect về trang chủ
redirect(BASE_URL);
?>
