<?php
class Database {
    private $host = "localhost";
    private $db_name = "guvitask";
    private $username = "root";
    private $password = "RohithRaaj@2005";
    private $conn;

    public function getConnection() {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->db_name);

        if (!$this->conn) {
            if (mysqli_connect_errno() === 1049) {
                $this->initDatabase();
                return $this->getConnection();
            }
            echo json_encode([
                'success' => false,
                'message' => 'Database connection failed: ' . mysqli_connect_error()
            ]);
            exit();
        }

        return $this->conn;
    }

    private function initDatabase() {
        mysqli_report(MYSQLI_REPORT_OFF);
        $conn = mysqli_connect($this->host, $this->username, $this->password);

        if (!$conn) {
            echo json_encode([
                'success' => false,
                'message' => 'Database initialization failed: ' . mysqli_connect_error()
            ]);
            exit();
        }

        mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "`");
        mysqli_select_db($conn, $this->db_name);
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        mysqli_close($conn);
    }
}
?>
