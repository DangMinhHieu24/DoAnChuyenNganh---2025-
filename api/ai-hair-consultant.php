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
            // Lưu vào session để tracking
            $_SESSION['last_hair_analysis'] = [
                'timestamp' => time(),
                'result' => $result['analysis']
            ];
            
            jsonResponse([
                'success' => true,
                'analysis' => $result['analysis'],
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
Bạn là chuyên gia tư vấn kiểu tóc chuyên nghiệp của salon {SALON_NAME}.

NHIỆM VỤ:
Phân tích ảnh khuôn mặt của khách hàng và đưa ra gợi ý kiểu tóc phù hợp nhất.

PHÂN TÍCH:
1. Khuôn mặt: Xác định hình dạng (tròn, vuông, dài, trái xoan, tim...)
2. Đặc điểm: Trán, má, cằm, tỷ lệ khuôn mặt
3. Màu da: Tông da (trắng, ngăm, bánh mật...)
4. Phong cách hiện tại: Kiểu tóc đang có (nếu thấy)

GỢI Ý:
Đưa ra 3-4 kiểu tóc phù hợp nhất với format:

**PHÂN TÍCH KHUÔN MẶT:**
[Mô tả chi tiết khuôn mặt]

**GỢI Ý KIỂU TÓC:**

1. **[Tên kiểu tóc]** ⭐⭐⭐⭐⭐
   - Mô tả: [Chi tiết kiểu tóc]
   - Phù hợp vì: [Lý do cụ thể]
   - Dịch vụ cần: [Cắt/Nhuộm/Uốn...]
   - Độ khó: [Dễ/Trung bình/Khó]

2. **[Tên kiểu tóc]** ⭐⭐⭐⭐
   [Tương tự]

3. **[Tên kiểu tóc]** ⭐⭐⭐⭐
   [Tương tự]

**LƯU Ý CHĂM SÓC:**
[Gợi ý chăm sóc tóc]

**DỊCH VỤ CỦA CHÚNG TÔI:**
- {$servicesText}

Hãy trả lời bằng tiếng Việt, thân thiện, chuyên nghiệp và chi tiết.
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
        return [
            'success' => false,
            'message' => 'Không thể parse response'
        ];
    }
    
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return [
            'success' => true,
            'analysis' => $result['candidates'][0]['content']['parts'][0]['text']
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Không nhận được phân tích từ AI'
    ];
}

/**
 * Parse gợi ý kiểu tóc từ text
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
