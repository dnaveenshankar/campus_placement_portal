<?php
// Database connection parameters
$host = "localhost";        
$db_name = "CampusHire";    
$username = "root";        
$password = "";            

// Create a new connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
