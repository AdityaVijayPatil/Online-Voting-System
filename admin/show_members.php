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
    $election_id = $_POST['election_id'];

    // Validate input
    if (!preg_match('/^\d{12}$/', $election_id)) {
        header("Location: admindashboard.php?status=Invalid Election ID");
        exit();
    }

    // Prepare and bind
    $stmt = $conn->prepare("SELECT voter_id, member_name FROM election_member_and_all WHERE election_id = ?");
    $stmt->bind_param("s", $election_id);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists and fetch the status
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($election_id);
        $stmt->fetch();
        header("Location: admindashboard.php?status=" . urlencode($election_id));
    } else {
        header("Location: admindashboard.php?status=Invalid Election ID");
    }

    $stmt->close();
    $conn->close();
}
?>
