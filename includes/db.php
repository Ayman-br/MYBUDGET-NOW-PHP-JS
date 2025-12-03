<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "mybudget_now";  // Changed from mybudget_db to mybudget_now

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to test connection
function testDatabaseConnection() {
    global $conn;
    $result = mysqli_query($conn, "SELECT 1");
    if (!$result) {
        die("Database test failed: " . mysqli_error($conn));
    }
    return "✅ Database connection successful!";
}

// Uncomment to test:
// echo testDatabaseConnection();
?>