<?php
/**
 * Helper Functions
 * Các hàm hỗ trợ chung
 */

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if user is staff
 */
function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'staff';
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Sanitize input
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return number_format($amount ?? 0, 0, ',', '.') . ' ₫';
}

/**
 * Format date
 */
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    return date($format, strtotime($datetime));
}

/**
 * Format time
 */
function formatTime($time) {
    return date('H:i', strtotime($time));
}

/**
 * Get day of week in Vietnamese
 */
function getDayOfWeek($date) {
    $days = [
        'Sunday' => 'Chủ nhật',
        'Monday' => 'Thứ hai',
        'Tuesday' => 'Thứ ba',
        'Wednesday' => 'Thứ tư',
        'Thursday' => 'Thứ năm',
        'Friday' => 'Thứ sáu',
        'Saturday' => 'Thứ bảy'
    ];
    return $days[date('l', strtotime($date))];
}

/**
 * Get booking status text
 */
function getBookingStatusText($status) {
    $statuses = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
        'no_show' => 'Không đến'
    ];
    return $statuses[$status] ?? $status;
}

/**
 * Get booking status badge class
 */
function getBookingStatusBadge($status) {
    $badges = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
        'no_show' => 'secondary'
    ];
    return $badges[$status] ?? 'secondary';
}

/**
 * Get payment status text
 */
function getPaymentStatusText($status) {
    $statuses = [
        'unpaid' => 'Chưa thanh toán',
        'paid' => 'Đã thanh toán',
        'refunded' => 'Đã hoàn tiền'
    ];
    return $statuses[$status] ?? $status;
}

/**
 * Upload file
 */
function uploadFile($file, $folder = 'images') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return false;
    }

    $newFilename = uniqid() . '.' . $ext;
    $uploadPath = UPLOAD_PATH . $folder . '/';
    
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $uploadPath . $newFilename)) {
        return $folder . '/' . $newFilename;
    }

    return false;
}

/**
 * Delete file
 */
function deleteFile($filepath) {
    $fullPath = UPLOAD_PATH . $filepath;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)))), 1, $length);
}

/**
 * Send JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone
 */
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10,11}$/', $phone);
}

/**
 * Calculate time slots
 */
function generateTimeSlots($startTime, $endTime, $duration) {
    $slots = [];
    $start = strtotime($startTime);
    $end = strtotime($endTime);
    
    while ($start < $end) {
        $slots[] = date('H:i', $start);
        $start = strtotime("+{$duration} minutes", $start);
    }
    
    return $slots;
}

/**
 * Check if date is in the past
 */
function isPastDate($date) {
    return strtotime($date) < strtotime(date('Y-m-d'));
}

/**
 * Check if datetime is in the past
 */
function isPastDateTime($date, $time) {
    $datetime = $date . ' ' . $time;
    return strtotime($datetime) < time();
}

/**
 * Get avatar URL
 */
function getAvatarUrl($avatar) {
    if ($avatar && file_exists(UPLOAD_PATH . $avatar)) {
        return UPLOAD_URL . $avatar;
    }
    return BASE_URL . '/assets/images/default-avatar.svg';
}

/**
 * Get service image URL
 */
function getServiceImageUrl($image) {
    if ($image && file_exists(UPLOAD_PATH . $image)) {
        return UPLOAD_URL . $image;
    }
    return BASE_URL . '/assets/images/default-service.svg';
}

/**
 * Flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Pagination helper
 */
function paginate($totalItems, $currentPage, $itemsPerPage = ITEMS_PER_PAGE) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'items_per_page' => $itemsPerPage,
        'offset' => $offset
    ];
}

/**
 * Get booking status icon
 */
function getBookingStatusIcon($status) {
    $icons = [
        'pending' => 'fa-clock',
        'confirmed' => 'fa-check-circle',
        'completed' => 'fa-check-double',
        'cancelled' => 'fa-times-circle',
        'no_show' => 'fa-user-slash'
    ];
    return isset($icons[$status]) ? $icons[$status] : 'fa-question-circle';
}
?>
