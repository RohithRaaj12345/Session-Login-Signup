<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config/database.php';
require_once 'config/redis.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

// Validation
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Database connection
$database = new Database();
$conn = $database->getConnection();

try {
    // Prepared statement to get user
    $query = "SELECT id, username, email, password FROM users WHERE username = :username LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Generate session token
            $session_token = bin2hex(random_bytes(32));
            
            // Store in Redis
            $redis_conn = new Redis_Connection();
            $redis = $redis_conn->getClient();
            
            // Store user data in Redis (expires in 24 hours)
            $redis->setex($session_token, 86400, json_encode([
                'username' => $user['username'],
                'email' => $user['email']
            ]));
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'usertoken' => $session_token,
                'username' => $user['username']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>