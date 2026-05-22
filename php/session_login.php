<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config/redis.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid Session Value While Login']);
    exit();
}

$redis_conn = new Redis_Connection();
$redis = $redis_conn->getClient();

$usertoken = trim($data['usertoken'] ?? '');

$user_data = $redis->get($usertoken);

if ($user_data) {
    echo json_encode([
        'success' => true,
        'message' => 'Session Valid',
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or expired session',
    ]);
}
?>
