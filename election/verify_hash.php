<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinevotingsystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if hash is set
if (isset($_POST['hash'])) {
    $hash = $_POST['hash'];

    // SQL query to check if the hash exists in the hash table
    $sql = "SELECT COUNT(*) AS count FROM hash WHERE hash = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "Error preparing statement: " . $conn->error;
        $conn->close();
        exit();
    }

    $stmt->bind_param("s", $hash);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();

    if ($count > 0) {
        echo "valid";
    } else {
        echo "invalid";
    }

    $stmt->close();
} else {
    echo "No hash provided.";
}

$conn->close();
?>
