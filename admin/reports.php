<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Booking.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(BASE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();
$bookingModel = new Booking($db);

$today = date('Y-m-d');
$this_month = date('Y-m');
$this_year = date('Y');

$today_stats = $bookingModel->getStats($today, $today);
$month_stats = $bookingModel->getStats($this_month.'-01', date('Y-m-t'));
$year_stats = $bookingModel->getStats($this_year.'-01-01', $this_year.'-12-31');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo thống kê - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .ai-analysis-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            width: 100%;
        }
        .ai-analysis-content {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
            line-height: 1.8;
            min-height: 300px;
            max-height: 600px;
            overflow-y: auto;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: pre-line;
            width: 100%;
        }
        
        .ai-analysis-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .ai-analysis-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        .ai-analysis-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }
        
        .ai-analysis-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        .ai-loading {
            text-align: center;
            padding: 40px;
        }
        .ai-loading .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        .refresh-analysis-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            transition: all 0.3s;
        }
        .refresh-analysis-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php include 'includes/sidebar.php'; ?>
        <div class="flex-grow-1">
            <?php include 'includes/navbar.php'; ?>
            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold mb-0">Báo cáo thống kê</h2>
                    <button class="btn btn-primary" onclick="loadAIAnalysis()">
                        <i class="fas fa-robot me-2"></i>Phân tích AI
                    </button>
                </div>

                <!-- AI Analysis Section -->
                <div id="aiAnalysisSection" style="display: none;" class="mb-4">
                    <div class="ai-analysis-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-brain me-2"></i>Phân tích thông minh bằng AI
                            </h5>
                            <button class="refresh-analysis-btn" onclick="loadAIAnalysis()">
                                <i class="fas fa-sync-alt me-2"></i>Làm mới
                            </button>
                        </div>
                        <div id="aiAnalysisContent" class="ai-analysis-content">
                            <div class="ai-loading">
                                <div class="spinner-border text-light" role="status">
                                    <span class="visually-hidden">Đang phân tích...</span>
                                </div>
                                <p class="mt-3 mb-0">AI đang phân tích dữ liệu của bạn...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">Hôm nay</h6>
                                <h3 class="fw-bold text-primary"><?php echo formatCurrency($today_stats['total_revenue'] ?? 0); ?></h3>
                                <p class="mb-0 small text-muted"><?php echo $today_stats['total_bookings'] ?? 0; ?> lịch hẹn</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">Tháng này</h6>
                                <h3 class="fw-bold text-success"><?php echo formatCurrency($month_stats['total_revenue'] ?? 0); ?></h3>
                                <p class="mb-0 small text-muted"><?php echo $month_stats['total_bookings'] ?? 0; ?> lịch hẹn</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">Năm nay</h6>
                                <h3 class="fw-bold text-info"><?php echo formatCurrency($year_stats['total_revenue'] ?? 0); ?></h3>
                                <p class="mb-0 small text-muted"><?php echo $year_stats['total_bookings'] ?? 0; ?> lịch hẹn</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Thống kê theo trạng thái</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Hoàn thành</span>
                                        <strong><?php echo $month_stats['completed'] ?? 0; ?></strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: <?php echo ($month_stats['total_bookings'] > 0) ? ($month_stats['completed'] / $month_stats['total_bookings'] * 100) : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Đã xác nhận</span>
                                        <strong><?php echo $month_stats['confirmed'] ?? 0; ?></strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" style="width: <?php echo ($month_stats['total_bookings'] > 0) ? ($month_stats['confirmed'] / $month_stats['total_bookings'] * 100) : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Chờ xác nhận</span>
                                        <strong><?php echo $month_stats['pending'] ?? 0; ?></strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: <?php echo ($month_stats['total_bookings'] > 0) ? ($month_stats['pending'] / $month_stats['total_bookings'] * 100) : 0; ?>%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Đã hủy</span>
                                        <strong><?php echo $month_stats['cancelled'] ?? 0; ?></strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" style="width: <?php echo ($month_stats['total_bookings'] > 0) ? ($month_stats['cancelled'] / $month_stats['total_bookings'] * 100) : 0; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadAIAnalysis() {
            const section = document.getElementById('aiAnalysisSection');
            const content = document.getElementById('aiAnalysisContent');
            
            // Show section
            section.style.display = 'block';
            
            // Show loading
            content.innerHTML = `
                <div class="ai-loading">
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Đang phân tích...</span>
                    </div>
                    <p class="mt-3 mb-0">AI đang phân tích dữ liệu của bạn...</p>
                </div>
            `;
            
            // Scroll to AI section
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Call API (with credentials to pass session)
            fetch('<?php echo BASE_URL; ?>/api/ai-report-analysis.php', {
                method: 'GET',
                credentials: 'same-origin', // Pass session cookie
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Format markdown-like text to HTML
                        let analysis = data.analysis;
                        
                        // Convert **bold** to <strong>
                        analysis = analysis.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                        
                        // Convert *italic* to <em>
                        analysis = analysis.replace(/\*(.+?)\*/g, '<em>$1</em>');
                        
                        // Convert line breaks
                        analysis = analysis.replace(/\n/g, '<br>');
                        
                        // Convert --- to <hr>
                        analysis = analysis.replace(/---/g, '<hr style="border: 1px solid rgba(255,255,255,0.2); margin: 15px 0;">');
                        
                        content.innerHTML = analysis;
                    } else {
                        content.innerHTML = `
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                <p>${data.message || 'Không thể phân tích dữ liệu'}</p>
                                <button class="btn btn-light btn-sm" onclick="loadAIAnalysis()">
                                    <i class="fas fa-redo me-2"></i>Thử lại
                                </button>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                            <p>Lỗi kết nối. Vui lòng thử lại sau.</p>
                            <button class="btn btn-light btn-sm" onclick="loadAIAnalysis()">
                                <i class="fas fa-redo me-2"></i>Thử lại
                            </button>
                        </div>
                    `;
                });
        }
    </script>
</body>
</html>
