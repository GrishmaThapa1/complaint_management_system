<?php
// Database connection settings
$host = "localhost";      // usually localhost
$db_name = "complaint_management"; // your database name
$username = "root";       // your MySQL username
$password = "";           // your MySQL password

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8");
