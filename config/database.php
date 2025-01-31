<?php
require_once 'configdb.php';

class Database {
    private $conn = null;

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host=" . DB_HOST . 
                    ";dbname=" . DB_NAME . 
                    ";charset=utf8",
                    DB_USER,
                    DB_PASS
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch(PDOException $e) {
                error_log("Connection Error: " . $e->getMessage());
                throw new Exception("ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
            }
        }
        return $this->conn;
    }
}
?>