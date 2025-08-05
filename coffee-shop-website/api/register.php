<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['email', 'username', 'birthDate', 'ssn', 'password'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        http_response_code(400);
        echo json_encode(['error' => ucfirst($field) . ' is required']);
        exit;
    }
}

$email = trim($input['email']);
$username = trim($input['username']);
$birthDate = trim($input['birthDate']);
$ssn = trim($input['ssn']);
$password = trim($input['password']);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// Validate SSN (4 digits)
if (!preg_match('/^\d{4}$/', $ssn)) {
    http_response_code(400);
    echo json_encode(['error' => 'SSN must be exactly 4 digits']);
    exit;
}

// Validate birth date
$date = DateTime::createFromFormat('Y-m-d', $birthDate);
if (!$date || $date->format('Y-m-d') !== $birthDate) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid birth date format']);
    exit;
}

// Password validation (minimum 6 characters)
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 6 characters long']);
    exit;
}

try {
    $db = getDB();
    
    // Check if email already exists
    $check_query = "SELECT id FROM users WHERE email = :email";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already registered']);
        exit;
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $insert_query = "INSERT INTO users (email, username, birth_date, ssn_last4, password_hash) 
                     VALUES (:email, :username, :birth_date, :ssn_last4, :password_hash)";
    
    $insert_stmt = $db->prepare($insert_query);
    $insert_stmt->bindParam(':email', $email);
    $insert_stmt->bindParam(':username', $username);
    $insert_stmt->bindParam(':birth_date', $birthDate);
    $insert_stmt->bindParam(':ssn_last4', $ssn);
    $insert_stmt->bindParam(':password_hash', $password_hash);
    
    if ($insert_stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to register user']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>