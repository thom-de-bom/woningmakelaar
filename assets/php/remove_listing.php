<?php
include 'config.php'; // Database connection

$listingId = $_GET['id'] ?? 0;
// Perform deletion logic here
$pdo->prepare("DELETE FROM listings WHERE id = ?")->execute([$listingId]);

?>
