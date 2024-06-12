<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = $_POST['filter-name-or-id'];
    $filters = explode('-', $input);

    foreach ($filters as $filter) {
        $query = is_numeric($filter) ? "DELETE FROM filters WHERE id = :filter" : "DELETE FROM filters WHERE name = :filter";
        $statement = $pdo->prepare($query);
        $statement->bindValue(':filter', $filter);
        $statement->execute();
    }

    // Redirect with success message
    header('Location: admin.php?showFilters=true&success=Filters deleted');
} else {
    // Redirect with error message
    header('Location: admin.php?showFilters=true&error=Invalid request');
}
exit;
?>
