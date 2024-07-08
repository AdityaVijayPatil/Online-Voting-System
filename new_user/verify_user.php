<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "OnlineVotingSystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aadhaarNumber = $_POST['aadhaarNumber'];

    // Validate input
    if (!preg_match('/^\d{12}$/', $aadhaarNumber)) {
        header("Location: userstatus.php?status=Invalid Aadhaar number");
        exit();
    }

    // Prepare and bind
    $stmt = $conn->prepare("
        SELECT new_user.status, voters.voter_id 
        FROM new_user 
        LEFT JOIN voters ON new_user.useradhaarnumber = voters.useradhaarnumber 
        WHERE new_user.useradhaarnumber = ?
    ");
    $stmt->bind_param("s", $aadhaarNumber);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists and fetch the status and voter ID
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($status, $voterId);
        $stmt->fetch();
        if ($voterId) {
            header("Location: userstatus.php?status=" . urlencode($status) . "&voter_id=" . urlencode($voterId));
        } else {
            header("Location: userstatus.php?status=" . urlencode($status));
        }
    } else {
        header("Location: userstatus.php?status=Invalid Aadhaar number");
    }

    $stmt->close();
    $conn->close();
}
?>
