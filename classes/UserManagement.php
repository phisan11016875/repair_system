<?php
class UserManagement {
    private $conn;
    private $db;

    public function __construct() {
        require_once dirname(__DIR__) . '/classes/Database.php';
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // เพิ่มผู้ใช้ใหม่
    public function addUser($data) {
        try {
            // เข้ารหัสรหัสผ่าน
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $this->conn->prepare("INSERT INTO psd_users 
                (fullname, user_name, pass_word, email, telephone, dept_id, position, level) 
                VALUES (:fullname, :username, :password, :email, :tel, :dept_id, :position, :level)");
            
            $stmt->bindParam(':fullname', $data['fullname']);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':tel', $data['telephone']);
            $stmt->bindParam(':dept_id', $data['dept_id']);
            $stmt->bindParam(':position', $data['position']);
            $stmt->bindParam(':level', $data['level']);

            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            error_log("Error adding user: " . $e->getMessage());
            return false;
        }
    }

    // แก้ไขข้อมูลผู้ใช้
    public function updateUser($user_id, $data) {
        try {
            $sql = "UPDATE psd_users SET 
                fullname = :fullname,
                email = :email,
                telephone = :tel,
                dept_id = :dept_id,
                position = :position";

            // เพิ่มการอัพเดทรหัสผ่านถ้ามีการส่งมา
            if (!empty($data['password'])) {
                $sql .= ", pass_word = :password";
                $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            if (isset($data['level'])) {
                $sql .= ", level = :level";
            }

            $sql .= " WHERE user_id = :user_id";

            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':fullname', $data['fullname']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':tel', $data['telephone']);
            $stmt->bindParam(':dept_id', $data['dept_id']);
            $stmt->bindParam(':position', $data['position']);
            $stmt->bindParam(':user_id', $user_id);

            if (!empty($data['password'])) {
                $stmt->bindParam(':password', $hashed_password);
            }

            if (isset($data['level'])) {
                $stmt->bindParam(':level', $data['level']);
            }

            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    // ลบผู้ใช้
    public function deleteUser($user_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM psd_users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }

    // ดึงข้อมูลผู้ใช้ทั้งหมด
    public function getAllUsers() {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, d.dept_name 
                FROM psd_users u
                LEFT JOIN department d ON u.dept_id = d.dept_id
                ORDER BY u.user_id DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting users: " . $e->getMessage());
            return [];
        }
    }

    // ดึงข้อมูลผู้ใช้ตาม ID
    public function getUserById($user_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, d.dept_name 
                FROM psd_users u
                LEFT JOIN department d ON u.dept_id = d.dept_id
                WHERE u.user_id = :user_id
            ");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting user: " . $e->getMessage());
            return false;
        }
    }

    // ดึงข้อมูลช่างซ่อมทั้งหมด
    public function getTechnicians() {
        try {
            $stmt = $this->conn->prepare("
                SELECT user_id, fullname, dept_name
                FROM psd_users u
                LEFT JOIN department d ON u.dept_id = d.dept_id
                WHERE level = 'technician'
                ORDER BY fullname
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting technicians: " . $e->getMessage());
            return [];
        }
    }

    // ค้นหาผู้ใช้
    public function searchUsers($keyword) {
        try {
            $search = "%{$keyword}%";
            $stmt = $this->conn->prepare("
                SELECT u.*, d.dept_name 
                FROM psd_users u
                LEFT JOIN department d ON u.dept_id = d.dept_id
                WHERE u.fullname LIKE :keyword 
                OR u.user_name LIKE :keyword
                OR u.email LIKE :keyword
                OR d.dept_name LIKE :keyword
                ORDER BY u.user_id DESC
            ");
            $stmt->bindParam(':keyword', $search);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error searching users: " . $e->getMessage());
            return [];
        }
    }

    // เช็คชื่อผู้ใช้ซ้ำ
    public function isUsernameTaken($username, $exclude_id = null) {
        try {
            $sql = "SELECT COUNT(*) FROM psd_users WHERE user_name = :username";
            if ($exclude_id) {
                $sql .= " AND user_id != :exclude_id";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            if ($exclude_id) {
                $stmt->bindParam(':exclude_id', $exclude_id);
            }
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error checking username: " . $e->getMessage());
            return false;
        }
    }

    // เช็คอีเมลซ้ำ
    public function isEmailTaken($email, $exclude_id = null) {
        try {
            $sql = "SELECT COUNT(*) FROM psd_users WHERE email = :email";
            if ($exclude_id) {
                $sql .= " AND user_id != :exclude_id";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            if ($exclude_id) {
                $stmt->bindParam(':exclude_id', $exclude_id);
            }
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error checking email: " . $e->getMessage());
            return false;
        }
    }
}
?>