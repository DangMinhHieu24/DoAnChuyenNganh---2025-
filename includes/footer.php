<!-- Footer -->
<footer class="footer bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-cut text-primary"></i> <?php echo defined('SITE_NAME') ? SITE_NAME : 'Salon Booking'; ?>
                </h5>
                <p class="text-muted">Hệ thống đặt lịch làm đẹp trực tuyến hàng đầu. Mang đến trải nghiệm dịch vụ chuyên nghiệp và tiện lợi nhất.</p>
                <div class="social-links mt-3">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-youtube fa-lg"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-tiktok fa-lg"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Liên kết</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo defined('BASE_URL') ? BASE_URL : '/Website_DatLich'; ?>" class="text-muted text-decoration-none">Trang chủ</a></li>
                    <li class="mb-2"><a href="<?php echo defined('BASE_URL') ? BASE_URL : '/Website_DatLich'; ?>/pages/services.php" class="text-muted text-decoration-none">Dịch vụ</a></li>
                    <li class="mb-2"><a href="<?php echo defined('BASE_URL') ? BASE_URL : '/Website_DatLich'; ?>/pages/booking.php" class="text-muted text-decoration-none">Đặt lịch</a></li>
                    <li class="mb-2"><a href="<?php echo defined('BASE_URL') ? BASE_URL : '/Website_DatLich'; ?>/pages/contact.php" class="text-muted text-decoration-none">Liên hệ</a></li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3">Hỗ trợ</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Câu hỏi thường gặp</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Chính sách bảo mật</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Điều khoản sử dụng</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Hướng dẫn đặt lịch</a></li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3">Liên hệ</h6>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <?php echo defined('SITE_ADDRESS') ? SITE_ADDRESS : '123 Đường ABC, Quận 1, TP.HCM'; ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2 text-primary"></i>
                        <?php echo defined('SITE_PHONE') ? SITE_PHONE : '1900-xxxx'; ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <?php echo defined('SITE_EMAIL') ? SITE_EMAIL : 'contact@salon.com'; ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        8:00 - 20:00 (Hàng ngày)
                    </li>
                </ul>
            </div>
        </div>
        
        <hr class="my-4 bg-secondary">
        
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> <?php echo defined('SITE_NAME') ? SITE_NAME : 'Salon Booking'; ?>. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 text-muted">Developed with <i class="fas fa-heart text-danger"></i> by Đặng Minh Hiếu</p>
            </div>
        </div>
    </div>
</footer>

<?php
// Include Chatbot Widget
if (file_exists(__DIR__ . '/chatbot-widget.php')) {
    include __DIR__ . '/chatbot-widget.php';
}
?>
