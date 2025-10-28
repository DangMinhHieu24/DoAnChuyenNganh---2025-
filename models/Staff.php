<?php
/**
 * Staff Model
 * Model quản lý nhân viên
 */

class Staff {
    private $conn;
    private $table = 'staff';

    public $staff_id;
    public $user_id;
    public $specialization;
    public $experience_years;
    public $rating;
    public $total_bookings;
    public $bio;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Lấy tất cả nhân viên
     */
    public function getAllStaff($status = 'active') {
        $query = "SELECT s.*, u.full_name, u.phone, u.email, u.avatar
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.user_id = u.user_id";
        
        if ($status) {
            $query .= " WHERE s.status = :status";
        }
        
        $query .= " ORDER BY s.rating DESC, u.full_name";

        $stmt = $this->conn->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy nhân viên theo ID
     */
    public function getStaffById($id) {
        $query = "SELECT s.*, u.full_name, u.phone, u.email, u.avatar, u.address
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.user_id = u.user_id
                  WHERE s.staff_id = :staff_id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    /**
     * Lấy nhân viên theo user_id
     */
    public function getStaffByUserId($user_id) {
        $query = "SELECT s.*, u.full_name, u.phone, u.email, u.avatar
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.user_id = u.user_id
                  WHERE s.user_id = :user_id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    /**
     * Lấy nhân viên theo dịch vụ
     */
    public function getStaffByService($service_id, $status = 'active') {
        $query = "SELECT s.*, u.full_name, u.phone, u.avatar
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.user_id = u.user_id
                  LEFT JOIN staff_services ss ON s.staff_id = ss.staff_id
                  WHERE ss.service_id = :service_id";
        
        if ($status) {
            $query .= " AND s.status = :status";
        }
        
        $query .= " ORDER BY s.rating DESC, u.full_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $service_id);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo nhân viên mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, specialization, experience_years, bio, status) 
                  VALUES (:user_id, :specialization, :experience_years, :bio, :status)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':specialization', $this->specialization);
        $stmt->bindParam(':experience_years', $this->experience_years);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->staff_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Cập nhật nhân viên
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET specialization = :specialization,
                      experience_years = :experience_years,
                      bio = :bio,
                      status = :status
                  WHERE staff_id = :staff_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':specialization', $this->specialization);
        $stmt->bindParam(':experience_years', $this->experience_years);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':staff_id', $this->staff_id);

        return $stmt->execute();
    }

    /**
     * Xóa nhân viên
     */
    public function delete($staff_id) {
        $query = "DELETE FROM " . $this->table . " WHERE staff_id = :staff_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        return $stmt->execute();
    }

    /**
     * Lấy dịch vụ của nhân viên
     */
    public function getStaffServices($staff_id) {
        $query = "SELECT s.*, c.category_name
                  FROM services s
                  LEFT JOIN categories c ON s.category_id = c.category_id
                  LEFT JOIN staff_services ss ON s.service_id = ss.service_id
                  WHERE ss.staff_id = :staff_id
                  ORDER BY c.display_order, s.service_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm dịch vụ cho nhân viên
     */
    public function addService($staff_id, $service_id) {
        $query = "INSERT INTO staff_services (staff_id, service_id) VALUES (:staff_id, :service_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':service_id', $service_id);
        return $stmt->execute();
    }

    /**
     * Xóa dịch vụ của nhân viên
     */
    public function removeService($staff_id, $service_id) {
        $query = "DELETE FROM staff_services WHERE staff_id = :staff_id AND service_id = :service_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':service_id', $service_id);
        return $stmt->execute();
    }

    /**
     * Lấy giờ làm việc của nhân viên
     */
    public function getWorkingHours($staff_id, $day_of_week = null) {
        $query = "SELECT * FROM working_hours WHERE staff_id = :staff_id";
        
        if ($day_of_week !== null) {
            $query .= " AND day_of_week = :day_of_week";
        }
        
        $query .= " ORDER BY day_of_week, start_time";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        
        if ($day_of_week !== null) {
            $stmt->bindParam(':day_of_week', $day_of_week);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy slot thời gian trống của nhân viên
     */
    public function getAvailableSlots($staff_id, $date, $duration) {
        // Lấy ngày trong tuần (0=CN, 1=T2, ..., 6=T7)
        $day_of_week = date('w', strtotime($date));
        
        // Lấy giờ làm việc
        $working_hours = $this->getWorkingHours($staff_id, $day_of_week);
        
        if (empty($working_hours)) {
            return [];
        }
        
        $wh = $working_hours[0];
        
        // Lấy các booking đã có trong ngày
        $query = "SELECT booking_time, duration 
                  FROM bookings 
                  WHERE staff_id = :staff_id 
                  AND booking_date = :date 
                  AND status NOT IN ('cancelled', 'no_show')
                  ORDER BY booking_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Tạo tất cả các slot trong giờ làm việc
        $all_slots = generateTimeSlots($wh['start_time'], $wh['end_time'], SLOT_DURATION);
        $available_slots = [];
        
        foreach ($all_slots as $slot) {
            $slot_end = date('H:i', strtotime($slot) + ($duration * 60));
            $is_available = true;
            
            // Kiểm tra xem slot có trùng với booking nào không
            foreach ($bookings as $booking) {
                $booking_start = $booking['booking_time'];
                $booking_end = date('H:i', strtotime($booking_start) + ($booking['duration'] * 60));
                
                // Kiểm tra overlap
                if (($slot >= $booking_start && $slot < $booking_end) ||
                    ($slot_end > $booking_start && $slot_end <= $booking_end) ||
                    ($slot <= $booking_start && $slot_end >= $booking_end)) {
                    $is_available = false;
                    break;
                }
            }
            
            // Kiểm tra slot có vượt quá giờ làm việc không
            if ($slot_end > $wh['end_time']) {
                $is_available = false;
            }
            
            // Nếu là hôm nay, kiểm tra xem đã qua giờ chưa
            if ($date == date('Y-m-d') && $slot <= date('H:i')) {
                $is_available = false;
            }
            
            if ($is_available) {
                $available_slots[] = $slot;
            }
        }
        
        return $available_slots;
    }

    /**
     * Lấy thống kê nhân viên
     */
    public function getStaffStats($staff_id) {
        $query = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as total_revenue,
                    AVG(CASE WHEN status = 'completed' THEN total_price ELSE NULL END) as avg_booking_value
                  FROM bookings
                  WHERE staff_id = :staff_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
