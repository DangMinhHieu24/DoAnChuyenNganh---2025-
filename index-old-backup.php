<?php
/**
 * Homepage - Trang chủ
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
    <title><?php echo SITE_NAME; ?> - Đặt lịch online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Đặt lịch làm đẹp <br>dễ dàng & nhanh chóng</h1>
                    <p class="lead mb-4">Trải nghiệm dịch vụ chăm sóc sắc đẹp chuyên nghiệp với đội ngũ nhân viên giàu kinh nghiệm</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="pages/booking.php" class="btn btn-light btn-lg px-4 shadow-lg">
                            <i class="fas fa-calendar-check me-2"></i>Đặt lịch ngay
                        </a>
                        <a href="pages/services.php" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-th-large me-2"></i>Xem dịch vụ
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/hero-image.svg" alt="Salon" class="img-fluid rounded-4 shadow-lg" onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-clock fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Đặt lịch 24/7</h5>
                        <p class="text-muted mb-0">Đặt lịch mọi lúc mọi nơi, tiện lợi và nhanh chóng</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-user-check fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Nhân viên chuyên nghiệp</h5>
                        <p class="text-muted mb-0">Đội ngũ nhân viên giàu kinh nghiệm, tay nghề cao</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-tags fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Giá cả hợp lý</h5>
                        <p class="text-muted mb-0">Nhiều ưu đãi và khuyến mãi hấp dẫn</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-3">An toàn & vệ sinh</h5>
                        <p class="text-muted mb-0">Đảm bảo vệ sinh và an toàn tuyệt đối</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Danh mục dịch vụ</h2>
                <p class="text-muted">Khám phá các dịch vụ làm đẹp đa dạng của chúng tôi</p>
            </div>
            <div class="row g-4">
                <?php foreach ($categories as $category): ?>
                <div class="col-md-4 col-lg-2">
                    <a href="pages/services.php?category=<?php echo $category['category_id']; ?>" class="text-decoration-none">
                        <div class="category-card text-center p-4">
                            <div class="category-icon mb-3">
                                <i class="fas <?php echo $category['icon']; ?> fa-3x text-primary"></i>
                            </div>
                            <h6 class="mb-2"><?php echo htmlspecialchars($category['category_name']); ?></h6>
                            <small class="text-muted"><?php echo $category['service_count']; ?> dịch vụ</small>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Popular Services Section -->
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
                            <img src="<?php echo getServiceImageUrl($service['image']); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                            <div class="service-badge"><?php echo htmlspecialchars($service['category_name']); ?></div>
                        </div>
                        <div class="service-body p-4">
                            <h5 class="mb-2"><?php echo htmlspecialchars($service['service_name']); ?></h5>
                            <p class="text-muted small mb-3"><?php echo htmlspecialchars(substr($service['description'], 0, 100)); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="service-price"><?php echo formatCurrency($service['price']); ?></div>
                                    <small class="text-muted"><i class="far fa-clock"></i> <?php echo $service['duration']; ?> phút</small>
                                </div>
                                <a href="pages/booking.php?service=<?php echo $service['service_id']; ?>" class="btn btn-primary">
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

    <!-- CTA Section -->
    <section class="cta-section py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-3">Sẵn sàng trải nghiệm dịch vụ của chúng tôi?</h2>
                    <p class="lead mb-0">Đặt lịch ngay hôm nay để nhận ưu đãi đặc biệt dành cho khách hàng mới!</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="pages/booking.php" class="btn btn-light btn-lg">
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
