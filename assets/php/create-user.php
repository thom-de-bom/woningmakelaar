<?php
include 'config.php'; // Database connection

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// Validate and sanitize inputs
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Username or password is missing.']);
    exit;
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Set the default role to "user"
$role = 'moderator';

// SQL query to insert new user with a role
$sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute(['username' => $username, 'password' => $hashedPassword, 'role' => $role]);

if ($success) {
    echo json_encode(['success' => true]);
    header("admin.php");
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to create user.']);
    header("admin.php");
}
?>
