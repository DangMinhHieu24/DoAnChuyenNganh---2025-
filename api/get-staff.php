<?php
/**
 * API: Get Staff by Service
 * Lấy danh sách nhân viên theo dịch vụ
 */
header('Content-Type: application/json');

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Staff.php';

$database = new Database();
$db = $database->getConnection();
$staffModel = new Staff($db);

$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

if ($service_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid service ID']);
    exit;
}

$staff = $staffModel->getStaffByService($service_id);

echo json_encode([
    'success' => true,
    'staff' => $staff
]);
?>
