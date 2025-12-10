<?php
/**
 * AI Report Analysis API
 * API phân tích báo cáo thông minh bằng AI
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/chatbot-config.php';
require_once '../models/Booking.php';

// Kiểm tra đăng nhập và quyền admin
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$bookingModel = new Booking($db);

// Lấy dữ liệu thống kê
$today = date('Y-m-d');
$this_month = date('Y-m');
$last_month = date('Y-m', strtotime('-1 month'));
$this_year = date('Y');

$today_stats = $bookingModel->getStats($today, $today);
$month_stats = $bookingModel->getStats($this_month.'-01', date('Y-m-t'));
$last_month_stats = $bookingModel->getStats($last_month.'-01', date('Y-m-t', strtotime('last day of last month')));
$year_stats = $bookingModel->getStats($this_year.'-01-01', $this_year.'-12-31');

// Kiểm tra nếu không có dữ liệu
if (($month_stats['total_bookings'] ?? 0) == 0) {
    echo json_encode([
        'success' => true,
        'analysis' => "📊 **PHÂN TÍCH TÌNH HÌNH KINH DOANH**\n\n" .
                     "⚠️ **Chưa có dữ liệu booking trong tháng này.**\n\n" .
                     "**GỢI Ý HÀNH ĐỘNG:**\n\n" .
                     "1. 📢 **Marketing & Quảng bá:**\n" .
                     "   - Chạy quảng cáo Facebook/Google Ads\n" .
                     "   - Tạo khuyến mãi khai trương/giảm giá\n" .
                     "   - Chia sẻ trên mạng xã hội\n\n" .
                     "2. 🎯 **Thu hút khách hàng đầu tiên:**\n" .
                     "   - Giảm 50% cho 10 khách đầu tiên\n" .
                     "   - Tặng voucher cho khách giới thiệu\n" .
                     "   - Tổ chức sự kiện khai trương\n\n" .
                     "3. 💻 **Tối ưu hệ thống:**\n" .
                     "   - Kiểm tra website hoạt động tốt\n" .
                     "   - Test tính năng đặt lịch\n" .
                     "   - Chuẩn bị dữ liệu mẫu để demo\n\n" .
                     "4. 📞 **Liên hệ trực tiếp:**\n" .
                     "   - Gọi điện cho khách hàng cũ\n" .
                     "   - Gửi SMS/Email thông báo khai trương\n" .
                     "   - Phát tờ rơi khu vực lân cận\n\n" .
                     "**💡 LƯU Ý:** Đây là giai đoạn khởi đầu, hãy tập trung vào việc thu hút khách hàng đầu tiên. " .
                     "Sau khi có dữ liệu, AI sẽ phân tích chi tiết hơn để giúp bạn tối ưu kinh doanh!",
        'data' => [
            'has_data' => false,
            'message' => 'Chưa có dữ liệu booking'
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Lấy top dịch vụ
$query = "SELECT s.service_name, COUNT(*) as booking_count, SUM(b.total_price) as revenue
          FROM bookings b
          JOIN services s ON b.service_id = s.service_id
          WHERE b.status = 'completed'
          AND MONTH(b.booking_date) = MONTH(CURRENT_DATE())
          AND YEAR(b.booking_date) = YEAR(CURRENT_DATE())
          GROUP BY b.service_id
          ORDER BY booking_count DESC
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$top_services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy top nhân viên
$query = "SELECT u.full_name, COUNT(*) as booking_count, SUM(b.total_price) as revenue
          FROM bookings b
          JOIN staff st ON b.staff_id = st.staff_id
          JOIN users u ON st.user_id = u.user_id
          WHERE b.status = 'completed'
          AND MONTH(b.booking_date) = MONTH(CURRENT_DATE())
          AND YEAR(b.booking_date) = YEAR(CURRENT_DATE())
          GROUP BY b.staff_id
          ORDER BY revenue DESC
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$top_staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tỷ lệ tăng trưởng
$revenue_growth = 0;
if ($last_month_stats['total_revenue'] > 0) {
    $revenue_growth = (($month_stats['total_revenue'] - $last_month_stats['total_revenue']) / $last_month_stats['total_revenue']) * 100;
}

$booking_growth = 0;
if ($last_month_stats['total_bookings'] > 0) {
    $booking_growth = (($month_stats['total_bookings'] - $last_month_stats['total_bookings']) / $last_month_stats['total_bookings']) * 100;
}

// Tính tỷ lệ hủy
$cancellation_rate = 0;
if ($month_stats['total_bookings'] > 0) {
    $cancellation_rate = ($month_stats['cancelled'] / $month_stats['total_bookings']) * 100;
}

// Tính tỷ lệ hoàn thành
$completion_rate = 0;
if ($month_stats['total_bookings'] > 0) {
    $completion_rate = ($month_stats['completed'] / $month_stats['total_bookings']) * 100;
}

// Chuẩn bị dữ liệu cho AI
$report_data = [
    'period' => [
        'today' => $today,
        'this_month' => $this_month,
        'this_year' => $this_year
    ],
    'revenue' => [
        'today' => $today_stats['total_revenue'],
        'this_month' => $month_stats['total_revenue'],
        'last_month' => $last_month_stats['total_revenue'],
        'year' => $year_stats['total_revenue'],
        'growth_rate' => round($revenue_growth, 2)
    ],
    'bookings' => [
        'today' => $today_stats['total_bookings'],
        'this_month' => $month_stats['total_bookings'],
        'last_month' => $last_month_stats['total_bookings'],
        'year' => $year_stats['total_bookings'],
        'growth_rate' => round($booking_growth, 2)
    ],
    'status' => [
        'pending' => $month_stats['pending'],
        'confirmed' => $month_stats['confirmed'],
        'completed' => $month_stats['completed'],
        'cancelled' => $month_stats['cancelled'],
        'completion_rate' => round($completion_rate, 2),
        'cancellation_rate' => round($cancellation_rate, 2)
    ],
    'top_services' => $top_services,
    'top_staff' => $top_staff
];

// Tạo prompt cho AI
$prompt = "Bạn là chuyên gia phân tích kinh doanh cho salon tóc. Hãy phân tích dữ liệu báo cáo sau và đưa ra insights chuyên sâu:\n\n";
$prompt .= "📊 DỮ LIỆU THÁNG " . date('m/Y') . ":\n\n";
$prompt .= "💰 DOANH THU:\n";
$prompt .= "- Hôm nay: " . number_format($report_data['revenue']['today'] ?? 0) . " VNĐ\n";
$prompt .= "- Tháng này: " . number_format($report_data['revenue']['this_month']) . " VNĐ\n";
$prompt .= "- Tháng trước: " . number_format($report_data['revenue']['last_month']) . " VNĐ\n";
$prompt .= "- Tăng trưởng: " . $report_data['revenue']['growth_rate'] . "%\n\n";

$prompt .= "📅 LỊCH HẸN:\n";
$prompt .= "- Tổng lịch tháng này: " . $report_data['bookings']['this_month'] . "\n";
$prompt .= "- Hoàn thành: " . $report_data['status']['completed'] . " (" . $report_data['status']['completion_rate'] . "%)\n";
$prompt .= "- Đã hủy: " . $report_data['status']['cancelled'] . " (" . $report_data['status']['cancellation_rate'] . "%)\n";
$prompt .= "- Chờ xác nhận: " . $report_data['status']['pending'] . "\n\n";

$prompt .= "🏆 TOP DỊCH VỤ:\n";
foreach ($top_services as $idx => $service) {
    $prompt .= ($idx + 1) . ". " . $service['service_name'] . " - " . $service['booking_count'] . " lượt (" . number_format($service['revenue']) . " VNĐ)\n";
}

$prompt .= "\n👥 TOP NHÂN VIÊN:\n";
foreach ($top_staff as $idx => $staff) {
    $prompt .= ($idx + 1) . ". " . $staff['full_name'] . " - " . $staff['booking_count'] . " lượt (" . number_format($staff['revenue']) . " VNĐ)\n";
}

$prompt .= "\n📋 YÊU CẦU PHÂN TÍCH:\n";
$prompt .= "1. Đánh giá tổng quan tình hình kinh doanh (tích cực/tiêu cực)\n";
$prompt .= "2. Phân tích xu hướng tăng trưởng và nguyên nhân\n";
$prompt .= "3. Đánh giá tỷ lệ hủy lịch (cao/thấp) và gợi ý cải thiện\n";
$prompt .= "4. Nhận xét về hiệu suất nhân viên\n";
$prompt .= "5. Gợi ý 3-5 hành động cụ thể để cải thiện doanh thu\n";
$prompt .= "6. Dự báo xu hướng tháng tới\n\n";
$prompt .= "Hãy trả lời bằng tiếng Việt, ngắn gọn, súc tích, sử dụng emoji phù hợp. Tập trung vào insights có giá trị và actionable.";

// Gọi Gemini API (dùng model riêng cho Report Analysis)
$api_url = GEMINI_REPORT_API_URL . '?key=' . GEMINI_API_KEY;

$request_data = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.95,
        'maxOutputTokens' => 8192, // Tăng lên để AI viết đầy đủ hơn
    ]
];

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200 && $response) {
    $result = json_decode($response, true);
    
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $analysis = $result['candidates'][0]['content']['parts'][0]['text'];
        
        echo json_encode([
            'success' => true,
            'analysis' => $analysis,
            'data' => $report_data
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể phân tích dữ liệu',
            'data' => $report_data
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi kết nối AI API',
        'data' => $report_data
    ], JSON_UNESCAPED_UNICODE);
}
?>
