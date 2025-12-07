<?php
/**
 * AI Hair Consultant Page
 * Tư vấn kiểu tóc qua ảnh với AI
 */

session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/functions.php';

$pageTitle = 'AI Tư Vấn Kiểu Tóc';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/ai-hair-consultant.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="ai-consultant-page">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="hero-title">
                            <i class="fas fa-magic"></i>
                            AI Tư Vấn Kiểu Tóc
                        </h1>
                        <p class="hero-subtitle">
                            Công nghệ AI phân tích khuôn mặt và gợi ý kiểu tóc phù hợp nhất với bạn
                        </p>
                        <div class="hero-features">
                            <div class="feature-item">
                                <i class="fas fa-brain"></i>
                                <span>AI Thông Minh</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-bolt"></i>
                                <span>Nhanh Chóng</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-star"></i>
                                <span>Chính Xác</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hero-image">
                            <img src="<?php echo BASE_URL; ?>/assets/images/ai-consultant-hero.svg" alt="AI Consultant" onerror="this.style.display='none'">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Upload Section -->
        <section class="upload-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="upload-card">
                            <div class="upload-header">
                                <h2>Upload Ảnh Của Bạn</h2>
                                <p>Chụp hoặc chọn ảnh selfie rõ mặt để AI phân tích</p>
                            </div>
                            
                            <div class="upload-area" id="uploadArea">
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h3>Kéo thả ảnh vào đây</h3>
                                    <p>hoặc</p>
                                    <button class="btn btn-primary" onclick="document.getElementById('imageInput').click()">
                                        <i class="fas fa-image"></i> Chọn Ảnh
                                    </button>
                                    <input type="file" id="imageInput" accept="image/*" style="display: none;">
                                    
                                    <div class="upload-tips">
                                        <small>
                                            <i class="fas fa-info-circle"></i>
                                            Tips: Ảnh rõ mặt, ánh sáng tốt, không đeo kính/mũ
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="image-preview" id="imagePreview" style="display: none;">
                                    <img id="previewImage" src="" alt="Preview">
                                    <div class="preview-actions">
                                        <button class="btn btn-secondary" onclick="resetUpload()">
                                            <i class="fas fa-redo"></i> Chọn Ảnh Khác
                                        </button>
                                        <button class="btn btn-primary" onclick="analyzeImage()">
                                            <i class="fas fa-magic"></i> Phân Tích Ngay
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="analyzing-state" id="analyzingState" style="display: none;">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <h3>AI đang phân tích...</h3>
                                    <p>Vui lòng đợi trong giây lát</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Result Section -->
        <section class="result-section" id="resultSection" style="display: none;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="result-image-card">
                            <h3>Ảnh Của Bạn</h3>
                            <img id="resultImage" src="" alt="Your Photo">
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                        <div class="result-analysis-card">
                            <h3>
                                <i class="fas fa-sparkles"></i>
                                Kết Quả Phân Tích AI
                            </h3>
                            <div id="analysisResult" class="analysis-content"></div>
                            
                            <div class="result-actions">
                                <button class="btn btn-primary" onclick="scrollToBooking()">
                                    <i class="fas fa-calendar-check"></i>
                                    Đặt Lịch Ngay
                                </button>
                                <button class="btn btn-outline-primary" onclick="resetUpload()">
                                    <i class="fas fa-redo"></i>
                                    Thử Lại
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- How It Works -->
        <section class="how-it-works">
            <div class="container">
                <h2 class="section-title">Cách Hoạt Động</h2>
                <div class="row">
                    <div class="col-md-3">
                        <div class="step-card">
                            <div class="step-number">1</div>
                            <i class="fas fa-camera"></i>
                            <h4>Upload Ảnh</h4>
                            <p>Chụp hoặc chọn ảnh selfie của bạn</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="step-card">
                            <div class="step-number">2</div>
                            <i class="fas fa-brain"></i>
                            <h4>AI Phân Tích</h4>
                            <p>AI phân tích khuôn mặt và đặc điểm</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="step-card">
                            <div class="step-number">3</div>
                            <i class="fas fa-lightbulb"></i>
                            <h4>Nhận Gợi Ý</h4>
                            <p>Xem kiểu tóc phù hợp nhất</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="step-card">
                            <div class="step-number">4</div>
                            <i class="fas fa-calendar-check"></i>
                            <h4>Đặt Lịch</h4>
                            <p>Đặt lịch với stylist ngay</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/ai-hair-consultant.js"></script>
</body>
</html>
