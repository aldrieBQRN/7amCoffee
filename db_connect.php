<?php
// Database connection settings
$host = "localhost";      
$user = "root";           
$pass = "";               
$dbname = "7am_coffee_db";     

// $host = "sql100.infinityfree.com";      
// $user = "if0_40595780";           
// $pass = "7amcoffee";               
// $dbname = "if0_40595780_7am_coffee_db";   


// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>