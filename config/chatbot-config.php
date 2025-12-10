<?php
/**
 * Chatbot Configuration
 * Cấu hình cho chatbot AI
 */

// ============================================
// GEMINI API CONFIGURATION
// ============================================

// API Key - Lấy từ: https://makersuite.google.com/app/apikey
define('GEMINI_API_KEY', 'AIzaSyCNfnPZL4NB0qGvyeMGix0lC81D4ax-mH8');

// ============================================
// MODEL CONFIGURATION - Dùng chung 1 model cho tất cả
// ============================================

// Model chung cho tất cả tính năng AI (Tối ưu quota và hiệu suất)
define('GEMINI_MODEL', 'gemini-2.5-flash');

// API Version (v1beta cho model mới nhất)
define('GEMINI_API_VERSION', 'v1beta');

// API Endpoint chung
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/' . GEMINI_API_VERSION . '/models/' . GEMINI_MODEL . ':generateContent');

// Alias cho từng tính năng (để dễ đọc code)
define('GEMINI_CHATBOT_MODEL', GEMINI_MODEL);
define('GEMINI_HAIR_MODEL', GEMINI_MODEL);
define('GEMINI_REPORT_MODEL', GEMINI_MODEL);

define('GEMINI_CHATBOT_API_URL', GEMINI_API_URL);
define('GEMINI_HAIR_API_URL', GEMINI_API_URL);
define('GEMINI_REPORT_API_URL', GEMINI_API_URL);

// ============================================
// CHATBOT SETTINGS
// ============================================

// Tên chatbot
define('CHATBOT_NAME', 'Trợ lý AI Salon');

// Avatar emoji
define('CHATBOT_AVATAR', '🤖');

// Thông điệp chào mừng
define('CHATBOT_WELCOME_MESSAGE', 'Xin chào! Tôi là trợ lý AI của salon. Tôi có thể giúp bạn tìm hiểu về dịch vụ, giá cả, nhân viên và đặt lịch hẹn. Bạn cần tôi hỗ trợ gì?');

// ============================================
// SALON INFORMATION
// ============================================

// Thông tin liên hệ
define('SALON_NAME', 'eBooking Salon');
define('SALON_ADDRESS', '162 ABC, Phường 5, TP Trà Vinh');
define('SALON_PHONE', '0976985305');
define('SALON_EMAIL', 'dminhhieu2408@gmail.com');

// Giờ làm việc
define('WORKING_HOURS_WEEKDAY', '9:00 - 20:00');
define('WORKING_HOURS_WEEKEND', '8:00 - 21:00');

// ============================================
// AI PARAMETERS
// ============================================

// Temperature (0.0 - 1.0)
// Thấp = chính xác hơn, Cao = sáng tạo hơn
define('AI_TEMPERATURE', 0.7);

// Max output tokens
define('AI_MAX_TOKENS', 1024);

// Top K
define('AI_TOP_K', 40);

// Top P
define('AI_TOP_P', 0.95);

// ============================================
// CHATBOT BEHAVIOR
// ============================================

// Có cho phép trả lời câu hỏi ngoài luồng không?
define('ALLOW_OFF_TOPIC', true);

// Có tự động gợi ý quick replies không?
define('ENABLE_QUICK_REPLIES', true);

// Số lượng tin nhắn tối đa lưu trong session
define('MAX_CHAT_HISTORY', 50);

// Timeout cho API call (giây)
define('API_TIMEOUT', 30);

// ============================================
// RATE LIMITING
// ============================================

// Số tin nhắn tối đa mỗi phút
define('MAX_MESSAGES_PER_MINUTE', 10);

// Số tin nhắn tối đa mỗi giờ
define('MAX_MESSAGES_PER_HOUR', 50);

// ============================================
// FEATURES
// ============================================

// Bật/tắt tính năng đặt lịch qua chatbot
define('ENABLE_BOOKING_VIA_CHAT', true);

// Bật/tắt tính năng kiểm tra lịch trống
define('ENABLE_AVAILABILITY_CHECK', true);

// Bật/tắt tính năng tìm kiếm dịch vụ
define('ENABLE_SERVICE_SEARCH', true);

// Bật/tắt tính năng xem thông tin nhân viên
define('ENABLE_STAFF_INFO', true);

// ============================================
// UI CUSTOMIZATION
// ============================================

// Màu chính (gradient start)
define('CHATBOT_COLOR_PRIMARY', '#667eea');

// Màu phụ (gradient end)
define('CHATBOT_COLOR_SECONDARY', '#764ba2');

// Vị trí chatbot (bottom-right, bottom-left)
define('CHATBOT_POSITION', 'bottom-right');

// Kích thước chatbot
define('CHATBOT_WIDTH', '380px');
define('CHATBOT_HEIGHT', '550px');

// ============================================
// LOGGING & ANALYTICS
// ============================================

// Có lưu lịch sử chat không?
define('ENABLE_CHAT_LOGGING', true);

// Có theo dõi analytics không?
define('ENABLE_ANALYTICS', false);

// ============================================
// ERROR MESSAGES
// ============================================

define('ERROR_API_FAILED', 'Xin lỗi, tôi đang gặp sự cố kỹ thuật. Vui lòng thử lại sau. 😔');
define('ERROR_RATE_LIMIT', 'Bạn đang gửi tin nhắn quá nhanh. Vui lòng đợi một chút. ⏳');
define('ERROR_EMPTY_MESSAGE', 'Vui lòng nhập tin nhắn của bạn. 📝');
define('ERROR_NETWORK', 'Không thể kết nối đến server. Vui lòng kiểm tra kết nối internet. 🌐');

// ============================================
// SYSTEM PROMPTS
// ============================================

// System prompt cơ bản
define('SYSTEM_PROMPT_BASE', 
    "Bạn là trợ lý AI thông minh và thân thiện của " . SALON_NAME . ". " .
    "Nhiệm vụ của bạn là:\n" .
    "1. Trả lời câu hỏi của khách hàng một cách chuyên nghiệp, thân thiện\n" .
    "2. Cung cấp thông tin chính xác về dịch vụ, giá cả, nhân viên\n" .
    "3. Hỗ trợ khách hàng đặt lịch hẹn\n" .
    "4. Sử dụng emoji phù hợp để thân thiện hơn\n" .
    "5. Trả lời ngắn gọn, súc tích nhưng đầy đủ thông tin\n\n"
);

// ============================================
// VALIDATION
// ============================================

// Kiểm tra API key đã được cấu hình chưa
if (GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY_HERE') {
    trigger_error(
        'Vui lòng cấu hình GEMINI_API_KEY trong file config/chatbot-config.php. ' .
        'Lấy API key tại: https://makersuite.google.com/app/apikey',
        E_USER_WARNING
    );
}
