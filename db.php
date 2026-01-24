<?php
$host = "localhost";
$user = "root";  // XAMPP er default user
$pass = "";      // XAMPP er default password empty thake
$dbname = "ecommerce_db"; // Database er naam jeta phpMyAdmin e banayecho
$port = 3307;
// Connection create kora
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Connection check kora
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Session start kore dilam, jate sob page e user login track kora jay
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>