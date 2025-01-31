<?php
class Logger {
    private $logFile;

    public function __construct() {
        $this->logFile = __DIR__ . '/../logs/app.log';
        
        // สร้างโฟลเดอร์ logs ถ้ายังไม่มี
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0755, true);
        }
    }

    public static function log($message, $level = 'info') {
        $logFile = __DIR__ . '/../logs/app.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    public function write($action, $description, $userId = null, $status = 'success') {
        $timestamp = date('Y-m-d H:i:s');
        $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
        
        // สร้าง log entry
        $logEntry = sprintf(
            "[%s] [%s] [IP: %s] [User: %s] [%s] %s\nUser Agent: %s\n",
            $timestamp,
            strtoupper($status),
            $ipAddress,
            $userId ? $userId : 'Guest',
            $action,
            $description,
            $userAgent
        );
        
        // เขียน log ลงไฟล์
        file_put_contents($this->logFile, $logEntry . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    public function loginLog($userId, $username, $status = 'success', $message = '') {
        $action = 'LOGIN';
        $description = "User '$username' logged in. " . $message;
        $this->write($action, $description, $userId, $status);
    }
    
    public function logoutLog($userId, $username) {
        $action = 'LOGOUT';
        $description = "User '$username' logged out.";
        $this->write($action, $description, $userId);
    }
    
    public function repairLog($userId, $repairId, $action, $status = 'success') {
        $description = "Repair ticket #$repairId - $action";
        $this->write('REPAIR', $description, $userId, $status);
    }
    
    public function moveLog($userId, $assetId, $fromDept, $toDept) {
        $description = "Asset #$assetId moved from $fromDept to $toDept";
        $this->write('ASSET_MOVE', $description, $userId);
    }
    
    public function userLog($adminId, $action, $targetUser, $status = 'success') {
        $description = "$action performed on user '$targetUser'";
        $this->write('USER_MANAGEMENT', $description, $adminId, $status);
    }
    
    public function assetLog($userId, $action, $assetId, $details = '') {
        $description = "$action performed on asset #$assetId. $details";
        $this->write('ASSET_MANAGEMENT', $description, $userId);
    }
    
    public function errorLog($error, $userId = null) {
        $this->write('ERROR', $error, $userId, 'error');
    }
}
?>