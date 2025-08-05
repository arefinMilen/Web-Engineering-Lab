<?php
// Database Configuration
class Database {
    private $host = "localhost";
    private $db_name = "coffee_shop_db";
    private $username = "root";  // Default XAMPP username
    private $password = "";      // Default XAMPP password (empty)
    public $conn;

    // Get database connection
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

// Function to get database connection (helper function)
function getDB() {
    $database = new Database();
    return $database->getConnection();
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>