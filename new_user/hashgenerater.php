<?php
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

// Fetch form data
$election_id = $_POST['election_id'];
$voter_id = $_POST['voter_id'];
$password = $_POST['password'];

// Validate input
// if (!preg_match('/^\d+$/', $election_id)) {
//     die("Election ID must be a numeric value");
// }

// if (!preg_match('/^\d+$/', $voter_id)) {
//     die("Voter ID must be a numeric value");
// }

// Generate a unique hash (4 to 6 digits)
$hash = rand(1000, 999999);

// Hash the hash for security purposes
// $hashed_hash = password_hash($hash, PASSWORD_BCRYPT);

// Prepare SQL query to check if the voter has already voted
$check_sql = "SELECT * FROM hash WHERE election_id = '$election_id' AND voter_id = '$voter_id'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    echo "<script>alert('You have already voted.'); window.location.href = 'userdashboard.php';</script>";
    exit;
}

// Prepare SQL query to insert data into the hash table
$sql = "INSERT INTO hash (hash, election_id, voter_id) VALUES ('$hash', '$election_id', '$voter_id')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Vote submitted successfully! Your hash is $hash'); window.location.href = '../election/votingmachine.php';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
