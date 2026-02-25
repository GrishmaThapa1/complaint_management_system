<?php
// Database connection 
$host = "localhost";      
$db_name = "complaint_management"; 
$username = "root";      
$password = "";           

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// set charset
$conn->set_charset("utf8");
