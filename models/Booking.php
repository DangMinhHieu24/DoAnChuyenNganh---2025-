<?php
/**
 * Booking Model
 * Model quản lý đặt lịch
 */

class Booking {
    private $conn;
    private $table = 'bookings';

    public $booking_id;
    public $customer_id;
    public $service_id;
    public $staff_id;
    public $booking_date;
    public $booking_time;
    public $duration;
    public $total_price;
    public $status;
    public $payment_status;
    public $payment_method;
    public $notes;
    public $cancellation_reason;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Tạo booking mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (customer_id, service_id, staff_id, booking_date, booking_time, 
                   duration, total_price, status, payment_status, payment_method, notes) 
                  VALUES (:customer_id, :service_id, :staff_id, :booking_date, :booking_time,
                          :duration, :total_price, :status, :payment_status, :payment_method, :notes)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':service_id', $this->service_id);
        $stmt->bindParam(':staff_id', $this->staff_id);
        $stmt->bindParam(':booking_date', $this->booking_date);
        $stmt->bindParam(':booking_time', $this->booking_time);
        $stmt->bindParam(':duration', $this->duration);
        $stmt->bindParam(':total_price', $this->total_price);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':payment_status', $this->payment_status);
        $stmt->bindParam(':payment_method', $this->payment_method);
        $stmt->bindParam(':notes', $this->notes);

        if ($stmt->execute()) {
            $this->booking_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Lấy booking theo ID
     */
    public function getBookingById($id) {
        $query = "SELECT b.*, 
                         u.full_name as customer_name, u.phone as customer_phone, u.email as customer_email,
                         s.service_name, s.price as service_price,
                         st.full_name as staff_name,
                         c.category_name
                  FROM " . $this->table . " b
                  LEFT JOIN users u ON b.customer_id = u.user_id
                  LEFT JOIN services s ON b.service_id = s.service_id
                  LEFT JOIN categories c ON s.category_id = c.category_id
                  LEFT JOIN staff stf ON b.staff_id = stf.staff_id
                  LEFT JOIN users st ON stf.user_id = st.user_id
                  WHERE b.booking_id = :booking_id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    /**
     * Lấy bookings theo customer
     */
    public function getBookingsByCustomer($customer_id, $status = null, $limit = null, $offset = 0) {
        $query = "SELECT b.*, 
                         s.service_name, s.price as service_price,
                         st.full_name as staff_name,
                         c.category_name
                  FROM " . $this->table . " b
                  LEFT JOIN services s ON b.service_id = s.service_id
                  LEFT JOIN categories c ON s.category_id = c.category_id
                  LEFT JOIN staff stf ON b.staff_id = stf.staff_id
                  LEFT JOIN users st ON stf.user_id = st.user_id
                  WHERE b.customer_id = :customer_id";
        
        if ($status) {
            $query .= " AND b.status = :status";
        }
        
        $query .= " ORDER BY b.booking_date DESC, b.booking_time DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy bookings theo staff
     */
    public function getBookingsByStaff($staff_id, $date = null, $status = null) {
        $query = "SELECT b.*, 
                         u.full_name as customer_name, u.phone as customer_phone,
                         s.service_name, s.price as service_price,
                         c.category_name
                  FROM " . $this->table . " b
                  LEFT JOIN users u ON b.customer_id = u.user_id
                  LEFT JOIN services s ON b.service_id = s.service_id
                  LEFT JOIN categories c ON s.category_id = c.category_id
                  WHERE b.staff_id = :staff_id";
        
        if ($date) {
            $query .= " AND b.booking_date = :date";
        }
        if ($status) {
            $query .= " AND b.status = :status";
        }
        
        $query .= " ORDER BY b.booking_date DESC, b.booking_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        
        if ($date) {
            $stmt->bindParam(':date', $date);
        }
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả bookings (admin)
     */
    public function getAllBookings($filters = [], $limit = null, $offset = 0) {
        $query = "SELECT b.*, 
                         u.full_name as customer_name, u.phone as customer_phone,
                         s.service_name,
                         st.full_name as staff_name,
                         c.category_name
                  FROM " . $this->table . " b
                  LEFT JOIN users u ON b.customer_id = u.user_id
                  LEFT JOIN services s ON b.service_id = s.service_id
                  LEFT JOIN categories c ON s.category_id = c.category_id
                  LEFT JOIN staff stf ON b.staff_id = stf.staff_id
                  LEFT JOIN users st ON stf.user_id = st.user_id
                  WHERE 1=1";
        
        if (!empty($filters['status'])) {
            $query .= " AND b.status = :status";
        }
        if (!empty($filters['date'])) {
            $query .= " AND b.booking_date = :date";
        }
        if (!empty($filters['staff_id'])) {
            $query .= " AND b.staff_id = :staff_id";
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND b.customer_id = :customer_id";
        }
        
        $query .= " ORDER BY b.booking_date DESC, b.booking_time DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->conn->prepare($query);
        
        if (!empty($filters['status'])) {
            $stmt->bindParam(':status', $filters['status']);
        }
        if (!empty($filters['date'])) {
            $stmt->bindParam(':date', $filters['date']);
        }
        if (!empty($filters['staff_id'])) {
            $stmt->bindParam(':staff_id', $filters['staff_id']);
        }
        if (!empty($filters['customer_id'])) {
            $stmt->bindParam(':customer_id', $filters['customer_id']);
        }
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm số lượng bookings
     */
    public function countBookings($filters = []) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        
        if (!empty($filters['status'])) {
            $query .= " AND status = :status";
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND customer_id = :customer_id";
        }
        if (!empty($filters['staff_id'])) {
            $query .= " AND staff_id = :staff_id";
        }

        $stmt = $this->conn->prepare($query);
        
        if (!empty($filters['status'])) {
            $stmt->bindParam(':status', $filters['status']);
        }
        if (!empty($filters['customer_id'])) {
            $stmt->bindParam(':customer_id', $filters['customer_id']);
        }
        if (!empty($filters['staff_id'])) {
            $stmt->bindParam(':staff_id', $filters['staff_id']);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Kiểm tra slot thời gian có trống không
     */
    public function checkAvailability($staff_id, $date, $time, $duration, $exclude_booking_id = null) {
        $query = "SELECT COUNT(*) as count
                  FROM " . $this->table . "
                  WHERE staff_id = :staff_id
                  AND booking_date = :date
                  AND status NOT IN ('cancelled', 'no_show')";
        
        if ($exclude_booking_id) {
            $query .= " AND booking_id != :exclude_booking_id";
        }
        
        $query .= " AND (
                      (booking_time <= :time1 AND ADDTIME(booking_time, SEC_TO_TIME(duration * 60)) > :time2)
                      OR
                      (booking_time < ADDTIME(:time3, SEC_TO_TIME(:duration * 60)) AND booking_time >= :time4)
                    )";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time1', $time);
        $stmt->bindParam(':time2', $time);
        $stmt->bindParam(':time3', $time);
        $stmt->bindParam(':time4', $time);
        $stmt->bindParam(':duration', $duration, PDO::PARAM_INT);
        
        if ($exclude_booking_id) {
            $stmt->bindParam(':exclude_booking_id', $exclude_booking_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'] == 0;
    }

    /**
     * Cập nhật status booking
     */
    public function updateStatus($booking_id, $status, $cancellation_reason = null) {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status";
        
        if ($cancellation_reason) {
            $query .= ", cancellation_reason = :cancellation_reason";
        }
        
        $query .= " WHERE booking_id = :booking_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':booking_id', $booking_id);
        
        if ($cancellation_reason) {
            $stmt->bindParam(':cancellation_reason', $cancellation_reason);
        }

        return $stmt->execute();
    }

    /**
     * Cập nhật payment status
     */
    public function updatePaymentStatus($booking_id, $payment_status, $payment_method = null) {
        $query = "UPDATE " . $this->table . " 
                  SET payment_status = :payment_status";
        
        if ($payment_method) {
            $query .= ", payment_method = :payment_method";
        }
        
        $query .= " WHERE booking_id = :booking_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':payment_status', $payment_status);
        $stmt->bindParam(':booking_id', $booking_id);
        
        if ($payment_method) {
            $stmt->bindParam(':payment_method', $payment_method);
        }

        return $stmt->execute();
    }

    /**
     * Cập nhật booking
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET service_id = :service_id,
                      staff_id = :staff_id,
                      booking_date = :booking_date,
                      booking_time = :booking_time,
                      duration = :duration,
                      total_price = :total_price,
                      notes = :notes
                  WHERE booking_id = :booking_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':service_id', $this->service_id);
        $stmt->bindParam(':staff_id', $this->staff_id);
        $stmt->bindParam(':booking_date', $this->booking_date);
        $stmt->bindParam(':booking_time', $this->booking_time);
        $stmt->bindParam(':duration', $this->duration);
        $stmt->bindParam(':total_price', $this->total_price);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':booking_id', $this->booking_id);

        return $stmt->execute();
    }

    /**
     * Xóa booking
     */
    public function delete($booking_id) {
        $query = "DELETE FROM " . $this->table . " WHERE booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id);
        return $stmt->execute();
    }

    /**
     * Lấy thống kê booking
     */
    public function getStats($start_date = null, $end_date = null) {
        $query = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as total_revenue
                  FROM " . $this->table . "
                  WHERE 1=1";
        
        if ($start_date) {
            $query .= " AND booking_date >= :start_date";
        }
        if ($end_date) {
            $query .= " AND booking_date <= :end_date";
        }

        $stmt = $this->conn->prepare($query);
        
        if ($start_date) {
            $stmt->bindParam(':start_date', $start_date);
        }
        if ($end_date) {
            $stmt->bindParam(':end_date', $end_date);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy bookings sắp tới
     */
    public function getUpcomingBookings($customer_id, $limit = 5) {
        $query = "SELECT b.*, 
                         s.service_name,
                         st.full_name as staff_name
                  FROM " . $this->table . " b
                  LEFT JOIN services s ON b.service_id = s.service_id
                  LEFT JOIN staff stf ON b.staff_id = stf.staff_id
                  LEFT JOIN users st ON stf.user_id = st.user_id
                  WHERE b.customer_id = :customer_id
                  AND b.status IN ('pending', 'confirmed')
                  AND CONCAT(b.booking_date, ' ', b.booking_time) >= NOW()
                  ORDER BY b.booking_date ASC, b.booking_time ASC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy lịch hẹn của staff theo ngày
     */
    public function getBookingsByStaffAndDate($staff_id, $date) {
        $query = "SELECT b.*, s.service_name, s.duration, s.price,
                         u.full_name as customer_name, u.phone as customer_phone
                  FROM " . $this->table . " b
                  LEFT JOIN services s ON b.service_id = s.service_id
                  LEFT JOIN users u ON b.customer_id = u.user_id
                  WHERE b.staff_id = :staff_id
                  AND b.booking_date = :date
                  ORDER BY b.booking_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy lịch hẹn sắp tới của staff
     */
    public function getUpcomingBookingsByStaff($staff_id, $limit = 10) {
        $query = "SELECT b.*, s.service_name, s.duration, s.price,
                         u.full_name as customer_name, u.phone as customer_phone
                  FROM " . $this->table . " b
                  LEFT JOIN services s ON b.service_id = s.service_id
                  LEFT JOIN users u ON b.customer_id = u.user_id
                  WHERE b.staff_id = :staff_id
                  AND b.status IN ('pending', 'confirmed')
                  AND CONCAT(b.booking_date, ' ', b.booking_time) >= NOW()
                  ORDER BY b.booking_date ASC, b.booking_time ASC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hủy booking
     */
    public function cancelBooking($booking_id, $cancellation_reason) {
        $query = "UPDATE " . $this->table . "
                  SET status = 'cancelled',
                      cancellation_reason = :cancellation_reason,
                      updated_at = NOW()
                  WHERE booking_id = :booking_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->bindParam(':cancellation_reason', $cancellation_reason);
        
        return $stmt->execute();
    }

    /**
     * Lấy thống kê tổng quan của staff
     */
    public function getStaffStatistics($staff_id) {
        $query = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as total_revenue
                  FROM " . $this->table . "
                  WHERE staff_id = :staff_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thống kê theo tháng của staff
     */
    public function getStaffMonthlyStatistics($staff_id, $year = null, $month = null) {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');
        
        $query = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as monthly_revenue
                  FROM " . $this->table . "
                  WHERE staff_id = :staff_id 
                    AND YEAR(booking_date) = :year
                    AND MONTH(booking_date) = :month";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':month', $month);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy lịch hẹn theo trạng thái và ngày
     */
    public function getStaffBookingsFiltered($staff_id, $filters = []) {
        $query = "SELECT b.*, s.service_name, s.duration, s.price,
                         u.full_name as customer_name, u.phone as customer_phone
                  FROM " . $this->table . " b
                  JOIN services s ON b.service_id = s.service_id
                  JOIN users u ON b.customer_id = u.user_id
                  WHERE b.staff_id = :staff_id";
        
        if (!empty($filters['status'])) {
            $query .= " AND b.status = :status";
        }
        
        if (!empty($filters['date_from'])) {
            $query .= " AND b.booking_date >= :date_from";
        }
        
        if (!empty($filters['date_to'])) {
            $query .= " AND b.booking_date <= :date_to";
        }
        
        if (!empty($filters['search'])) {
            $query .= " AND (u.full_name LIKE :search OR u.phone LIKE :search OR s.service_name LIKE :search)";
        }
        
        $query .= " ORDER BY b.booking_date DESC, b.booking_time DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        
        if (!empty($filters['status'])) {
            $stmt->bindParam(':status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $stmt->bindParam(':date_from', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $stmt->bindParam(':date_to', $filters['date_to']);
        }
        
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $search);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
