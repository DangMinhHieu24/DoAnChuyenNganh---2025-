<?php
/**
 * List tất cả model có sẵn từ Google Gemini API
 */

$apiKey = 'AIzaSyCNfnPZL4NB0qGvyeMGix0lC81D4ax-mH8';

echo "<h1>📋 Danh sách Model có sẵn</h1>";
echo "<p>Đang lấy danh sách từ Google Gemini API...</p>";
echo "<hr>";

// Test cả v1 và v1beta
$apiVersions = ['v1', 'v1beta'];

foreach ($apiVersions as $version) {
    echo "<h2>API Version: $version</h2>";
    
    $url = "https://generativelanguage.googleapis.com/{$version}/models?key={$apiKey}";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        
        if (isset($result['models'])) {
            echo "<p style='color: green;'>✅ Tìm thấy " . count($result['models']) . " models</p>";
            echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #667eea; color: white;'>";
            echo "<th>Model Name</th>";
            echo "<th>Display Name</th>";
            echo "<th>Supports</th>";
            echo "<th>Input Token Limit</th>";
            echo "</tr>";
            
            foreach ($result['models'] as $model) {
                $modelName = str_replace('models/', '', $model['name']);
                $displayName = $model['displayName'] ?? 'N/A';
                $methods = isset($model['supportedGenerationMethods']) ? implode(', ', $model['supportedGenerationMethods']) : 'N/A';
                $inputLimit = $model['inputTokenLimit'] ?? 'N/A';
                
                // Highlight models that support generateContent
                $highlight = (strpos($methods, 'generateContent') !== false) ? "style='background: #d4edda;'" : "";
                
                echo "<tr $highlight>";
                echo "<td><strong>$modelName</strong></td>";
                echo "<td>$displayName</td>";
                echo "<td>$methods</td>";
                echo "<td>$inputLimit</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ Không có model nào</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ HTTP $httpCode</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
    
    echo "<hr>";
}

echo "<h3>💡 Hướng dẫn:</h3>";
echo "<ul>";
echo "<li>Chọn model có <strong>generateContent</strong> trong cột Supports</li>";
echo "<li>Copy <strong>Model Name</strong> chính xác</li>";
echo "<li>Cập nhật vào <code>config/chatbot-config.php</code></li>";
echo "</ul>";

echo "<h3>🔧 Về lỗi Quota (429):</h3>";
echo "<ul>";
echo "<li><strong>gemini-2.0-flash-exp</strong> đã hết quota hôm nay</li>";
echo "<li>Đợi đến ngày mai quota sẽ reset</li>";
echo "<li>Hoặc dùng model khác có quota còn</li>";
echo "<li>Hoặc tạo API key mới tại: <a href='https://aistudio.google.com/app/apikey' target='_blank'>Google AI Studio</a></li>";
echo "</ul>";
?>
