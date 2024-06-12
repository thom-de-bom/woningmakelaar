<?php
include 'config.php'; // Database connection

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'] ?? '';

// Validate input
if (empty($userId)) {
    echo json_encode(['success' => false, 'error' => 'User ID is missing.']);
    exit;
}

// SQL query to delete user
$sql = "DELETE FROM users WHERE id = :userId";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute(['userId' => $userId]);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete user.']);
}
