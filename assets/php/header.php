<?php 
// Check if the user is logged in via session or cookie
$isLoggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : isset($_COOKIE['loggedin']);

if ($isLoggedin) {
    echo '<a href="./assets/php/admin.php" class="user-btn">' . $_SESSION['username'] . '</a>';
} else {
    echo '<a href="login.php" class="login-btn">login</a>';
}
?>
