<?php
/**
 * Chatbot Configuration Example
 * Copy file này thành chatbot-config.php và điền thông tin của bạn
 */

// ============================================
// GEMINI API CONFIGURATION
// ============================================

// API Key - Lấy từ: https://makersuite.google.com/app/apikey
define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY_HERE');

// Model - Gemini 2.0 Flash (mới nhất!)
define('GEMINI_MODEL', 'gemini-2.0-flash');

// API Endpoint
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent');

// ============================================
// SALON INFORMATION - THAY ĐỔI THÔNG TIN CỦA BẠN
// ============================================

define('SALON_NAME', 'Salon Làm Đẹp ABC');
define('SALON_ADDRESS', '123 Đường ABC, Quận 1, TP.HCM');
define('SALON_PHONE', '0123 456 789');
define('SALON_EMAIL', 'contact@salon.com');

// Giờ làm việc
define('WORKING_HOURS_WEEKDAY', '9:00 - 20:00');
define('WORKING_HOURS_WEEKEND', '8:00 - 21:00');

// ============================================
// CHATBOT SETTINGS
// ============================================

define('CHATBOT_NAME', 'Trợ lý AI Salon');
define('CHATBOT_AVATAR', '🤖');
define('CHATBOT_WELCOME_MESSAGE', 'Xin chào! Tôi là trợ lý AI của salon. Tôi có thể giúp bạn tìm hiểu về dịch vụ, giá cả, nhân viên và đặt lịch hẹn. Bạn cần tôi hỗ trợ gì?');

// ============================================
// AI PARAMETERS - KHÔNG NÊN THAY ĐỔI TRỪ KHI BẠN HIỂU RÕ
// ============================================

define('AI_TEMPERATURE', 0.7);
define('AI_MAX_TOKENS', 1024);
define('AI_TOP_K', 40);
define('AI_TOP_P', 0.95);

// ============================================
// FEATURES
// ============================================

define('ENABLE_BOOKING_VIA_CHAT', true);
define('ENABLE_AVAILABILITY_CHECK', true);
define('ENABLE_SERVICE_SEARCH', true);
define('ENABLE_STAFF_INFO', true);
define('ALLOW_OFF_TOPIC', true);
define('ENABLE_QUICK_REPLIES', true);

// ============================================
// UI CUSTOMIZATION
// ============================================

define('CHATBOT_COLOR_PRIMARY', '#667eea');
define('CHATBOT_COLOR_SECONDARY', '#764ba2');
define('CHATBOT_POSITION', 'bottom-right');
define('CHATBOT_WIDTH', '380px');
define('CHATBOT_HEIGHT', '550px');

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
// RATE LIMITING
// ============================================

define('MAX_MESSAGES_PER_MINUTE', 10);
define('MAX_MESSAGES_PER_HOUR', 50);
define('API_TIMEOUT', 30);

// ============================================
// LOGGING & ANALYTICS
// ============================================

define('ENABLE_CHAT_LOGGING', true);
define('ENABLE_ANALYTICS', false);
define('MAX_CHAT_HISTORY', 50);

// ============================================
// VALIDATION
// ============================================

if (GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY_HERE') {
    trigger_error(
        'Vui lòng cấu hình GEMINI_API_KEY trong file config/chatbot-config.php. ' .
        'Lấy API key tại: https://makersuite.google.com/app/apikey',
        E_USER_WARNING
    );
}
