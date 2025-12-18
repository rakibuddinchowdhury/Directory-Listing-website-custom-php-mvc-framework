 <?php
require_once __DIR__ . '/../config/Database.php';

class Auth {
    private $conn;
    private $table = 'users';

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Register User
    public function register($name, $email, $password, $role = 'user') {
        // Check if email exists
        $checkQuery = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return "Email already exists.";
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert User
        $query = "INSERT INTO " . $this->table . " (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            return true;
        }
        return "Registration failed.";
    }

    // Login User
    public function login($email, $password) {
        $query = "SELECT id, name, password, role FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify Password
            if (password_verify($password, $row['password'])) {
                // Set Session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_role'] = $row['role'];
                return true;
            }
        }
        return false;
    }

    // Logout
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }

    // Check if Admin
    public function requireAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: ../login.php");
            exit;
        }
    }
}
?>
