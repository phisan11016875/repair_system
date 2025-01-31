<?php
class AssetManagement {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
    // ดึงข้อมูลพัสดุทั้งหมด
    public function getAllAssets() {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, d.dept_name 
                FROM psd_recieve r
                LEFT JOIN department d ON r.dept_id = d.dept_id
                ORDER BY r.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    // ดึงข้อมูลพัสดุตาม ID
    public function getAssetById($psd_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, d.dept_name 
                FROM psd_recieve r
                LEFT JOIN department d ON r.dept_id = d.dept_id
                WHERE r.psd_id = :id
            ");
            $stmt->bindParam(':id', $psd_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function getAssetsByDepartment($dept_id) {
    try {
        $stmt = $this->conn->prepare("
            SELECT r.*, d.dept_name 
            FROM psd_recieve r
            LEFT JOIN department d ON r.dept_id = d.dept_id
            WHERE r.dept_id = :dept_id
            ORDER BY r.date_recieve DESC
        ");
        $stmt->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error getting assets by department: " . $e->getMessage());
        return [];
    }
}
    // เพิ่มพัสดุ
    public function addAsset($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO psd_recieve 
                (brand_name, models, type_recieve, dept_id, psd_total, date_recieve, psd_status) 
                VALUES (:brand, :model, :type, :dept, :total, :date, :status)");
            
            $stmt->bindParam(':brand', $data['brand_name']);
            $stmt->bindParam(':model', $data['models']);
            $stmt->bindParam(':type', $data['type_recieve']);
            $stmt->bindParam(':dept', $data['dept_id']);
            $stmt->bindParam(':total', $data['psd_total']);
            $stmt->bindParam(':date', $data['date_recieve']);
            $stmt->bindParam(':status', $data['psd_status']);

            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // แก้ไขพัสดุ
    public function updateAsset($psd_id, $data) {
        try {
            $stmt = $this->conn->prepare("UPDATE psd_recieve SET 
                brand_name = :brand, 
                models = :model, 
                type_recieve = :type, 
                dept_id = :dept, 
                psd_total = :total, 
                date_recieve = :date, 
                psd_status = :status 
                WHERE psd_id = :id");
            
            $stmt->bindParam(':brand', $data['brand_name']);
            $stmt->bindParam(':model', $data['models']);
            $stmt->bindParam(':type', $data['type_recieve']);
            $stmt->bindParam(':dept', $data['dept_id']);
            $stmt->bindParam(':total', $data['psd_total']);
            $stmt->bindParam(':date', $data['date_recieve']);
            $stmt->bindParam(':status', $data['psd_status']);
            $stmt->bindParam(':id', $psd_id);

            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // ลบพัสดุ
    public function deleteAsset($psd_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM psd_recieve WHERE psd_id = :id");
            $stmt->bindParam(':id', $psd_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // โยกย้ายพัสดุ
    public function moveAsset($data) {
        try {
            $this->conn->beginTransaction();

            // บันทึกการโยกย้าย
            $stmt_move = $this->conn->prepare("INSERT INTO psd_move 
                (psd_id, deptid_old, deptid_new, move_cause, user_id, date_move) 
                VALUES (:psd, :old_dept, :new_dept, :cause, :user, :date)");
            
            $stmt_move->bindParam(':psd', $data['psd_id']);
            $stmt_move->bindParam(':old_dept', $data['deptid_old']);
            $stmt_move->bindParam(':new_dept', $data['deptid_new']);
            $stmt_move->bindParam(':cause', $data['move_cause']);
            $stmt_move->bindParam(':user', $data['user_id']);
            $stmt_move->bindParam(':date', $data['date_move']);
            $stmt_move->execute();

            // อัปเดตแผนก
            $stmt_update = $this->conn->prepare("UPDATE psd_recieve SET dept_id = :new_dept WHERE psd_id = :psd");
            $stmt_update->bindParam(':new_dept', $data['deptid_new']);
            $stmt_update->bindParam(':psd', $data['psd_id']);
            $stmt_update->execute();

            // บันทึก log
            $stmt_log = $this->conn->prepare("INSERT INTO psd_log 
                (user_id, psd_id, depid_old, depid_new, date_move) 
                VALUES (:user, :psd, :old_dept, :new_dept, :date)");
            
            $stmt_log->bindParam(':user', $data['user_id']);
            $stmt_log->bindParam(':psd', $data['psd_id']);
            $stmt_log->bindParam(':old_dept', $data['deptid_old']);
            $stmt_log->bindParam(':new_dept', $data['deptid_new']);
            $stmt_log->bindParam(':date', $data['date_move']);
            $stmt_log->execute();

            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // ค้นหาพัสดุ
    public function searchAssets($keyword) {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, d.dept_name 
                FROM psd_recieve r
                LEFT JOIN department d ON r.dept_id = d.dept_id
                WHERE r.brand_name LIKE :keyword 
                OR r.models LIKE :keyword 
                OR d.dept_name LIKE :keyword
                ORDER BY r.created_at DESC
            ");
            
            $search_keyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $search_keyword);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    // ดึงสถิติพัสดุ
    public function getAssetStatistics() {
    try {
        $stats = array(
            'total_assets' => 0,
            'status_counts' => array(
                'active' => 0,
                'repair' => 0,
                'inactive' => 0
            )
        );
        
        // จำนวนพัสดุทั้งหมด
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM psd_recieve");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_assets'] = isset($result['total']) ? $result['total'] : 0;
        
        // จำนวนพัสดุแยกตามสถานะ
        $stmt = $this->conn->prepare("
            SELECT psd_status, COUNT(*) as count 
            FROM psd_recieve 
            GROUP BY psd_status
        ");
        $stmt->execute();
        $status_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        if ($status_counts) {
            foreach ($status_counts as $status => $count) {
                if (isset($stats['status_counts'][$status])) {
                    $stats['status_counts'][$status] = (int)$count;
                }
            }
        }
        
        return $stats;
    } catch(PDOException $e) {
        error_log("AssetStatistics Error: " . $e->getMessage());
        return $stats; // ส่งค่าเริ่มต้นกลับไป
    }
}
}
?>