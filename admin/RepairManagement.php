<?php
class RepairManagement {
    private $conn;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // แจ้งซ่อม
    public function createRepairTicket($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO psd_repair 
                (psd_id, repair_cause, user1, date_1, repair_status) 
                VALUES (:psd_id, :cause, :user_id, :date, 'waiting')");
            
            $stmt->bindParam(':psd_id', $data['psd_id']);
            $stmt->bindParam(':cause', $data['repair_cause']);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':date', $data['date_repair']);

            $stmt->execute();

            // อัพเดทสถานะพัสดุเป็น 'repair'
            $stmt = $this->conn->prepare("UPDATE psd_recieve SET psd_status = 'repair' WHERE psd_id = :psd_id");
            $stmt->bindParam(':psd_id', $data['psd_id']);
            $stmt->execute();

            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            return false;
        }
    }

    // อัพเดทสถานะการซ่อม
    public function updateRepairStatus($repair_id, $data) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("UPDATE psd_repair 
                SET repair_status = :status, 
                    repair_detail = :detail,
                    technician = :tech_id,
                    date_2 = CASE 
                        WHEN :status = 'completed' THEN CURRENT_DATE
                        ELSE date_2
                    END
                WHERE repair_id = :repair_id");
            
            $stmt->bindParam(':status', $data['repair_status']);
            $stmt->bindParam(':detail', $data['repair_detail']);
            $stmt->bindParam(':tech_id', $data['technician_id']);
            $stmt->bindParam(':repair_id', $repair_id);
            $stmt->execute();

            // อัพเดทสถานะพัสดุเมื่อซ่อมเสร็จ
            if ($data['repair_status'] == 'completed') {
                $stmt = $this->conn->prepare("
                    UPDATE psd_recieve r 
                    JOIN psd_repair pr ON r.psd_id = pr.psd_id
                    SET r.psd_status = 'active'
                    WHERE pr.repair_id = :repair_id");
                $stmt->bindParam(':repair_id', $repair_id);
                $stmt->execute();
            }

            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // ดึงข้อมูลการแจ้งซ่อมทั้งหมด
    public function getAllRepairs() {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, 
                    p.brand_name, p.models,
                    u1.fullname as reporter_name,
                    u2.fullname as technician_name,
                    d.dept_name
                FROM psd_repair r
                LEFT JOIN psd_recieve p ON r.psd_id = p.psd_id
                LEFT JOIN psd_users u1 ON r.user1 = u1.user_id
                LEFT JOIN psd_users u2 ON r.technician = u2.user_id
                LEFT JOIN department d ON p.dept_id = d.dept_id
                ORDER BY r.date_1 DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    // ดึงข้อมูลการแจ้งซ่อมตาม ID
    public function getRepairById($repair_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, 
                    p.brand_name, p.models,
                    u1.fullname as reporter_name,
                    u2.fullname as technician_name,
                    d.dept_name
                FROM psd_repair r
                LEFT JOIN psd_recieve p ON r.psd_id = p.psd_id
                LEFT JOIN psd_users u1 ON r.user1 = u1.user_id
                LEFT JOIN psd_users u2 ON r.technician = u2.user_id
                LEFT JOIN department d ON p.dept_id = d.dept_id
                WHERE r.repair_id = :repair_id
            ");
            $stmt->bindParam(':repair_id', $repair_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // ดึงข้อมูลการแจ้งซ่อมตามแผนก
    public function getRepairsByDepartment($dept_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, 
                    p.brand_name, p.models,
                    u1.fullname as reporter_name,
                    u2.fullname as technician_name,
                    d.dept_name
                FROM psd_repair r
                LEFT JOIN psd_recieve p ON r.psd_id = p.psd_id
                LEFT JOIN psd_users u1 ON r.user1 = u1.user_id
                LEFT JOIN psd_users u2 ON r.technician = u2.user_id
                LEFT JOIN department d ON p.dept_id = d.dept_id
                WHERE p.dept_id = :dept_id
                ORDER BY r.date_1 DESC
            ");
            $stmt->bindParam(':dept_id', $dept_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    // ดึงข้อมูลการแจ้งซ่อมตามผู้แจ้ง
    public function getRepairsByUser($user_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, 
                    p.brand_name, p.models,
                    u1.fullname as reporter_name,
                    u2.fullname as technician_name,
                    d.dept_name
                FROM psd_repair r
                LEFT JOIN psd_recieve p ON r.psd_id = p.psd_id
                LEFT JOIN psd_users u1 ON r.user1 = u1.user_id
                LEFT JOIN psd_users u2 ON r.technician = u2.user_id
                LEFT JOIN department d ON p.dept_id = d.dept_id
                WHERE r.user1 = :user_id
                ORDER BY r.date_1 DESC
            ");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    // ดึงสถิติการซ่อม
    public function getRepairStatistics() {
        try {
            $stats = [];
            
            // จำนวนการแจ้งซ่อมทั้งหมด
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM psd_repair");
            $stmt->execute();
            $stats['total_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // จำนวนการแจ้งซ่อมแยกตามสถานะ
            $stmt = $this->conn->prepare("
                SELECT repair_status, COUNT(*) as count 
                FROM psd_repair 
                GROUP BY repair_status
            ");
            $stmt->execute();
            $stats['status_counts'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            // จำนวนการแจ้งซ่อมแยกตามแผนก
            $stmt = $this->conn->prepare("
                SELECT d.dept_name, COUNT(r.repair_id) as count 
                FROM department d
                LEFT JOIN psd_recieve p ON d.dept_id = p.dept_id
                LEFT JOIN psd_repair r ON p.psd_id = r.psd_id
                GROUP BY d.dept_id, d.dept_name
            ");
            $stmt->execute();
            $stats['department_counts'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            // เวลาเฉลี่ยในการซ่อม (วัน)
            $stmt = $this->conn->prepare("
                SELECT AVG(DATEDIFF(date_2, date_1)) as avg_repair_time
                FROM psd_repair 
                WHERE repair_status = 'completed'
                AND date_2 IS NOT NULL
            ");
            $stmt->execute();
            $stats['avg_repair_time'] = round($stmt->fetch(PDO::FETCH_ASSOC)['avg_repair_time'], 1);
            
            return $stats;
        } catch(PDOException $e) {
            return [];
        }
    }
}
