<?php
session_start();

// Database configuration - MODIFY THESE VALUES FOR YOUR DATABASE
$servername = "localhost";
$username = "root";          // Your MySQL username
$password = "";              // Your MySQL password
$dbname = "campus_fest_2025";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>