<?php
/**
 * AI Hair Consultant API
 * Tư vấn kiểu tóc qua ảnh sử dụng Gemini Vision AI
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/chatbot-config.php';
require_once '../config/functions.php';
require_once '../models/Service.php';

$database = new Database();
$db = $database->getConnection();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'analyze_face':
        analyzeFace($db);
        break;
        
    case 'get_hairstyle_services':
        getHairstyleServices($db, $_POST);
        break;
        
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Phân tích khuôn mặt và gợi ý kiểu tóc
 */
function analyzeFace($db) {
    try {
        // Kiểm tra file upload
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(['success' => false, 'message' => 'Vui lòng upload ảnh']);
            return;
        }
        
        $file = $_FILES['image'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            jsonResponse(['success' => false, 'message' => 'Chỉ chấp nhận file JPG, PNG, WEBP']);
            return;
        }
        
        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            jsonResponse(['success' => false, 'message' => 'Ảnh quá lớn. Tối đa 5MB']);
            return;
        }
        
        // Đọc file và encode base64
        $imageData = file_get_contents($file['tmp_name']);
        $base64Image = base64_encode($imageData);
        
        // Lấy mime type
        $mimeType = $file['type'];
        
        // Tạo prompt cho Gemini
        $prompt = buildHairConsultantPrompt($db);
        
        // Gọi Gemini Vision API
        $result = callGeminiVisionAPI($prompt, $base64Image, $mimeType);
        
        if ($result['success']) {
            // Thêm phần gợi ý đặt lịch
            $enhancedAnalysis = enhanceAnalysisWithBooking($result['analysis'], $db);
            
            // Lưu vào session để tracking
            $_SESSION['last_hair_analysis'] = [
                'timestamp' => time(),
                'result' => $enhancedAnalysis
            ];
            
            jsonResponse([
                'success' => true,
                'analysis' => $enhancedAnalysis,
                'suggestions' => parseHairstyleSuggestions($result['analysis']),
                'message' => 'Phân tích thành công! 🎨'
            ]);
        } else {
            jsonResponse([
                'success' => false,
                'message' => $result['message']
            ]);
        }
        
    } catch (Exception $e) {
        error_log("AI Hair Consultant Error: " . $e->getMessage());
        jsonResponse([
            'success' => false,
            'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
        ]);
    }
}

/**
 * Tạo prompt cho AI
 */
function buildHairConsultantPrompt($db) {
    // Lấy danh sách dịch vụ liên quan đến tóc
    $serviceModel = new Service($db);
    $services = $serviceModel->getAllServices();
    
    $hairServices = [];
    foreach ($services as $service) {
        if (stripos($service['service_name'], 'tóc') !== false || 
            stripos($service['service_name'], 'cắt') !== false ||
            stripos($service['service_name'], 'nhuộm') !== false ||
            stripos($service['service_name'], 'uốn') !== false) {
            $hairServices[] = $service['service_name'] . ' (' . formatCurrency($service['price']) . ')';
        }
    }
    
    $servicesText = implode("\n- ", $hairServices);
    
    $prompt = <<<PROMPT
Bạn là chuyên gia tư vấn kiểu tóc chuyên nghiệp của salon eBooking. 

NHIỆM VỤ:
Phân tích ảnh khuôn mặt của khách hàng và đưa ra gợi ý kiểu tóc CỤ THỂ kèm dịch vụ của salon.

BƯỚC 1 - PHÂN TÍCH KHUÔN MẶT:
- Hình dạng: Xác định chính xác (tròn, vuông, dài, trái xoan, tim, oval...)
- Đặc điểm nổi bật: Trán, má, cằm, tỷ lệ khuôn mặt
- Màu da: Tông da (trắng, ngăm, bánh mật...)
- Tóc hiện tại: Mô tả kiểu tóc đang có (nếu thấy)

BƯỚC 2 - GỢI Ý KIỂU TÓC CỤ THỂ:
Đưa ra 3-4 kiểu tóc phù hợp với format SAU:

**PHÂN TÍCH KHUÔN MẶT:**
[Mô tả chi tiết khuôn mặt của khách hàng]

**GỢI Ý KIỂU TÓC PHÙ HỢP:**

**1. [Tên kiểu tóc cụ thể - VD: Tóc Undercut Fade, Tóc Bob Ngắn Layer...]** ⭐⭐⭐⭐⭐
   - **Mô tả kiểu tóc:** [Chi tiết độ dài, lớp, kiểu cắt...]
   - **Phù hợp vì:** [Lý do cụ thể dựa trên khuôn mặt]
   - **Dịch vụ cần làm tại salon:**
     • Cắt tóc [mô tả cách cắt]
     • Nhuộm màu [gợi ý màu nếu cần]
     • Uốn/Duỗi [nếu cần]
   - **Thời gian:** Khoảng [X] phút
   - **Độ khó:** [Dễ/Trung bình/Khó]

**2. [Tên kiểu tóc khác]** ⭐⭐⭐⭐
   [Format tương tự]

**3. [Tên kiểu tóc khác]** ⭐⭐⭐⭐
   [Format tương tự]

**DỊCH VỤ TẠI SALON CHÚNG TÔI:**
{$servicesText}

**LƯU Ý CHĂM SÓC:**
- [Gợi ý sản phẩm và cách chăm sóc]
- [Tần suất cắt tỉa]

**KẾT LUẬN:**
Khuyến khích khách hàng đặt lịch để được tư vấn trực tiếp và trải nghiệm dịch vụ chuyên nghiệp.

QUAN TRỌNG:
- Phải gợi ý TÊN KIỂU TÓC CỤ THỂ (không chung chung)
- Phải nói rõ DỊCH VỤ NÀO cần làm tại salon
- Phải khuyến khích ĐẶT LỊCH
- Trả lời bằng tiếng Việt, thân thiện, chuyên nghiệp

Hãy phân tích và tư vấn chi tiết!
PROMPT;

    return $prompt;
}

