<?php
$servername = "localhost"; // Replace with your servername
$username = "aftabsaad"; // Replace with your username
$password = "saadaftab122"; // Replace with your password
$dbname = "pharmacy"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
