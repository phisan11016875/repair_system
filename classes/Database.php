<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'repair_system';
    private $username = 'root';
    private $password = '';
    private $conn = null;

    public function __construct() {
        try {
            // เพิ่ม options สำหรับ PDO
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::ATTR_PERSISTENT => true
            );

            // สร้างการเชื่อมต่อ
            $this->conn = new PDO(
                "mysql:host=" . $this->host . 
                ";dbname=" . $this->dbname,
                $this->username,
                $this->password,
                $options
            );

        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
        }
    }

    public function getConnection() {
        if ($this->conn === null) {
            throw new Exception("ไม่มีการเชื่อมต่อฐานข้อมูล");
        }
        return $this->conn;
    }
}
?>