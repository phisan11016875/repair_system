<?php
        class Logger {
            private $logPath;
            private $logFile;
            
            public function __construct($type = 'system') {
                // สร้างโฟลเดอร์ logs ถ้ายังไม่มี
                $this->logPath = dirname(__DIR__) . '/logs';
                if (!file_exists($this->logPath)) {
                    mkdir($this->logPath, 0777, true);
                }
                
                // กำหนดชื่อไฟล์ log ตามวันที่
                $this->logFile = $this->logPath . '/' . $type . '_' . date('Y-m-d') . '.log';
            }
            
            public function write($action, $description, $userId = null, $status = 'success') {
                $timestamp = date('Y-m-d H:i:s');
                $ipAddress = $_SERVER['REMOTE_ADDR'];
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                
                // สร้าง log entry
                $logEntry = sprintf(
                    "[%s] [%s] [IP: %s] [User: %s] [%s] %s\nUser Agent: %s\n",
                    $timestamp,
                    strtoupper($status),
                    $ipAddress,
                    $userId ?? 'Guest',
                    $action,
                    $description,
                    $userAgent
                );
                
                // เขียน log ลงไฟล์
                file_put_contents($this->logFile, $logEntry . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
            
            // เก็บ log การเข้าสู่ระบบ
            public function loginLog($userId, $username, $status = 'success', $message = '') {
                $action = 'LOGIN';
                $description = "User '$username' logged in. " . $message;
                $this->write($action, $description, $userId, $status);
            }
            
            // เก็บ log การออกจากระบบ
            public function logoutLog($userId, $username) {
                $action = 'LOGOUT';
                $description = "User '$username' logged out.";
                $this->write($action, $description, $userId);
            }
            
            // เก็บ log การแจ้งซ่อม
            public function repairLog($userId, $repairId, $action, $status = 'success') {
                $description = "Repair ticket #$repairId - $action";
                $this->write('REPAIR', $description, $userId, $status);
            }
            
            // เก็บ log การโยกย้ายพัสดุ
            public function moveLog($userId, $assetId, $fromDept, $toDept) {
                $description = "Asset #$assetId moved from $fromDept to $toDept";
                $this->write('ASSET_MOVE', $description, $userId);
            }
            
            // เก็บ log การจัดการผู้ใช้
            public function userLog($adminId, $action, $targetUser, $status = 'success') {
                $description = "$action performed on user '$targetUser'";
                $this->write('USER_MANAGEMENT', $description, $adminId, $status);
            }
            
            // เก็บ log การจัดการพัสดุ
            public function assetLog($userId, $action, $assetId, $details = '') {
                $description = "$action performed on asset #$assetId. $details";
                $this->write('ASSET_MANAGEMENT', $description, $userId);
            }
            
            // เก็บ log ข้อผิดพลาด
            public function errorLog($error, $userId = null) {
                $this->write('ERROR', $error, $userId, 'error');
            }
        }
?>