/**
 * Gọi Gemini Vision API
 */
function callGeminiVisionAPI($prompt, $base64Image, $mimeType) {
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt],
                    [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $base64Image
                        ]
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 2048,
        ]
    ];
    
    $ch = curl_init(GEMINI_API_URL . '?key=' . GEMINI_API_KEY);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        error_log("Gemini Vision API cURL Error: $curlError");
        return [
            'success' => false,
            'message' => 'Lỗi kết nối API'
        ];
    }
    
    if ($httpCode !== 200) {
        error_log("Gemini Vision API Error: HTTP $httpCode - $response");
        return [
            'success' => false,
            'message' => 'API trả về lỗi: ' . $httpCode
        ];
    }
    
    $result = json_decode($response, true);
    
    if (!$result) {
        error_log("Gemini Vision API: Cannot parse JSON - " . $response);
        return [
            'success' => false,
            'message' => 'Không thể parse response'
        ];
    }
    
    // Log response structure for debugging
    error_log("Gemini Vision API Response: " . json_encode($result));
    
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return [
            'success' => true,
            'analysis' => $result['candidates'][0]['content']['parts'][0]['text']
        ];
    }
    
    // Check for error in response
    if (isset($result['error'])) {
        error_log("Gemini Vision API Error: " . json_encode($result['error']));
        return [
            'success' => false,
            'message' => 'Lỗi API: ' . ($result['error']['message'] ?? 'Unknown error')
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Không nhận được phân tích từ AI. Vui lòng thử lại.'
    ];
}

/**
 * Parse gợi ý kiểu tóc từ text và thêm dịch vụ
 */
function parseHairstyleSuggestions($analysisText) {
    $suggestions = [];
    
    // Tìm các kiểu tóc được gợi ý (số 1., 2., 3.)
    preg_match_all('/\d+\.\s*\*\*(.+?)\*\*/', $analysisText, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $hairstyle) {
            $suggestions[] = [
                'name' => trim($hairstyle),
                'icon' => '💇‍♀️'
            ];
        }
    }
    
    return $suggestions;
}

/**
 * Thêm phần gợi ý đặt lịch vào cuối analysis
 */
function enhanceAnalysisWithBooking($analysisText, $db) {
    $serviceModel = new Service($db);
    $services = $serviceModel->getAllServices();
    
    $hairServices = [];
    foreach ($services as $service) {
        if (stripos($service['service_name'], 'tóc') !== false || 
            stripos($service['service_name'], 'cắt') !== false ||
            stripos($service['service_name'], 'nhuộm') !== false ||
            stripos($service['service_name'], 'uốn') !== false) {
            $hairServices[] = $service;
        }
    }
    
    $bookingSection = "\n\n---\n\n";
    $bookingSection .= "**🎯 ĐẶT LỊCH NGAY ĐỂ TRẢI NGHIỆM:**\n\n";
    $bookingSection .= "Các dịch vụ phù hợp với bạn:\n\n";
    
    foreach (array_slice($hairServices, 0, 5) as $service) {
        $bookingSection .= "✨ **{$service['service_name']}**\n";
        $bookingSection .= "   - Giá: " . number_format($service['price']) . "đ\n";
        $bookingSection .= "   - Thời gian: {$service['duration']} phút\n\n";
    }
    
    $bookingSection .= "📞 **Liên hệ:** " . SALON_PHONE . "\n";
    $bookingSection .= "📍 **Địa chỉ:** " . SALON_ADDRESS . "\n\n";
    $bookingSection .= "👉 **Click nút 'Đặt Lịch Ngay' bên dưới để được phục vụ!**";
    
    return $analysisText . $bookingSection;
}

/**
 * Lấy dịch vụ liên quan đến kiểu tóc
 */
function getHairstyleServices($db, $input) {
    $hairstyleName = $input['hairstyle'] ?? '';
    
    $serviceModel = new Service($db);
    $allServices = $serviceModel->getAllServices();
    
    $relatedServices = [];
    foreach ($allServices as $service) {
        if (stripos($service['service_name'], 'tóc') !== false || 
            stripos($service['service_name'], 'cắt') !== false ||
            stripos($service['service_name'], 'nhuộm') !== false ||
            stripos($service['service_name'], 'uốn') !== false) {
            $relatedServices[] = [
                'id' => $service['service_id'],
                'name' => $service['service_name'],
                'price' => $service['price'],
                'duration' => $service['duration']
            ];
        }
    }
    
    jsonResponse([
        'success' => true,
        'services' => $relatedServices
    ]);
}
