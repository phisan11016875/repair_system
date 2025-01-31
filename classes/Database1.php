<?php
if (!class_exists('Database')) {
    class Database {
        private $host = "localhost";
        private $db_name = "repair_system";
        private $username = "root";
        private $password = "";
        private $conn = null;

        public function getConnection() {
            try {
                if ($this->conn === null) {
                    $this->conn = new PDO(
                        "mysql:host=" . $this->host . 
                        ";dbname=" . $this->db_name . 
                        ";charset=utf8",
                        $this->username,
                        $this->password
                    );
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                }
                return $this->conn;
            } catch(PDOException $e) {
                error_log("Connection Error: " . $e->getMessage());
                //throw new Exception("ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
            }
        }
    }
}
?>