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

    $allKeys = $redis->keys('*'); 

    foreach ($allKeys as $key) {
        // $value = $redis->get($key); // Retrieve the value for each key
        // $userDatas = json_decode($value, true);
        // print_r($userDatas);
        if($key === $usertoken){
            echo json_encode([
                'success' => true,
                'message' => 'Session Valid',
            ]);
            exit();
        }
    }
    


?>