<?php
/**
 * User Model
 * Model quản lý người dùng
 */

class User {
    private $conn;
    private $table = 'users';

    public $user_id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $phone;
    public $address;
    public $role;
    public $avatar;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Đăng ký user mới
     */
    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                  (username, email, password, full_name, phone, role) 
                  VALUES (:username, :email, :password, :full_name, :phone, :role)";

        $stmt = $this->conn->prepare($query);

        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':role', $this->role);

        if ($stmt->execute()) {
            $this->user_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Đăng nhập
     */
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE (username = :username OR email = :email) 
                  AND status = 'active' 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $row['password'])) {
                $this->user_id = $row['user_id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->full_name = $row['full_name'];
                $this->phone = $row['phone'];
                $this->address = $row['address'];
                $this->role = $row['role'];
                $this->avatar = $row['avatar'];
                $this->status = $row['status'];
                return true;
            }
        }
        return false;
    }

    /**
     * Lấy thông tin user theo ID
     */
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    /**
     * Lấy tất cả users
     */
    public function getAllUsers($role = null, $status = null, $limit = null, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        
        if ($role) {
            $query .= " AND role = :role";
        }
        if ($status) {
            $query .= " AND status = :status";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->conn->prepare($query);
        
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
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
     * Đếm số lượng users
     */
    public function countUsers($role = null, $status = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        
        if ($role) {
            $query .= " AND role = :role";
        }
        if ($status) {
            $query .= " AND status = :status";
        }

        $stmt = $this->conn->prepare($query);
        
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Cập nhật thông tin user
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET full_name = :full_name, 
                      phone = :phone, 
                      address = :address";
        
        if ($this->avatar) {
            $query .= ", avatar = :avatar";
        }
        
        $query .= " WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':user_id', $this->user_id);
        
        if ($this->avatar) {
            $stmt->bindParam(':avatar', $this->avatar);
        }

        return $stmt->execute();
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword($user_id, $new_password) {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }

    /**
     * Cập nhật status
     */
    public function updateStatus($user_id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    /**
     * Xóa user
     */
    public function delete($user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    /**
     * Kiểm tra username đã tồn tại
     */
    public function usernameExists($username, $exclude_id = null) {
        $query = "SELECT user_id FROM " . $this->table . " WHERE username = :username";
        
        if ($exclude_id) {
            $query .= " AND user_id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Kiểm tra email đã tồn tại
     */
    public function emailExists($email, $exclude_id = null) {
        $query = "SELECT user_id FROM " . $this->table . " WHERE email = :email";
        
        if ($exclude_id) {
            $query .= " AND user_id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Tìm kiếm users
     */
    public function search($keyword, $role = null) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE (full_name LIKE :keyword1 
                  OR email LIKE :keyword2 
                  OR phone LIKE :keyword3 
                  OR username LIKE :keyword4)";
        
        if ($role) {
            $query .= " AND role = :role";
        }
        
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword1', $searchTerm);
        $stmt->bindParam(':keyword2', $searchTerm);
        $stmt->bindParam(':keyword3', $searchTerm);
        $stmt->bindParam(':keyword4', $searchTerm);
        
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
