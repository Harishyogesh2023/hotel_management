<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $email;
    public $first_name;
    public $last_name;
    public $phone;
    public $is_admin;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register new user
    public function register() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET username = :username, password = :password, email = :email, 
                      first_name = :first_name, last_name = :last_name, phone = :phone";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));

        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":phone", $this->phone);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Login user
    public function login() {
        $query = "SELECT id, username, password, email, first_name, last_name, phone, is_admin 
                  FROM " . $this->table_name . " 
                  WHERE username = :username OR email = :username";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $this->username);
        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Debug output: show what is retrieved from DB
            echo '<div style="color:purple; font-weight:bold;">DEBUG: User row from DB:<br>' . htmlspecialchars(json_encode($row)) . '</div>';
            // Allow both hashed and plain text passwords for development
            if(password_verify($this->password, $row['password']) || $this->password === $row['password']) {
                echo '<div style="color:green; font-weight:bold;">DEBUG: Password matched!</div>';
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->first_name = $row['first_name'];
                $this->last_name = $row['last_name'];
                $this->phone = $row['phone'];
                $this->is_admin = $row['is_admin'];
                return true;
            } else {
                echo '<div style="color:red; font-weight:bold;">DEBUG: Password did not match. Entered: ' . htmlspecialchars($this->password) . ' | DB: ' . htmlspecialchars($row['password']) . '</div>';
            }
        } else {
            echo '<div style="color:red; font-weight:bold;">DEBUG: No user found for username/email: ' . htmlspecialchars($this->username) . '</div>';
        }
        return false;
    }

    // Get user by ID
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Get all users (admin only)
    public function getAllUsers() {
        $query = "SELECT id, username, email, first_name, last_name, phone, is_admin, created_at 
                  FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>