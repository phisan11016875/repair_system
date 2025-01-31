<?php
        // classes/FSNManager.php
        class FSNManager {
            private $conn;
            private $db;

            public function __construct() {
                $this->db = new Database();
                $this->conn = $this->db->getConnection();
            }

            // เพิ่มกลุ่มพัสดุ
            public function addGroup($data) {
                try {
                    // ตรวจสอบข้อมูลซ้ำ
                    $stmt = $this->conn->prepare("SELECT group_id FROM fsn_groups WHERE group_id = :group_id");
                    $params = array(':group_id' => $data['group_id']); // แยก array parameter
                    $stmt->execute($params);
                    if ($stmt->fetch()) {
                        return false; // มีรหัสกลุ่มนี้แล้ว
                    }

                    $stmt = $this->conn->prepare("
                        INSERT INTO fsn_groups (
                            group_id, 
                            group_name, 
                            description, 
                            created_by
                        ) VALUES (
                            :group_id,
                            :group_name,
                            :description,
                            :created_by
                        )
                    ");

                    $params = array( // แยก array parameter
                        ':group_id' => $data['group_id'],
                        ':group_name' => $data['group_name'],
                        ':description' => $data['description'],
                        ':created_by' => $data['created_by']
                    );

                    return $stmt->execute($params);

                } catch(PDOException $e) {
                    error_log("Error adding FSN group: " . $e->getMessage());
                    return false;
                }
            }

            // เพิ่มประเภทพัสดุ
            public function addClass($data) {
                try {
                    // ตรวจสอบว่ามีกลุ่มพัสดุนี้อยู่จริง
                    $stmt = $this->conn->prepare("SELECT group_id FROM fsn_groups WHERE group_id = :group_id");
                    $params = array(':group_id' => $data['group_id']); // แยก array parameter
                    $stmt->execute($params);
                    if (!$stmt->fetch()) {
                        return false; // ไม่พบกลุ่มพัสดุ
                    }

                    // ตรวจสอบข้อมูลซ้ำ
                    $stmt = $this->conn->prepare("
                        SELECT class_id 
                        FROM fsn_classes 
                        WHERE group_id = :group_id AND class_id = :class_id
                    ");
                    $params = array( // แยก array parameter
                        ':group_id' => $data['group_id'],
                        ':class_id' => $data['class_id']
                    );
                    $stmt->execute($params);
                    if ($stmt->fetch()) {
                        return false; // มีรหัสประเภทนี้แล้ว
                    }

                    $stmt = $this->conn->prepare("
                        INSERT INTO fsn_classes (
                            group_id,
                            class_id,
                            class_name,
                            description,
                            created_by
                        ) VALUES (
                            :group_id,
                            :class_id,
                            :class_name,
                            :description,
                            :created_by
                        )
                    ");

                    $params = array( // แยก array parameter
                        ':group_id' => $data['group_id'],
                        ':class_id' => $data['class_id'],
                        ':class_name' => $data['class_name'],
                        ':description' => $data['description'],
                        ':created_by' => $data['created_by']
                    );

                    return $stmt->execute($params);

                } catch(PDOException $e) {
                    error_log("Error adding FSN class: " . $e->getMessage());
                    return false;
                }
            }

            // เพิ่มชนิดพัสดุ 
            public function addType($data) {
                try {
                    // ตรวจสอบข้อมูลซ้ำ
                    $stmt = $this->conn->prepare("
                        SELECT type_id 
                        FROM fsn_type 
                        WHERE group_class = :group_class AND type_id = :type_id
                    ");
                    $params = array( // แยก array parameter
                        ':group_class' => $data['group_class'],
                        ':type_id' => $data['type_id']
                    );
                    $stmt->execute($params);
                    if ($stmt->fetch()) {
                        return false; // มีรหัสชนิดนี้แล้ว
                    }

                    $stmt = $this->conn->prepare("
                        INSERT INTO fsn_type (
                            group_class,
                            type_id,
                            type_name,
                            description,
                            created_by
                        ) VALUES (
                            :group_class,
                            :type_id,
                            :type_name,
                            :description,
                            :created_by
                        )
                    ");

                    $params = array( // แยก array parameter
                        ':group_class' => $data['group_class'],
                        ':type_id' => $data['type_id'],
                        ':type_name' => $data['type_name'],
                        ':description' => $data['description'],
                        ':created_by' => $data['created_by']
                    );

                    return $stmt->execute($params);

                } catch(PDOException $e) {
                    error_log("Error adding FSN type: " . $e->getMessage());
                    return false;
                }
            }

            // ดึงข้อมูลกลุ่มพัสดุทั้งหมด
            public function getAllGroups() {
                try {
                    $stmt = $this->conn->prepare("
                        SELECT g.*, u.fullname as created_by_name
                        FROM fsn_groups g
                        LEFT JOIN psd_users u ON g.created_by = u.user_id
                        ORDER BY g.group_id
                    ");
                    $stmt->execute();
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch(PDOException $e) {
                    error_log("Error getting FSN groups: " . $e->getMessage());
                    return array();
                }
            }

            // ดึงข้อมูลประเภทพัสดุตามกลุ่ม

            public function getClassesByGroup($group_id) {
                try {
                    $stmt = $this->conn->prepare("
                        SELECT c.*, u.fullname as created_by_name
                        FROM fsn_classes c
                        LEFT JOIN psd_users u ON c.created_by = u.user_id
                        WHERE c.group_id = :group_id
                        ORDER BY c.class_id
                    ");
                    $params = array(':group_id' => $group_id); // แยก array parameter
                    $stmt->execute($params);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch(PDOException $e) {
                    error_log("Error getting FSN classes: " . $e->getMessage());
                    return array();
                }
            }

            // ดึงข้อมูลชนิดพัสดุตามกลุ่มและประเภท
            public function getAllClasses() {
            try {
                $stmt = $this->conn->prepare("
                    SELECT c.*, g.group_name, u.fullname as created_by_name
                    FROM fsn_classes c
                    LEFT JOIN fsn_groups g ON c.group_id = g.group_id
                    LEFT JOIN psd_users u ON c.created_by = u.user_id
                    ORDER BY c.group_id, c.class_id
                ");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                error_log("Error fetching classes: " . $e->getMessage());
                return array();
            }
        }
            public function getAllTypes() {
                try {
                    $stmt = $this->conn->prepare("
                        SELECT t.*, u.fullname as created_by_name
                        FROM fsn_type t
                        LEFT JOIN psd_users u ON t.created_by = u.user_id
                        ORDER BY t.group_class, t.type_id
                    ");
                    $stmt->execute();
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch(PDOException $e) {
                    error_log("Error fetching types: " . $e->getMessage());
                    return array();
                }
            }

            public function getTypesByGroupClass($group_class) {
                try {
                    $stmt = $this->conn->prepare("
                        SELECT t.*, u.fullname as created_by_name
                        FROM fsn_type t
                        LEFT JOIN psd_users u ON t.created_by = u.user_id
                        WHERE t.group_class = :group_class
                        ORDER BY t.type_id
                    ");
                    $stmt->bindParam(':group_class', $group_class);
                    $stmt->execute();
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch(PDOException $e) {
                    error_log("Error fetching types by group_class: " . $e->getMessage());
                    return array();
                }
            }

        }
?>