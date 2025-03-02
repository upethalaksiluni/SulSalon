<?php
// Database connection parameters
$host = "localhost";
$dbname = "salon"; // Your database name
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password has no password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>