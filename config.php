<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'rsoa_rsoa0278_1');
define('DB_USER', 'rsoa_rsoa0278_1');
define('DB_PASS', '654321#');

// Create connection
function getDBConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("set names utf8");
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Global functions
function redirect($url) {
    echo "<script>window.location.href = '$url';</script>";
    exit;
}
?>
