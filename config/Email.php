<?php
/**
 * Email Helper Class
 * Gửi email thông báo
 */
class Email {
    private $from_email;
    private $from_name;

    public function __construct() {
        $this->from_email = SITE_EMAIL;
        $this->from_name = SITE_NAME;
    }

    /**
     * Gửi email đặt lịch thành công
     */
    public function sendBookingConfirmation($booking_data, $customer_email, $customer_name) {
        $subject = "[" . SITE_NAME . "] Xác nhận đặt lịch #" . $booking_data['booking_id'];
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { text-align: center; padding: 10px; color: #666; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                td { padding: 10px; border-bottom: 1px solid #ddd; }
                .label { font-weight: bold; width: 40%; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Xác nhận đặt lịch thành công!</h2>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>{$customer_name}</strong>,</p>
                    <p>Cảm ơn bạn đã đặt lịch tại <strong>" . SITE_NAME . "</strong>. Thông tin chi tiết:</p>
                    
                    <table>
                        <tr>
                            <td class='label'>Mã đặt lịch:</td>
                            <td>#{$booking_data['booking_id']}</td>
                        </tr>
                        <tr>
                            <td class='label'>Dịch vụ:</td>
                            <td>{$booking_data['service_name']}</td>
                        </tr>
                        <tr>
                            <td class='label'>Nhân viên:</td>
                            <td>{$booking_data['staff_name']}</td>
                        </tr>
                        <tr>
                            <td class='label'>Ngày:</td>
                            <td>" . date('d/m/Y', strtotime($booking_data['booking_date'])) . "</td>
                        </tr>
                        <tr>
                            <td class='label'>Giờ:</td>
                            <td>" . date('H:i', strtotime($booking_data['booking_time'])) . "</td>
                        </tr>
                        <tr>
                            <td class='label'>Thời gian:</td>
                            <td>{$booking_data['duration']} phút</td>
                        </tr>
                        <tr>
                            <td class='label'>Tổng tiền:</td>
                            <td><strong>" . number_format($booking_data['total_price']) . " VND</strong></td>
                        </tr>
                    </table>
                    
                    <p><strong>Lưu ý:</strong> Vui lòng đến đúng giờ. Nếu cần hủy lịch, vui lòng hủy trước " . BOOKING_CANCEL_HOURS . " giờ.</p>
                    
                    <p>Địa chỉ: <strong>" . SITE_ADDRESS . "</strong></p>
                    <p>Hotline: <strong>" . SITE_PHONE . "</strong></p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " " . SITE_NAME . ". All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->send($customer_email, $subject, $message);
    }

    /**
     * Gửi email xác nhận lịch hẹn
     */
    public function sendBookingApproved($booking_data, $customer_email, $customer_name) {
        $subject = "[" . SITE_NAME . "] Lịch hẹn đã được xác nhận";
        
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2 style='color: #4CAF50;'>Lịch hẹn đã được xác nhận!</h2>
            <p>Xin chào <strong>{$customer_name}</strong>,</p>
            <p>Lịch hẹn #{$booking_data['booking_id']} của bạn đã được xác nhận.</p>
            <p>Dịch vụ: <strong>{$booking_data['service_name']}</strong></p>
            <p>Thời gian: <strong>" . date('d/m/Y H:i', strtotime($booking_data['booking_date'] . ' ' . $booking_data['booking_time'])) . "</strong></p>
            <p>Chúng tôi rất mong được phục vụ bạn!</p>
            <p>Trân trọng,<br>" . SITE_NAME . "</p>
        </body>
        </html>
        ";
        
        return $this->send($customer_email, $subject, $message);
    }

    /**
     * Gửi email hủy lịch hẹn
     */
    public function sendBookingCancelled($booking_data, $customer_email, $customer_name) {
        $subject = "[" . SITE_NAME . "] Lịch hẹn đã bị hủy";
        
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2 style='color: #f44336;'>Lịch hẹn đã bị hủy</h2>
            <p>Xin chào <strong>{$customer_name}</strong>,</p>
            <p>Lịch hẹn #{$booking_data['booking_id']} của bạn đã bị hủy.</p>
            <p>Dịch vụ: <strong>{$booking_data['service_name']}</strong></p>
            <p>Chúng tôi rất tiếc về sự bất tiện này. Hy vọng được phục vụ bạn trong thời gian tới!</p>
            <p>Trân trọng,<br>" . SITE_NAME . "</p>
        </body>
        </html>
        ";
        
        return $this->send($customer_email, $subject, $message);
    }

    /**
     * Gửi email nhắc nhở trước lịch hẹn
     */
    public function sendBookingReminder($booking_data, $customer_email, $customer_name) {
        $subject = "[" . SITE_NAME . "] Nhắc nhở lịch hẹn";
        
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2 style='color: #FF9800;'>Nhắc nhở lịch hẹn</h2>
            <p>Xin chào <strong>{$customer_name}</strong>,</p>
            <p>Đây là email nhắc nhở về lịch hẹn của bạn:</p>
            <p>Dịch vụ: <strong>{$booking_data['service_name']}</strong></p>
            <p>Thời gian: <strong>" . date('d/m/Y H:i', strtotime($booking_data['booking_date'] . ' ' . $booking_data['booking_time'])) . "</strong></p>
            <p>Địa chỉ: <strong>" . SITE_ADDRESS . "</strong></p>
            <p>Chúng tôi rất mong được gặp bạn!</p>
            <p>Trân trọng,<br>" . SITE_NAME . "</p>
        </body>
        </html>
        ";
        
        return $this->send($customer_email, $subject, $message);
    }

    /**
     * Gửi email
     */
    private function send($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>" . "\r\n";
        
        // Note: Trong production, nên dùng PHPMailer hoặc SMTP
        // Hiện tại dùng mail() function của PHP (cần cấu hình SMTP server)
        
        // return mail($to, $subject, $message, $headers);
        
        // Tạm thời log thay vì gửi thật (để test)
        error_log("Email would be sent to: $to");
        error_log("Subject: $subject");
        return true; // Giả lập gửi thành công
    }
}
?>
