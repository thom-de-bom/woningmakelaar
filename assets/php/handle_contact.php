<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $volledige_naam = filter_var($_POST['volledige-naam'], FILTER_SANITIZE_SPECIAL_CHARS);
    $mobiel = filter_var($_POST['mobiel'], FILTER_SANITIZE_SPECIAL_CHARS);
    $bericht = filter_var($_POST['bericht'], FILTER_SANITIZE_SPECIAL_CHARS);

    // Capture the IP address of the user
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $stmt = $pdo->prepare("INSERT INTO contact_submissions (email, volledige_naam, mobiel, bericht, ip_address) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$email, $volledige_naam, $mobiel, $bericht, $ip_address])) {
        header("Location: ../../contact.php?status=success");
        exit();
    } else {
        header("Location: ../../contact.php?status=error");
        exit();
    }
}
?>
