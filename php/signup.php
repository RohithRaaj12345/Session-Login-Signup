<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$database = new Database();
$conn = $database->getConnection();

$stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Please login.'
    ]);
} else {
    if (mysqli_errno($conn) == 1062) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conn)]);
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
