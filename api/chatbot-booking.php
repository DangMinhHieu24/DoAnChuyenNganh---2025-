<?php
/**
 * Chatbot Booking API - Xử lý đặt lịch tự động qua chat
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../config/config.php';
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
    case 'start_booking':
        startBooking($db);
        break;
        
    case 'select_service':
        selectService($db, $input);
        break;
        
    case 'select_staff':
        selectStaff($db, $input);
        break;
        
    case 'select_date':
        selectDate($db, $input);
        break;
        
    case 'select_time':
        selectTime($db, $input);
        break;
        
    case 'confirm_booking':
        confirmBooking($db, $input);
        break;
        
    case 'get_booking_state':
        getBookingState();
        break;
        
    case 'cancel_booking':
        cancelBooking();
        break;
        
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Bắt đầu quy trình đặt lịch
 */
function startBooking($db) {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        jsonResponse([
            'success' => false,
            'require_login' => true,
            'message' => 'Bạn cần đăng nhập để đặt lịch. Vui lòng đăng nhập trước nhé! 😊',
            'login_url' => '/Website_DatLich/auth/login.php'
        ]);
        return;
    }
    
    // Khởi tạo session booking
    $_SESSION['chatbot_booking'] = [
        'step' => 'select_service',
        'service_id' => null,
        'staff_id' => null,
        'date' => null,
        'time' => null,
        'notes' => ''
    ];
    
    // Lấy danh sách dịch vụ
    $serviceModel = new Service($db);
    $services = $serviceModel->getAllServices();
    
    // Nhóm theo category
    $categories = [];
    foreach ($services as $service) {
        $catName = $service['category_name'] ?? 'Khác';
        if (!isset($categories[$catName])) {
            $categories[$catName] = [];
        }
        $categories[$catName][] = $service;
    }
    
    jsonResponse([
        'success' => true,
        'step' => 'select_service',
        'message' => 'Tuyệt vời! Hãy chọn dịch vụ bạn muốn sử dụng nhé 💇‍♀️',
        'categories' => $categories
    ]);
}

/**
 * Chọn dịch vụ
 */
function selectService($db, $input) {
    $serviceId = $input['service_id'] ?? null;
    
    if (!$serviceId) {
        jsonResponse(['success' => false, 'message' => 'Vui lòng chọn dịch vụ']);
        return;
    }
    
    // Lưu vào session
    $_SESSION['chatbot_booking']['service_id'] = $serviceId;
    $_SESSION['chatbot_booking']['step'] = 'select_staff';
    
    // Lấy thông tin dịch vụ
    $serviceModel = new Service($db);
    $service = $serviceModel->getServiceById($serviceId);
    
    // Lấy danh sách nhân viên cho dịch vụ này
    $staffModel = new Staff($db);
    $staffList = $staffModel->getStaffByService($serviceId);
    
    if (empty($staffList)) {
        $staffList = $staffModel->getAllStaff();
    }
    
    jsonResponse([
        'success' => true,
        'step' => 'select_staff',
        'message' => "Bạn đã chọn: {$service['service_name']} ✅\n\nBây giờ hãy chọn nhân viên bạn muốn phục vụ nhé 👨‍💼",
        'service' => $service,
        'staff_list' => $staffList
    ]);
}

/**
 * Chọn nhân viên
 */
function selectStaff($db, $input) {
    $staffId = $input['staff_id'] ?? null;
    
    if (!$staffId) {
        jsonResponse(['success' => false, 'message' => 'Vui lòng chọn nhân viên']);
        return;
    }
    
    $_SESSION['chatbot_booking']['staff_id'] = $staffId;
    $_SESSION['chatbot_booking']['step'] = 'select_date';
    
    $staffModel = new Staff($db);
    $staff = $staffModel->getStaffById($staffId);
    
    // Tạo danh sách ngày có thể đặt (7 ngày tới)
    $dates = [];
    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime("+$i days"));
        $dayName = getDayOfWeek($date);
        $dates[] = [
            'date' => $date,
            'display' => date('d/m/Y', strtotime($date)) . " ($dayName)"
        ];
    }
    
    jsonResponse([
        'success' => true,
        'step' => 'select_date',
        'message' => "Nhân viên {$staff['full_name']} sẽ phục vụ bạn ✅\n\nChọn ngày bạn muốn đến nhé 📅",
        'staff' => $staff,
        'dates' => $dates
    ]);
}

/**
 * Chọn ngày
 */
function selectDate($db, $input) {
    try {
        $date = $input['date'] ?? null;
        
        if (!$date) {
            jsonResponse(['success' => false, 'message' => 'Vui lòng chọn ngày']);
            return;
        }
        
        if (!isset($_SESSION['chatbot_booking'])) {
            jsonResponse(['success' => false, 'message' => 'Phiên đặt lịch đã hết hạn. Vui lòng bắt đầu lại!']);
            return;
        }
        
        $_SESSION['chatbot_booking']['date'] = $date;
        $_SESSION['chatbot_booking']['step'] = 'select_time';
        
        // Lấy thông tin đã chọn
        $serviceId = $_SESSION['chatbot_booking']['service_id'];
        $staffId = $_SESSION['chatbot_booking']['staff_id'];
        
        if (!$serviceId || !$staffId) {
            jsonResponse(['success' => false, 'message' => 'Thiếu thông tin. Vui lòng bắt đầu lại!']);
            return;
        }
        
        $serviceModel = new Service($db);
        $service = $serviceModel->getServiceById($serviceId);
        
        if (!$service) {
            jsonResponse(['success' => false, 'message' => 'Không tìm thấy dịch vụ!']);
            return;
        }
        
        // Lấy slot thời gian trống
        $staffModel = new Staff($db);
        $availableSlots = $staffModel->getAvailableSlots($staffId, $date, $service['duration']);
        
        if (empty($availableSlots)) {
            jsonResponse([
                'success' => false,
                'message' => "Rất tiếc, ngày này đã hết lịch trống 😔\n\nVui lòng chọn ngày khác!",
                'step' => 'select_date'
            ]);
            return;
        }
        
        jsonResponse([
            'success' => true,
            'step' => 'select_time',
            'message' => "Ngày " . date('d/m/Y', strtotime($date)) . " ✅\n\nChọn giờ bạn muốn đến nhé ⏰",
            'available_slots' => $availableSlots
        ]);
        
    } catch (Exception $e) {
        error_log("Chatbot booking error: " . $e->getMessage());
        jsonResponse([
            'success' => false,
            'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
        ]);
    }
}

