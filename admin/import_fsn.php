<?php
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

class FSNImporter {
    private $conn;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    public function importFromExcel($file) {
        try {
            if (PHP_VERSION_ID < 50600) {
                throw new Exception('Requires PHP 5.6 or higher');
            }
            
            require_once dirname(__FILE__) . '/../vendor/autoload.php';
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file['tmp_name']);
            $spreadsheet = $reader->load($file['tmp_name']);
            
            $this->importGroups($spreadsheet->getSheet(0));
            $this->importClasses($spreadsheet->getSheet(1)); 
            $this->importTypes($spreadsheet->getSheet(2));
            
            return true;
        } catch (Exception $e) {
            error_log('Import error: ' . $e->getMessage());
            return false;
        }
    }
    
            private function getRowData($worksheet, $row, $range) {
                $cells = $worksheet->rangeToArray($range . $row->getRowIndex(), null, true, false);
                return isset($cells[0]) ? array_map('trim', $cells[0]) : array();
            }
            public function getConnection() {
            if (!$this->conn) {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                    $this->username,
                    $this->password,
                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                );
            }
            return $this->conn;
        }
}
<?php
require_once 'FSNImporter.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    $importer = new FSNImporter();
    if ($importer->importFromExcel($_FILES['excel_file'])) {
        $success = "นำเข้าข้อมูลสำเร็จ";
    } else {
        $error = "เกิดข้อผิดพลาดในการนำเข้าข้อมูล";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>เลือกไฟล์ Excel</label>
        <input type="file" name="excel_file" accept=".xlsx" required>
    </div>
    <button type="submit" class="btn btn-primary">นำเข้าข้อมูล</button>
</form>
?>
