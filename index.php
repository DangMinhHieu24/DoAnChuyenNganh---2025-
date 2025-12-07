<?php
/**
 * Homepage - Trang chủ CẢI TIẾN
 */
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/Category.php';
require_once 'models/Service.php';

$database = new Database();
$db = $database->getConnection();

$categoryModel = new Category($db);
$serviceModel = new Service($db);

$categories = $categoryModel->getCategoriesWithServiceCount();
$popularServices = $serviceModel->getPopularServices(6);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Đặt lịch salon online</title>
    <meta name="description" content="Đặt lịch salon spa chuyên nghiệp. Cắt tóc, nhuộm tóc, làm móng, trang điểm. Giá tốt, dịch vụ tận tâm.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/homepage-improvements.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section - CẢI TIẾN -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="display-3 fw-bold mb-3">
                        Làm đẹp chuyên nghiệp<br>
                        <span class="text-gradient">Đặt lịch 30 giây</span>
                    </h1>
                    <p class="lead mb-4 fs-5">Hệ thống đặt lịch thông minh - Nhân viên tay nghề cao - Giá cả hợp lý</p>
                    
                    <!-- Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="stat-box">
                                <h3 class="fw-bold mb-0">1000+</h3>
                                <small>Khách hàng</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-box">
                                <h3 class="fw-bold mb-0">50+</h3>
                                <small>Dịch vụ</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-box">
                                <h3 class="fw-bold mb-0">4.9⭐</h3>
                                <small>Đánh giá</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="pages/booking.php" class="btn btn-light btn-lg px-4 shadow-lg">
                            <i class="fas fa-calendar-check me-2"></i>Đặt lịch ngay
                        </a>
                        <a href="pages/services.php" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-th-large me-2"></i>Xem dịch vụ
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="hero-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=600" 
                             alt="Salon" 
                             class="img-fluid rounded-4 shadow-lg"
                             onerror="this.style.display='none'">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section - GIỮ NGUYÊN -->
    <section class="features-section py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-clock fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Đặt lịch 24/7</h5>
                        <p class="text-muted mb-0">Online mọi lúc mọi nơi, xác nhận tức thì</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-user-check fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Chuyên nghiệp</h5>
                        <p class="text-muted mb-0">Đội ngũ stylist 5+ năm kinh nghiệm</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-tags fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Giá tốt nhất</h5>
                        <p class="text-muted mb-0">Cam kết giá rẻ hơn 20% so với thị trường</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-3">An toàn & sạch sẽ</h5>
                        <p class="text-muted mb-0">Dụng cụ vô trùng, không gian 5 sao</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section - CẢI TIẾN Layout -->
    <section class="categories-section py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Danh mục dịch vụ</h2>
                <p class="text-muted">Khám phá các dịch vụ làm đẹp đa dạng</p>
            </div>
            <div class="row g-4 justify-content-center">
                <?php foreach ($categories as $category): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="pages/services.php?category=<?php echo $category['category_id']; ?>" class="text-decoration-none">
                        <div class="category-card text-center p-4">
                            <div class="category-icon mb-3">
                                <i class="fas <?php echo $category['icon']; ?> fa-3x text-primary"></i>
                            </div>
                            <h6 class="mb-2 fw-bold"><?php echo htmlspecialchars($category['category_name']); ?></h6>
                            <small class="text-muted"><?php echo $category['service_count']; ?> dịch vụ</small>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Popular Services Section - GIỮ NGUYÊN -->
    <section class="services-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Dịch vụ phổ biến</h2>
                <p class="text-muted">Những dịch vụ được khách hàng yêu thích nhất</p>
            </div>
            <div class="row g-4">
                <?php foreach ($popularServices as $service): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="service-card h-100">
                        <div class="service-image">
                            <img src="<?php echo getServiceImageUrl($service['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                            <div class="service-badge"><?php echo htmlspecialchars($service['category_name']); ?></div>
                        </div>
                        <div class="service-body p-4">
                            <h5 class="mb-2 fw-bold"><?php echo htmlspecialchars($service['service_name']); ?></h5>
                            <p class="text-muted small mb-3">
                                <?php echo htmlspecialchars(substr($service['description'], 0, 80)); ?>...
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="service-price"><?php echo formatCurrency($service['price']); ?></div>
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> <?php echo $service['duration']; ?> phút
                                    </small>
                                </div>
                                <a href="pages/booking.php?service=<?php echo $service['service_id']; ?>" 
                                   class="btn btn-primary">
                                    Đặt lịch
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-5">
                <a href="pages/services.php" class="btn btn-primary btn-lg px-5 py-3">
                    <i class="fas fa-th-large me-2"></i>
                    Xem tất cả dịch vụ
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- THÊM: Testimonials Section -->
    <section class="testimonials-section py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Khách hàng nói gì về chúng tôi</h2>
                <p class="text-muted">Hơn 1000+ đánh giá 5 sao</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card p-4 bg-white rounded-4 shadow-sm h-100">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-3">"Dịch vụ tuyệt vời! Nhân viên nhiệt tình, tư vấn rất tận tâm. Tóc sau khi cắt đẹp hơn mong đợi."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=Nguyen+Van+A&background=6366f1&color=fff" 
                                 class="rounded-circle me-3" width="50" height="50" alt="">
                            <div>
                                <h6 class="mb-0 fw-bold">Nguyễn Văn A</h6>
                                <small class="text-muted">Khách hàng thân thiết</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card p-4 bg-white rounded-4 shadow-sm h-100">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-3">"Đặt lịch online rất nhanh, không phải chờ đợi. Giá cả hợp lý, chất lượng tốt."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=Tran+Thi+B&background=8b5cf6&color=fff" 
                                 class="rounded-circle me-3" width="50" height="50" alt="">
                            <div>
                                <h6 class="mb-0 fw-bold">Trần Thị B</h6>
                                <small class="text-muted">Khách hàng mới</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card p-4 bg-white rounded-4 shadow-sm h-100">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-3">"Không gian sạch sẽ, thoáng mát. Stylist rất chuyên nghiệp và friendly. Sẽ quay lại!"</p>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=Le+Van+C&background=10b981&color=fff" 
                                 class="rounded-circle me-3" width="50" height="50" alt="">
                            <div>
                                <h6 class="mb-0 fw-bold">Lê Văn C</h6>
                                <small class="text-muted">Khách hàng VIP</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section - CẢI TIẾN -->
    <section class="cta-section py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 text-center text-lg-start">
                    <h2 class="fw-bold mb-3">
                        <i class="fas fa-gift me-2"></i>
                        Ưu đãi đặc biệt cho khách mới!
                    </h2>
                    <p class="lead mb-0">Giảm ngay 20% cho lần đặt lịch đầu tiên. Đăng ký ngay để không bỏ lỡ!</p>
                </div>
                <div class="col-lg-4 text-center text-lg-end mt-3 mt-lg-0">
                    <a href="pages/booking.php" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-calendar-check me-2"></i>Đặt lịch ngay
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
