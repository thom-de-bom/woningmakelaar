<?php
include_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Prepare and execute the delete query
    $stmt = $pdo->prepare("DELETE FROM contact_submissions WHERE id = ?");
    $success = $stmt->execute([$id]);
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
}
