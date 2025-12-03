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
$user_name = $user['username'];

$age = trim($data['age'] ?? '');
$dob = trim($data['dob'] ?? '');
$contact = trim($data['contact'] ?? '');
$address = trim($data['address'] ?? '');
$bio = trim($data['bio'] ?? '');

// Update profile in MongoDB
$mongodb = new MongoDB_Connection();
$collection = $mongodb->getCollection();

try {
    $result = $collection->updateOne(
        ['user_email' => $user_email],
        ['$set' => [
            'user_name' => $user_name,
            'user_email' => $user_email,
            'age' => $age,
            'dob' => $dob,
            'contact' => $contact,
            'address' => $address,
            'bio' => $bio,
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]],
        ['upsert' => true]
    );
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>