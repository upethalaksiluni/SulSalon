<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include "db_connect.php";

// Handle admin login
if (isset($_POST['action']) && $_POST['action'] == 'admin_login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = 'Please enter both username and password';
        header('Location: adminlogin.php');
        exit;
    }
    
    try {
        $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Direct check for the known credentials (for development/testing only)
        if ($username === 'Admin1' && $password === 'Admin@123') {
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_username'] = 'Admin1';
            header('Location: admindashboard.php');
            exit;
        } 
        else if ($username === 'Admin2' && $password === 'Admin@1234') {
            $_SESSION['admin_id'] = 2;
            $_SESSION['admin_username'] = 'Admin2';
            header('Location: admindashboard.php');
            exit;
        }
        // Try password_verify if the admin record was found
        else if ($admin && password_verify($password, $admin['password'])) {
            // Set session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Redirect to admin dashboard
            header('Location: admindashboard.php');
            exit;
        } 
        else {
            $_SESSION['error_message'] = 'Invalid username or password';
            header('Location: adminlogin.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Login failed: ' . $e->getMessage();
        header('Location: adminlogin.php');
        exit;
    }
}

// Handle admin logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Clear admin session data
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    
    // Redirect to admin login
    header('Location: adminlogin.php');
    exit;
}

// Redirect to admin login if accessed directly
header('Location: adminlogin.php');
exit;
?>