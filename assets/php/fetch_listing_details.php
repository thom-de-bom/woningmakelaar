<?php
include_once "config.php"; // Include your database configuration

function getFilterNames($pdo, $filterIds, $filterType) {
    if (empty($filterIds)) return [];
    
    $placeholders = str_repeat('?,', count($filterIds) - 1) . '?';
    $stmt = $pdo->prepare("SELECT name FROM filters WHERE id IN ($placeholders) AND `option` = ?");
    $stmt->execute(array_merge($filterIds, [$filterType]));
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    // Convert ligging and eigenschappen from comma-separated strings to arrays
    $liggingIds = $listing['ligging'] ? explode(',', $listing['ligging']) : [];
    $eigenschappenIds = $listing['eigenschappen'] ? explode(',', $listing['eigenschappen']) : [];

    // Get filter names by their IDs
    $listing['ligging'] = getFilterNames($pdo, $liggingIds, 'ligging');
    $listing['eigenschappen'] = getFilterNames($pdo, $eigenschappenIds, 'eigenschappen');

    // Convert to JSON and output
    echo json_encode($listing);
}

?>
