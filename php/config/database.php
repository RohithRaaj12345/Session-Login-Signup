<?php
class Database {
    private $host = "localhost";
    private $db_name = "guvitask";
    private $username = "root";
    private $password = "RohithRaaj@2005";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $e) {
            if ($e->getCode() == 1049) {
                $this->initDatabase();
                return $this->getConnection();
            }
            echo json_encode([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ]);
            exit();
        }

        return $this->conn;
    }

    private function initDatabase() {
        try {
            $conn = new PDO(
                "mysql:host=" . $this->host,
                $this->username,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "`");
            $conn->exec("USE `" . $this->db_name . "`");
            $conn->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        } catch(PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database initialization failed: ' . $e->getMessage()
            ]);
            exit();
        }
    }
}
?>
