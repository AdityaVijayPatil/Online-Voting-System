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

// Check if ID is set
if (isset($_POST['id']) && isset($_POST['hash'])) {
    $memberId = $_POST['id'];
    $hash = $_POST['hash'];

    // SQL query to increment the vote count
    $sql = "UPDATE election_member_and_all SET member_total_vote = member_total_vote + 1 WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "Error preparing statement: " . $conn->error;
        $conn->close();
        exit();
    }

    $stmt->bind_param("i", $memberId);

    if ($stmt->execute()) {
        // Generate a new unique hash
        $newHash = bin2hex(random_bytes(16));
        
        // SQL query to update the hash value
        $updateHashSql = "UPDATE hash SET hash = ? WHERE hash = ?";
        $updateHashStmt = $conn->prepare($updateHashSql);
        if ($updateHashStmt === false) {
            echo "Error preparing hash update statement: " . $conn->error;
            $conn->close();
            exit();
        }

        $updateHashStmt->bind_param("ss", $newHash, $hash);
        if ($updateHashStmt->execute()) {
            echo "success";
        } else {
            echo "Error updating hash: " . $updateHashStmt->error;
        }

        $updateHashStmt->close();
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No member ID or hash provided.";
}

$conn->close();
?>
