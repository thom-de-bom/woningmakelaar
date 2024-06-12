<?php
session_start();

// Destroy the session
session_destroy();

// Delete the loggedin cookie
if (isset($_COOKIE['loggedin'])) {
    setcookie('loggedin', '', time() - 3600, "/"); 
}

header('Location: ../../index.php'); // redirect to homepage
exit;
?>
