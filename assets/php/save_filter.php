<?php
include "config.php";

$response = [];

if(isset($_POST['filter-name']) && !empty($_POST['filter-name'])) {
    $filterNames = explode('-', $_POST['filter-name']);
    $stmt = $pdo->prepare("INSERT INTO filters (name, option) VALUES (:name, :option)");

    foreach ($filterNames as $filterName) {
        $filterName = trim($filterName);
        $filterType = isset($_POST['filter-option']) ? $_POST['filter-option'] : 'default_type';

        $stmt->execute(['name' => $filterName, 'option' => $filterType]);
    }

    // Redirect with success message
    header('Location: admin.php?showFilters=true&success=Filters added');
} else {
    // Redirect with error message
    header('Location: admin.php?showFilters=true&error=No filter name provided');
}
exit;
?>
