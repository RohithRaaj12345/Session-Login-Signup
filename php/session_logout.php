<?php
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    require_once 'config/redis.php';

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid Session Value While Logingout']);
        exit();
    }

    $redis_conn = new Redis_Connection();
    $redis = $redis_conn->getClient();

    $usertoken = trim($data['usertoken'] ?? '');
    $redis->del($usertoken);
    echo json_encode([
        'success' => true,
        'message' => 'Session Logged Out Successfully',
    ]);
?>