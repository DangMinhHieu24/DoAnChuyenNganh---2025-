<?php
/**
 * API: Update Booking Status (Staff only)
 * Cập nhật trạng thái booking
 */
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../models/Booking.php';
require_once '../../models/Staff.php';

// Kiểm tra đăng nhập và role
if (!isLoggedIn() || getCurrentUserRole() !== 'staff') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$bookingModel = new Booking($db);
$staffModel = new Staff($db);

// Lấy data
$data = json_decode(file_get_contents('php://input'), true);
$booking_id = isset($data['booking_id']) ? (int)$data['booking_id'] : 0;
$new_status = isset($data['status']) ? trim($data['status']) : '';

// Validation
if ($booking_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

$allowed_statuses = ['confirmed', 'completed', 'cancelled'];
if (!in_array($new_status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Lấy thông tin staff
$user_id = $_SESSION['user_id'];
$staff_info = $staffModel->getStaffByUserId($user_id);

if (!$staff_info) {
    echo json_encode(['success' => false, 'message' => 'Staff not found']);
    exit;
}

$staff_id = $staff_info['staff_id'];

// Lấy booking và kiểm tra quyền
$booking = $bookingModel->getBookingById($booking_id);

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit;
}

if ($booking['staff_id'] != $staff_id) {
    echo json_encode(['success' => false, 'message' => 'Access denied. This booking is not assigned to you']);
    exit;
}

// Kiểm tra logic status transition
$current_status = $booking['status'];
$valid_transitions = [
    'pending' => ['confirmed', 'cancelled'],
    'confirmed' => ['completed', 'cancelled'],
    'completed' => [],
    'cancelled' => []
];

if (!isset($valid_transitions[$current_status]) || !in_array($new_status, $valid_transitions[$current_status])) {
    echo json_encode([
        'success' => false, 
        'message' => "Cannot change status from {$current_status} to {$new_status}"
    ]);
    exit;
}

// Update status
$query = "UPDATE bookings 
          SET status = :status, 
              updated_at = NOW() 
          WHERE booking_id = :booking_id";

$stmt = $db->prepare($query);
$stmt->bindParam(':status', $new_status);
$stmt->bindParam(':booking_id', $booking_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Status updated successfully',
        'new_status' => $new_status
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
?>
