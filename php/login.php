<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config/database.php';
require_once 'config/redis.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$stmt = mysqli_prepare($conn, "SELECT id, username, email, password FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {
        $session_token = bin2hex(random_bytes(32));

        $redis_conn = new Redis_Connection();
        $redis = $redis_conn->getClient();

        $redis->setex($session_token, 86400, json_encode([
            'name' => $user['username'],
            'email' => $user['email']
        ]));

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'usertoken' => $session_token,
            'name' => $user['username']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
