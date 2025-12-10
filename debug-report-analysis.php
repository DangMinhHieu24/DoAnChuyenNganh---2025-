<?php
/**
 * Debug Report Analysis chi tiết
 */

session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'config/chatbot-config.php';
require_once 'models/Booking.php';

echo "<h1>🔍 Debug Report Analysis</h1>";
echo "<style>
    .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

// Check 1: Authentication
echo "<h2>1️⃣ Kiểm tra Authentication</h2>";
if (isLoggedIn()) {
    echo "<div class='success'>✅ Đã đăng nhập</div>";
    if (isAdmin()) {
        echo "<div class='success'>✅ Là Admin</div>";
    } else {
        echo "<div class='error'>❌ KHÔNG phải Admin - Đây là lý do lỗi!</div>";
        echo "<p>Bạn cần đăng nhập bằng tài khoản Admin để test Report Analysis</p>";
        exit;
    }
} else {
    echo "<div class='error'>❌ Chưa đăng nhập - Đây là lý do lỗi!</div>";
    echo "<p>Vui lòng <a href='auth/login.php'>đăng nhập Admin</a> trước</p>";
    exit;
}

// Check 2: Database connection
echo "<h2>2️⃣ Kiểm tra Database</h2>";
try {
    $database = new Database();
    $db = $database->getConnection();
    echo "<div class='success'>✅ Kết nối database thành công</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Lỗi database: " . $e->getMessage() . "</div>";
    exit;
}

// Check 3: Get booking data
echo "<h2>3️⃣ Kiểm tra dữ liệu Booking</h2>";
$bookingModel = new Booking($db);
$today = date('Y-m-d');
$this_month = date('Y-m');

$month_stats = $bookingModel->getStats($this_month.'-01', date('Y-m-t'));

echo "<div class='info'>";
echo "<strong>Dữ liệu tháng này:</strong><br>";
echo "• Tổng lịch hẹn: " . ($month_stats['total_bookings'] ?? 0) . "<br>";
echo "• Doanh thu: " . number_format($month_stats['total_revenue'] ?? 0) . " VNĐ<br>";
echo "• Hoàn thành: " . ($month_stats['completed'] ?? 0) . "<br>";
echo "• Đã hủy: " . ($month_stats['cancelled'] ?? 0) . "<br>";
echo "</div>";

if (($month_stats['total_bookings'] ?? 0) == 0) {
    echo "<div class='error'>⚠️ Không có dữ liệu booking. AI sẽ không có gì để phân tích!</div>";
}

// Check 4: Test API trực tiếp
echo "<h2>4️⃣ Test API Report Analysis</h2>";

$testPrompt = "Phân tích dữ liệu kinh doanh salon:\n";
$testPrompt .= "- Doanh thu tháng này: " . number_format($month_stats['total_revenue'] ?? 0) . " VNĐ\n";
$testPrompt .= "- Tổng lịch hẹn: " . ($month_stats['total_bookings'] ?? 0) . "\n";
$testPrompt .= "- Hoàn thành: " . ($month_stats['completed'] ?? 0) . "\n";
$testPrompt .= "- Đã hủy: " . ($month_stats['cancelled'] ?? 0) . "\n\n";
$testPrompt .= "Hãy đưa ra nhận xét ngắn gọn về tình hình kinh doanh.";

echo "<div class='info'>";
echo "<strong>Prompt gửi đến AI:</strong><br>";
echo "<pre>" . htmlspecialchars($testPrompt) . "</pre>";
echo "</div>";

$requestData = [
    'contents' => [
        [
            'parts' => [
                ['text' => $testPrompt]
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

echo "<div class='info'>";
echo "<strong>API URL:</strong> " . GEMINI_REPORT_API_URL . "<br>";
echo "<strong>Model:</strong> " . GEMINI_REPORT_MODEL . "<br>";
echo "<strong>API Key:</strong> " . substr(GEMINI_API_KEY, 0, 20) . "...<br>";
echo "</div>";

$ch = curl_init(GEMINI_REPORT_API_URL . '?key=' . GEMINI_API_KEY);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "<h3>📡 Response từ API:</h3>";

if ($curl_error) {
    echo "<div class='error'>";
    echo "❌ <strong>cURL Error:</strong> $curl_error";
    echo "</div>";
} else {
    echo "<div class='info'>";
    echo "<strong>HTTP Code:</strong> $http_code<br>";
    echo "</div>";
    
    if ($http_code === 200) {
        $result = json_decode($response, true);
        
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            echo "<div class='success'>";
            echo "✅ <strong>API hoạt động tốt!</strong><br><br>";
            echo "<strong>Phân tích từ AI:</strong><br>";
            echo "<pre>" . htmlspecialchars($result['candidates'][0]['content']['parts'][0]['text']) . "</pre>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "❌ <strong>Response không đúng format</strong><br>";
            echo "<strong>Full response:</strong><br>";
            echo "<pre>" . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</pre>";
            echo "</div>";
        }
    } else {
        echo "<div class='error'>";
        echo "❌ <strong>API Error</strong><br>";
        $error = json_decode($response, true);
        echo "<pre>" . htmlspecialchars(json_encode($error, JSON_PRETTY_PRINT)) . "</pre>";
        echo "</div>";
        
        // Gợi ý fix
        if ($http_code === 429) {
            echo "<div class='info'>";
            echo "<strong>🔧 Cách fix lỗi 429:</strong><br>";
            echo "1. Model <code>" . GEMINI_REPORT_MODEL . "</code> đã hết quota<br>";
            echo "2. Đổi sang <code>gemini-2.5-flash</code> trong <code>config/chatbot-config.php</code><br>";
            echo "3. Hoặc đợi đến 7h sáng mai quota sẽ reset<br>";
            echo "</div>";
        }
    }
}

// Check 5: Test API endpoint của dự án
echo "<hr>";
echo "<h2>5️⃣ Test API Endpoint của dự án</h2>";
echo "<p>Gọi trực tiếp <code>/api/ai-report-analysis.php</code></p>";

$apiUrl = BASE_URL . '/api/ai-report-analysis.php';
echo "<div class='info'><strong>URL:</strong> $apiUrl</div>";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id()); // Pass session

$apiResponse = curl_exec($ch);
$apiHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<div class='info'><strong>HTTP Code:</strong> $apiHttpCode</div>";

if ($apiHttpCode === 200) {
    $apiResult = json_decode($apiResponse, true);
    
    if ($apiResult['success'] ?? false) {
        echo "<div class='success'>";
        echo "✅ <strong>API endpoint hoạt động tốt!</strong><br><br>";
        echo "<strong>Phân tích:</strong><br>";
        echo "<pre>" . htmlspecialchars($apiResult['analysis']) . "</pre>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "❌ <strong>API endpoint trả về lỗi:</strong><br>";
        echo htmlspecialchars($apiResult['message'] ?? 'Unknown error');
        echo "</div>";
    }
} else {
    echo "<div class='error'>";
    echo "❌ <strong>Không thể gọi API endpoint</strong><br>";
    echo "<strong>Response:</strong><br>";
    echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<h2>📊 Kết luận</h2>";
echo "<ul>";
echo "<li>Nếu test 4 thành công nhưng test 5 lỗi → Vấn đề ở code <code>api/ai-report-analysis.php</code></li>";
echo "<li>Nếu cả 2 đều lỗi → Vấn đề ở API key hoặc quota</li>";
echo "<li>Nếu lỗi 401 ở test 5 → Vấn đề authentication (chưa đăng nhập admin)</li>";
echo "</ul>";
?>
