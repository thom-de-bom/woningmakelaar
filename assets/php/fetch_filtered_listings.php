<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include 'config.php';

function fetchListingsBasedOnFilters($pdo, $filters, $page, $perPage) {
    error_log('Received filters: ' . print_r($filters, true));

    $query = "SELECT * FROM listings";
    $parameters = [];
    $conditions = [];

    // Handle ligging and eigenschappen filters
    if (!empty($filters['ligging']) && is_array($filters['ligging'])) {
        $placeholders = array_map(function($key) { return ":ligging$key"; }, array_keys($filters['ligging']));
        $conditions[] = "ligging IN (" . implode(', ', $placeholders) . ")";
        foreach ($filters['ligging'] as $key => $value) {
            $parameters[":ligging$key"] = $value;
        }
    }

    if (!empty($filters['eigenschappen']) && is_array($filters['eigenschappen'])) {
        $placeholders = array_map(function($key) { return ":eigenschappen$key"; }, array_keys($filters['eigenschappen']));
        $conditions[] = "eigenschappen IN (" . implode(', ', $placeholders) . ")";
        foreach ($filters['eigenschappen'] as $key => $value) {
            $parameters[":eigenschappen$key"] = $value;
        }
    }

    // Additional filter for postcode
    if (!empty($filters['postcode'])) {
        $conditions[] = "postcode = :postcode";
        $parameters[':postcode'] = $filters['postcode'];
    }

    // Additional filter for maximum price
    if (!empty($filters['prijsmax'])) {
        $conditions[] = "prijs <= :prijsmax";
        $parameters[':prijsmax'] = $filters['prijsmax'];
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    $query .= " LIMIT :perPage OFFSET :offset";

    $stmt = $pdo->prepare($query);
    foreach ($parameters as $key => $value) {
        if (is_array($value)) {
            // Convert array values to a comma-separated string
            $value = implode(',', $value);
        }
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);

    $stmt->execute();
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countQuery = "SELECT COUNT(*) FROM listings";
    if (!empty($conditions)) {
        $countQuery .= " WHERE " . implode(' AND ', $conditions);
    }

    $countStmt = $pdo->prepare($countQuery);
    foreach ($parameters as $key => $value) {
        if (is_array($value)) {
            // Convert array values to a comma-separated string for the count query
            $value = implode(',', $value);
        }
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalFiltered = $countStmt->fetchColumn();

    return ['listings' => $listings, 'total' => $totalFiltered];
}

$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$perPage = 3;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $liggingFilters = isset($_POST['ligging']) ? $_POST['ligging'] : [];
    $eigenschappenFilters = isset($_POST['eigenschappen']) ? $_POST['eigenschappen'] : [];
    $postcode = isset($_POST['postcode']) ? $_POST['postcode'] : null;
    $prijsmax = isset($_POST['prijsmax']) ? $_POST['prijsmax'] : null;

    $filters = [
        'ligging' => $liggingFilters,
        'eigenschappen' => $eigenschappenFilters,
        'postcode' => $postcode,
        'prijsmax' => $prijsmax
    ];

    $result = fetchListingsBasedOnFilters($pdo, $filters, $page, $perPage);
    echo json_encode($result);
    exit;
}
?>
