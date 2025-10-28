<?php
/**
 * API: Get Available Time Slots
 * Lấy các khung giờ trống
 */
header('Content-Type: application/json');

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Staff.php';

$database = new Database();
$db = $database->getConnection();
$staffModel = new Staff($db);

$staff_id = isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : 0;
$date = isset($_GET['date']) ? $_GET['date'] : '';
$duration = isset($_GET['duration']) ? (int)$_GET['duration'] : 30;

if ($staff_id <= 0 || empty($date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit;
}

// Check if date is in the past
if (strtotime($date) < strtotime(date('Y-m-d'))) {
    echo json_encode(['success' => false, 'message' => 'Cannot book for past dates']);
    exit;
}

$slots = $staffModel->getAvailableSlots($staff_id, $date, $duration);

echo json_encode([
    'success' => true,
    'slots' => $slots
]);
?>
