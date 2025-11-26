<?php
/**
 * Chatbot Actions API
 * Xử lý các hành động cụ thể từ chatbot (đặt lịch, kiểm tra slot trống...)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Service.php';
require_once '../models/Staff.php';
require_once '../models/Booking.php';

$database = new Database();
$db = $database->getConnection();

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'get_services':
        getServices($db);
        break;
        
    case 'get_service_price':
        getServicePrice($db, $input);
        break;
        
    case 'get_staff':
        getStaff($db, $input);
        break;
        
    case 'check_availability':
        checkAvailability($db, $input);
        break;
        
    case 'create_booking':
        createBooking($db, $input);
        break;
        
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

/**
 * Lấy danh sách dịch vụ
 */
function getServices($db) {
    $serviceModel = new Service($db);
    $services = $serviceModel->getAllServices();
    
    $result = [];
    foreach ($services as $service) {
        $result[] = [
            'id' => $service['service_id'],
            'name' => $service['service_name'],
            'price' => $service['price'],
            'duration' => $service['duration'],
            'category' => $service['category_name'],
            'description' => $service['description']
        ];
    }
    
    jsonResponse(['success' => true, 'data' => $result]);
}

/**
 * Lấy giá dịch vụ cụ thể
 */
function getServicePrice($db, $input) {
    $serviceName = $input['service_name'] ?? '';
    
    if (empty($serviceName)) {
        jsonResponse(['success' => false, 'message' => 'Service name required'], 400);
    }
    
    $serviceModel = new Service($db);
    $services = $serviceModel->search($serviceName);
    
    if (empty($services)) {
        jsonResponse(['success' => false, 'message' => 'Service not found']);
    }
    
    $result = [];
    foreach ($services as $service) {
        $result[] = [
            'id' => $service['service_id'],
            'name' => $service['service_name'],
            'price' => number_format($service['price']) . 'đ',
            'duration' => $service['duration'] . ' phút',
            'description' => $service['description']
        ];
    }
    
    jsonResponse(['success' => true, 'data' => $result]);
}

/**
 * Lấy danh sách nhân viên
 */
function getStaff($db, $input) {
    $serviceId = $input['service_id'] ?? null;
    
    $staffModel = new Staff($db);
    
    if ($serviceId) {
        $staff = $staffModel->getStaffByService($serviceId);
    } else {
        $staff = $staffModel->getAllStaff();
    }
    
    $result = [];
    foreach ($staff as $s) {
        $result[] = [
            'id' => $s['staff_id'],
            'name' => $s['full_name'],
            'specialization' => $s['specialization'] ?? '',
            'experience' => $s['experience_years'] ?? 0,
            'rating' => $s['rating'] ?? 0,
            'avatar' => getAvatarUrl($s['avatar'] ?? null)
        ];
    }
    
    jsonResponse(['success' => true, 'data' => $result]);
}

/**
 * Kiểm tra lịch trống
 */
function checkAvailability($db, $input) {
    $staffId = $input['staff_id'] ?? null;
    $date = $input['date'] ?? date('Y-m-d');
    $duration = $input['duration'] ?? 60;
    
    if (!$staffId) {
        jsonResponse(['success' => false, 'message' => 'Staff ID required'], 400);
    }
    
    // Validate date
    if (strtotime($date) < strtotime(date('Y-m-d'))) {
        jsonResponse(['success' => false, 'message' => 'Không thể chọn ngày trong quá khứ']);
    }
    
    $staffModel = new Staff($db);
    $slots = $staffModel->getAvailableSlots($staffId, $date, $duration);
    
    if (empty($slots)) {
        jsonResponse([
            'success' => true, 
            'data' => [],
            'message' => 'Không có lịch trống trong ngày này'
        ]);
    }
    
    jsonResponse([
        'success' => true, 
        'data' => $slots,
        'message' => 'Có ' . count($slots) . ' khung giờ trống'
    ]);
}

/**
 * Tạo booking mới
 */
function createBooking($db, $input) {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        jsonResponse([
            'success' => false, 
            'message' => 'Vui lòng đăng nhập để đặt lịch',
            'require_login' => true
        ]);
    }
    
    $customerId = $_SESSION['user_id'];
    $serviceId = $input['service_id'] ?? null;
    $staffId = $input['staff_id'] ?? null;
    $date = $input['date'] ?? null;
    $time = $input['time'] ?? null;
    $notes = $input['notes'] ?? '';
    
    // Validate
    if (!$serviceId || !$staffId || !$date || !$time) {
        jsonResponse([
            'success' => false, 
            'message' => 'Thiếu thông tin đặt lịch'
        ], 400);
    }
    
    // Lấy thông tin dịch vụ
    $serviceModel = new Service($db);
    $service = $serviceModel->getServiceById($serviceId);
    
    if (!$service) {
        jsonResponse(['success' => false, 'message' => 'Dịch vụ không tồn tại']);
    }
    
    // Kiểm tra availability
    $bookingModel = new Booking($db);
    $isAvailable = $bookingModel->checkAvailability($staffId, $date, $time, $service['duration']);
    
    if (!$isAvailable) {
        jsonResponse([
            'success' => false, 
            'message' => 'Khung giờ này đã được đặt. Vui lòng chọn giờ khác.'
        ]);
    }
    
    // Tạo booking
    $bookingModel->customer_id = $customerId;
    $bookingModel->service_id = $serviceId;
    $bookingModel->staff_id = $staffId;
    $bookingModel->booking_date = $date;
    $bookingModel->booking_time = $time;
    $bookingModel->duration = $service['duration'];
    $bookingModel->total_price = $service['price'];
    $bookingModel->status = 'pending';
    $bookingModel->payment_status = 'unpaid';
    $bookingModel->payment_method = 'cash';
    $bookingModel->notes = $notes;
    
    if ($bookingModel->create()) {
        jsonResponse([
            'success' => true,
            'message' => 'Đặt lịch thành công! Mã đặt lịch: #' . $bookingModel->booking_id,
            'booking_id' => $bookingModel->booking_id,
            'data' => [
                'service' => $service['service_name'],
                'date' => date('d/m/Y', strtotime($date)),
                'time' => $time,
                'price' => number_format($service['price']) . 'đ'
            ]
        ]);
    } else {
        jsonResponse([
            'success' => false,
            'message' => 'Có lỗi xảy ra. Vui lòng thử lại.'
        ]);
    }
}
