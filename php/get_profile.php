<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config/redis.php';
require_once 'config/mongodb.php';

// Get token
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No token provided']);
    exit();
}

$usertoken = trim($data['usertoken'] ?? '');

// Verify token in Redis
$redis_conn = new Redis_Connection();
$redis = $redis_conn->getClient();

$user_data = $redis->get($usertoken);

if (!$user_data) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired session']);
    exit();
}

$user = json_decode($user_data, true);
$user_email = $user['email'];

// Get profile from MongoDB
$mongodb = new MongoDB_Connection();
$collection = $mongodb->getCollection();

try {
    $profile = $collection->findOne(['user_email' => $user_email]);
    
    if ($profile) {
        echo json_encode([
            'success' => true,
            'username' => $profile['user_name'] ?? '',
            'email' => $profile['user_email'] ?? '',
            'profile' => [
                'age' => $profile['age'] ?? '',
                'dob' => $profile['dob'] ?? '',
                'contact' => $profile['contact'] ?? '',
                'address' => $profile['address'] ?? '',
                'bio' => $profile['bio'] ?? ''
            ]
        ]);
    } else {
        // No profile exists yet
        echo json_encode([
            'success' => true,
            'username' => $user['username'],
            'email' => $user['email'],
            'profile' => [
                'age' => '',
                'dob' => '',
                'contact' => '',
                'address' => '',
                'bio' => ''
            ]
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>