/**
 * Chọn giờ
 */
function selectTime($db, $input) {
    $time = $input['time'] ?? null;
    
    if (!$time) {
        jsonResponse(['success' => false, 'message' => 'Vui lòng chọn giờ']);
        return;
    }
    
    $_SESSION['chatbot_booking']['time'] = $time;
    $_SESSION['chatbot_booking']['step'] = 'confirm';
    
    // Lấy tất cả thông tin đã chọn
    $serviceId = $_SESSION['chatbot_booking']['service_id'];
    $staffId = $_SESSION['chatbot_booking']['staff_id'];
    $date = $_SESSION['chatbot_booking']['date'];
    
    $serviceModel = new Service($db);
    $service = $serviceModel->getServiceById($serviceId);
    
    $staffModel = new Staff($db);
    $staff = $staffModel->getStaffById($staffId);
    
    $summary = [
        'service' => $service['service_name'],
        'staff' => $staff['full_name'],
        'date' => date('d/m/Y', strtotime($date)) . ' (' . getDayOfWeek($date) . ')',
        'time' => $time,
        'duration' => $service['duration'] . ' phút',
        'price' => formatCurrency($service['price'])
    ];
    
    jsonResponse([
        'success' => true,
        'step' => 'confirm',
        'message' => "Giờ $time ✅\n\nVui lòng xác nhận thông tin đặt lịch:",
        'summary' => $summary
    ]);
}

/**
 * Xác nhận và tạo booking
 */
function confirmBooking($db, $input) {
    if (!isset($_SESSION['chatbot_booking'])) {
        jsonResponse(['success' => false, 'message' => 'Phiên đặt lịch đã hết hạn']);
        return;
    }
    
    $booking = $_SESSION['chatbot_booking'];
    $notes = $input['notes'] ?? '';
    
    // Lấy thông tin dịch vụ
    $serviceModel = new Service($db);
    $service = $serviceModel->getServiceById($booking['service_id']);
    
    // Kiểm tra lại availability
    $bookingModel = new Booking($db);
    $isAvailable = $bookingModel->checkAvailability(
        $booking['staff_id'],
        $booking['date'],
        $booking['time'],
        $service['duration']
    );
    
    if (!$isAvailable) {
        jsonResponse([
            'success' => false,
            'message' => 'Rất tiếc, khung giờ này vừa được đặt. Vui lòng chọn giờ khác! 😔'
        ]);
        return;
    }
    
    // Tạo booking
    $bookingModel->customer_id = $_SESSION['user_id'];
    $bookingModel->service_id = $booking['service_id'];
    $bookingModel->staff_id = $booking['staff_id'];
    $bookingModel->booking_date = $booking['date'];
    $bookingModel->booking_time = $booking['time'];
    $bookingModel->duration = $service['duration'];
    $bookingModel->total_price = $service['price'];
    $bookingModel->status = 'pending';
    $bookingModel->payment_status = 'unpaid';
    $bookingModel->payment_method = 'cash';
    $bookingModel->notes = $notes;
    
    if ($bookingModel->create()) {
        // Xóa session booking
        unset($_SESSION['chatbot_booking']);
        
        $staffModel = new Staff($db);
        $staff = $staffModel->getStaffById($booking['staff_id']);
        
        jsonResponse([
            'success' => true,
            'message' => "🎉 Đặt lịch thành công!\n\n" .
                        "Mã đặt lịch: #" . $bookingModel->booking_id . "\n" .
                        "Dịch vụ: {$service['service_name']}\n" .
                        "Nhân viên: {$staff['full_name']}\n" .
                        "Thời gian: " . date('d/m/Y', strtotime($booking['date'])) . " lúc {$booking['time']}\n" .
                        "Giá: " . formatCurrency($service['price']) . "\n\n" .
                        "Chúng tôi sẽ liên hệ xác nhận sớm nhất. Cảm ơn bạn! 💖",
            'booking_id' => $bookingModel->booking_id
        ]);
    } else {
        jsonResponse([
            'success' => false,
            'message' => 'Có lỗi xảy ra. Vui lòng thử lại! 😔'
        ]);
    }
}

/**
 * Lấy trạng thái đặt lịch hiện tại
 */
function getBookingState() {
    if (!isset($_SESSION['chatbot_booking'])) {
        jsonResponse(['success' => false, 'has_booking' => false]);
        return;
    }
    
    jsonResponse([
        'success' => true,
        'has_booking' => true,
        'booking' => $_SESSION['chatbot_booking']
    ]);
}

/**
 * Hủy quy trình đặt lịch
 */
function cancelBooking() {
    unset($_SESSION['chatbot_booking']);
    jsonResponse([
        'success' => true,
        'message' => 'Đã hủy đặt lịch. Bạn có thể bắt đầu lại bất cứ lúc nào! 😊'
    ]);
}
