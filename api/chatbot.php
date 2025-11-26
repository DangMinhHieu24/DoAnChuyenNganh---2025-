<?php
/**
 * Chatbot API - Xử lý chat với Gemini AI
 * Tích hợp với database để trả lời câu hỏi về dịch vụ, nhân viên, đặt lịch
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../config/chatbot-config.php';
require_once '../models/Service.php';
require_once '../models/Staff.php';
require_once '../models/Booking.php';

class ChatbotHandler {
    private $db;
    private $serviceModel;
    private $staffModel;
    private $bookingModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->serviceModel = new Service($this->db);
        $this->staffModel = new Staff($this->db);
        $this->bookingModel = new Booking($this->db);
    }
    
    /**
     * Phân tích intent từ câu hỏi người dùng
     */
    private function analyzeIntent($message) {
        $message = mb_strtolower($message, 'UTF-8');
        
        // Intent: Hỏi về giá dịch vụ
        if (preg_match('/(giá|bao nhiêu|chi phí|phí|tiền)/u', $message) && 
            preg_match('/(dịch vụ|cắt|nhuộm|uốn|gội|massage|spa)/u', $message)) {
            return ['type' => 'price_inquiry', 'message' => $message];
        }
        
        // Intent: Xem danh sách dịch vụ
        if (preg_match('/(có những|có các|danh sách|xem|dịch vụ nào)/u', $message) && 
            preg_match('/(dịch vụ)/u', $message)) {
            return ['type' => 'list_services', 'message' => $message];
        }
        
        // Intent: Hỏi về nhân viên
        if (preg_match('/(nhân viên|thợ|stylist|staff)/u', $message) && 
            preg_match('/(nào|ai|có|trống|rảnh|giỏi)/u', $message)) {
            return ['type' => 'staff_inquiry', 'message' => $message];
        }
        
        // Intent: Đặt lịch
        if (preg_match('/(đặt lịch|book|booking|hẹn|đặt hẹn)/u', $message)) {
            return ['type' => 'booking', 'message' => $message];
        }
        
        // Intent: Kiểm tra lịch trống
        if (preg_match('/(lịch trống|giờ trống|slot|khung giờ|thời gian)/u', $message) && 
            preg_match('/(nào|có|còn|rảnh)/u', $message)) {
            return ['type' => 'check_availability', 'message' => $message];
        }
        
        // Intent: Hỏi về giờ làm việc
        if (preg_match('/(giờ|mở cửa|đóng cửa|làm việc|hoạt động)/u', $message)) {
            return ['type' => 'working_hours', 'message' => $message];
        }
        
        // Intent: Hỏi về địa chỉ/liên hệ
        if (preg_match('/(địa chỉ|ở đâu|liên hệ|số điện thoại|email)/u', $message)) {
            return ['type' => 'contact_info', 'message' => $message];
        }
        
        return ['type' => 'general', 'message' => $message];
    }
    
    /**
     * Lấy thông tin từ database dựa trên intent
     */
    private function getContextData($intent) {
        $context = [];
        
        switch ($intent['type']) {
            case 'price_inquiry':
            case 'list_services':
                $services = $this->serviceModel->getAllServices();
                $context['services'] = $services;
                break;
                
            case 'staff_inquiry':
            case 'check_availability':
                $staff = $this->staffModel->getAllStaff();
                $context['staff'] = $staff;
                break;
                
            case 'working_hours':
                $context['working_hours'] = [
                    'weekday' => WORKING_HOURS_WEEKDAY,
                    'weekend' => WORKING_HOURS_WEEKEND
                ];
                break;
                
            case 'contact_info':
                $context['contact'] = [
                    'name' => SALON_NAME,
                    'address' => SALON_ADDRESS,
                    'phone' => SALON_PHONE,
                    'email' => SALON_EMAIL
                ];
                break;
        }
        
        return $context;
    }
    
    /**
     * Tạo system prompt cho Gemini
     */
    private function buildSystemPrompt($intent, $contextData) {
        $basePrompt = "Bạn là trợ lý AI thông minh của salon làm đẹp. Nhiệm vụ của bạn là:\n";
        $basePrompt .= "1. Trả lời câu hỏi của khách hàng một cách thân thiện, chuyên nghiệp\n";
        $basePrompt .= "2. Cung cấp thông tin chính xác về dịch vụ, giá cả, nhân viên\n";
        $basePrompt .= "3. Hỗ trợ khách hàng đặt lịch hẹn\n";
        $basePrompt .= "4. Có thể trả lời các câu hỏi ngoài luồng một cách tự nhiên\n\n";
        
        // Thêm context data
        if (!empty($contextData['services'])) {
            $basePrompt .= "DANH SÁCH DỊCH VỤ:\n";
            foreach ($contextData['services'] as $service) {
                $basePrompt .= "- {$service['service_name']}: " . number_format($service['price']) . "đ";
                $basePrompt .= " (Thời gian: {$service['duration']} phút)\n";
                if ($service['description']) {
                    $basePrompt .= "  Mô tả: {$service['description']}\n";
                }
            }
            $basePrompt .= "\n";
        }
        
        if (!empty($contextData['staff'])) {
            $basePrompt .= "DANH SÁCH NHÂN VIÊN:\n";
            foreach ($contextData['staff'] as $staff) {
                $basePrompt .= "- {$staff['full_name']}";
                if ($staff['specialization']) {
                    $basePrompt .= " - Chuyên môn: {$staff['specialization']}";
                }
                if ($staff['experience_years']) {
                    $basePrompt .= " ({$staff['experience_years']} năm kinh nghiệm)";
                }
                if ($staff['rating']) {
                    $basePrompt .= " - Đánh giá: {$staff['rating']}/5.0";
                }
                $basePrompt .= "\n";
            }
            $basePrompt .= "\n";
        }
        
        if (!empty($contextData['working_hours'])) {
            $basePrompt .= "GIỜ LÀM VIỆC:\n";
            $basePrompt .= "- Thứ 2 - Thứ 6: {$contextData['working_hours']['weekday']}\n";
            $basePrompt .= "- Thứ 7 - Chủ nhật: {$contextData['working_hours']['weekend']}\n\n";
        }
        
        if (!empty($contextData['contact'])) {
            $basePrompt .= "THÔNG TIN LIÊN HỆ:\n";
            $basePrompt .= "- Tên salon: " . SALON_NAME . "\n";
            $basePrompt .= "- Địa chỉ: " . SALON_ADDRESS . "\n";
            $basePrompt .= "- Điện thoại: " . SALON_PHONE . "\n";
            $basePrompt .= "- Email: " . SALON_EMAIL . "\n\n";
        }
        
        $basePrompt .= "Hãy trả lời câu hỏi của khách hàng dựa trên thông tin trên. ";
        $basePrompt .= "Trả lời ngắn gọn, súc tích nhưng đầy đủ thông tin. Sử dụng emoji phù hợp để thân thiện hơn.\n\n";
        
        $basePrompt .= "HƯỚNG DẪN ĐẶT LỊCH:\n";
        $basePrompt .= "Khi khách hàng muốn đặt lịch, hãy:\n";
        $basePrompt .= "1. Hỏi họ muốn dịch vụ gì (nếu chưa rõ)\n";
        $basePrompt .= "2. Gợi ý họ click vào nút 'Đặt lịch ngay' hoặc truy cập trang đặt lịch\n";
        $basePrompt .= "3. Hoặc hướng dẫn: 'Bạn có thể đặt lịch trực tiếp tại trang Đặt lịch trên website, ";
        $basePrompt .= "hoặc gọi điện " . SALON_PHONE . " để được hỗ trợ nhanh hơn.'\n";
        $basePrompt .= "4. Nếu họ hỏi về lịch trống, gợi ý họ chọn dịch vụ và nhân viên trên trang đặt lịch để xem giờ trống.";
        
        return $basePrompt;
    }
    
    /**
     * Gọi Gemini API
     */
    private function callGeminiAPI($systemPrompt, $userMessage) {
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nCâu hỏi của khách hàng: " . $userMessage]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => AI_TEMPERATURE,
                'topK' => AI_TOP_K,
                'topP' => AI_TOP_P,
                'maxOutputTokens' => AI_MAX_TOKENS,
            ]
        ];
        
        $ch = curl_init(GEMINI_API_URL . '?key=' . GEMINI_API_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            error_log("Gemini API cURL Error: $curlError");
            return null;
        }
        
        if ($httpCode !== 200) {
            error_log("Gemini API Error: HTTP $httpCode - Response: $response");
            return null;
        }
        
        $result = json_decode($response, true);
        
        if (!$result) {
            error_log("Gemini API Error: Invalid JSON response - $response");
            return null;
        }
        
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }
        
        error_log("Gemini API Error: Unexpected response structure - " . json_encode($result));
        return null;
    }
    
    /**
     * Xử lý tin nhắn
     */
    public function handleMessage($message) {
        // Phân tích intent
        $intent = $this->analyzeIntent($message);
        
        // Lấy context data từ database
        $contextData = $this->getContextData($intent);
        
        // Tạo system prompt
        $systemPrompt = $this->buildSystemPrompt($intent, $contextData);
        
        // Gọi Gemini API
        $response = $this->callGeminiAPI($systemPrompt, $message);
        
        if ($response === null) {
            // Log lỗi để debug
            error_log("Chatbot API Error: Gemini API returned null");
            return [
                'success' => false,
                'message' => ERROR_API_FAILED,
                'debug' => 'API returned null - check error_log for details'
            ];
        }
        
        return [
            'success' => true,
            'message' => $response,
            'intent' => $intent['type'],
            'context' => $contextData
        ];
    }
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['message']) || empty(trim($input['message']))) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng nhập tin nhắn'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $chatbot = new ChatbotHandler();
    $result = $chatbot->handleMessage($input['message']);
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ], JSON_UNESCAPED_UNICODE);
}
