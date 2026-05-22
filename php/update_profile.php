<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config/redis.php';
require_once 'config/mongodb.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No token provided']);
    exit();
}

$usertoken = trim($data['usertoken'] ?? '');

$redis_conn = new Redis_Connection();
$redis = $redis_conn->getClient();

$user_data = $redis->get($usertoken);

if (!$user_data) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired session']);
    exit();
}

$user = json_decode($user_data, true);
$user_email = $user['email'];
$user_name = $user['name'];

$age = trim($data['age'] ?? '');
$dob = trim($data['dob'] ?? '');
$contact = trim($data['contact'] ?? '');
$address = trim($data['address'] ?? '');
$bio = trim($data['bio'] ?? '');

$mongodb = new MongoDB_Connection();
$manager = $mongodb->getManager();
$namespace = $mongodb->getDatabase() . '.' . $mongodb->getCollection();

try {
    $bulk = new MongoDB\Driver\BulkWrite;

    $bulk->update(
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
        ['multi' => false, 'upsert' => true]
    );

    $manager->executeBulkWrite($namespace, $bulk);

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
