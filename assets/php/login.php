<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require 'config.php'; // Include the database connection

$error = ''; // Initialize error variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($user = $stmt->fetch()) {
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Set a cookie that the user is logged in. This will last for 30 days.
            setcookie("loggedin", true, time() + (86400 * 30), "/"); // 86400 = 1 day

            header('Location: admin.php');
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}

// Somewhere in your HTML (or above the login form)
if($error): ?>
    <div class="error-message"><?php echo $error; ?></div>
<?php endif; ?>
