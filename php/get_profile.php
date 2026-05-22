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

$mongodb = new MongoDB_Connection();
$manager = $mongodb->getManager();
$namespace = $mongodb->getDatabase() . '.' . $mongodb->getCollection();

try {
    $query = new MongoDB\Driver\Query(['user_email' => $user_email]);
    $cursor = $manager->executeQuery($namespace, $query);
    $documents = iterator_to_array($cursor);

    if (!empty($documents)) {
        $profile = (array) $documents[0];

        // Convert BSON types to string
        $age = isset($profile['age']) ? (string) $profile['age'] : '';
        $dob = isset($profile['dob']) ? (string) $profile['dob'] : '';
        $contact = isset($profile['contact']) ? (string) $profile['contact'] : '';
        $address = isset($profile['address']) ? (string) $profile['address'] : '';
        $bio = isset($profile['bio']) ? (string) $profile['bio'] : '';

        echo json_encode([
            'success' => true,
            'name' => isset($profile['user_name']) ? (string) $profile['user_name'] : '',
            'email' => isset($profile['user_email']) ? (string) $profile['user_email'] : '',
            'profile' => [
                'age' => $age,
                'dob' => $dob,
                'contact' => $contact,
                'address' => $address,
                'bio' => $bio
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'name' => $user['name'],
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
