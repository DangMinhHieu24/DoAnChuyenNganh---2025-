<?php
/**
 * Application Configuration
 * Cấu hình ứng dụng
 */

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Constants
define('BASE_URL', 'http://localhost/Website_DatLich');
define('SITE_NAME', 'eBooking');
define('SITE_EMAIL', 'dminhhieu2408@gmail.com');
define('SITE_PHONE', '0976985305');
define('SITE_ADDRESS', '162 ABC, Phường 5, TP Trà Vinh');

// Upload paths
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Booking settings
define('BOOKING_ADVANCE_DAYS', 30);
define('BOOKING_CANCEL_HOURS', 24);
define('WORKING_START_TIME', '08:00');
define('WORKING_END_TIME', '20:00');
define('SLOT_DURATION', 30); // minutes

// Error reporting (tắt khi production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper functions
require_once __DIR__ . '/functions.php';
?>
