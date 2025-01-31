<?php
class Department {
    private $conn;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    // ดึงข้อมูลแผนกทั้งหมด
    public function getAllDepartments() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM department ORDER BY dept_name");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting departments: " . $e->getMessage());
            return [];
        }
    }

    // ดึงข้อมูลแผนกตาม ID
    public function getDepartmentById($dept_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM department WHERE dept_id = :dept_id");
            $stmt->bindParam(':dept_id', $dept_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting department: " . $e->getMessage());
            return false;
        }
    }

    // เพิ่มแผนกใหม่
    public function addDepartment($dept_name) {
        try {
            // ตรวจสอบชื่อแผนกซ้ำ
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM department WHERE dept_name = :dept_name");
            $stmt->bindParam(':dept_name', $dept_name);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                return 'duplicate';
            }

            $stmt = $this->conn->prepare("INSERT INTO department (dept_name) VALUES (:dept_name)");
            $stmt->bindParam(':dept_name', $dept_name);
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            error_log("Error adding department: " . $e->getMessage());
            return false;
        }
    }

    // แก้ไขข้อมูลแผนก
    public function updateDepartment($dept_id, $dept_name) {
        try {
            // ตรวจสอบชื่อแผนกซ้ำ (ยกเว้นแผนกปัจจุบัน)
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) FROM department 
                WHERE dept_name = :dept_name AND dept_id != :dept_id
            ");
            $stmt->bindParam(':dept_name', $dept_name);
            $stmt->bindParam(':dept_id', $dept_id);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                return 'duplicate';
            }

            $stmt = $this->conn->prepare("
                UPDATE department 
                SET dept_name = :dept_name 
                WHERE dept_id = :dept_id
            ");
            $stmt->bindParam(':dept_name', $dept_name);
            $stmt->bindParam(':dept_id', $dept_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating department: " . $e->getMessage());
            return false;
        }
    }

    // ลบแผนก
    public function deleteDepartment($dept_id) {
        try {
            // ตรวจสอบว่ามีการใช้งานแผนกในตารางอื่นหรือไม่
            $tables = ['psd_users', 'psd_recieve'];
            foreach ($tables as $table) {
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM $table WHERE dept_id = :dept_id");
                $stmt->bindParam(':dept_id', $dept_id);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    return 'in_use';
                }
            }

            $stmt = $this->conn->prepare("DELETE FROM department WHERE dept_id = :dept_id");
            $stmt->bindParam(':dept_id', $dept_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error deleting department: " . $e->getMessage());
            return false;
        }
    }

    // ค้นหาแผนก
    public function searchDepartments($keyword) {
        try {
            $search = "%$keyword%";
            $stmt = $this->conn->prepare("
                SELECT * FROM department 
                WHERE dept_name LIKE :search 
                ORDER BY dept_name
            ");
            $stmt->bindParam(':search', $search);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error searching departments: " . $e->getMessage());
            return [];
        }
    }

    // นับจำนวนผู้ใช้ในแผนก
    public function countDepartmentUsers($dept_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) FROM psd_users 
                WHERE dept_id = :dept_id
            ");
            $stmt->bindParam(':dept_id', $dept_id);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch(PDOException $e) {
            error_log("Error counting department users: " . $e->getMessage());
            return 0;
        }
    }

    // นับจำนวนพัสดุในแผนก
    public function countDepartmentAssets($dept_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) FROM psd_recieve 
                WHERE dept_id = :dept_id
            ");
            $stmt->bindParam(':dept_id', $dept_id);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch(PDOException $e) {
            error_log("Error counting department assets: " . $e->getMessage());
            return 0;
        }
    }
}