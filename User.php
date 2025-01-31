<?
	// models/psd_users.php
class User {
    private $conn;
    private $table_name = "psd_users";

    public $user_id;
    public $user_name;
    public $pass_word;
    public $fullname;
    public $email;
    public $telephone;
    public $dept_id;
    public $level;
    public $position;
    public $reset_token;
    public $reset_expirs;
    public $created_at;
    public $department;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT id, username, position, department, email 
                 FROM " . $this->table_name . " 
                 WHERE username = ? AND password = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(2, $password_hash);
        $stmt->execute();
        return $stmt;
    }
}

?>