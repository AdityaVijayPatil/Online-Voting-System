<?php

// Database connection parameters
$serverName = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "onlinevotingsystem"; // Ensure the database name is correct and case-sensitive

// Establish a connection to the database
$conn = mysqli_connect($serverName, $dbUsername, $dbPassword, $dbName);

// Check if the connection was successful
if ($conn) {
    echo "Connection to the database was successful!";
} else {
    echo "Connection failed: " . mysqli_connect_error();
}

// Close the database connection
if ($conn) {
    mysqli_close($conn);
}
?>
