<?php
// db_connect.php - Database connection
$servername = "localhost";
$username = "root";  // Default XAMPP
$password = "";      // Default XAMPP
$dbname = "expedia_flight_booking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>