<?php
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

// SQL query to fetch members with voter ID from the latest election
$sql = "SELECT id, member_logo, member_name, member_total_vote as votes 
        FROM election_member_and_all 
        WHERE election_id = (SELECT election_id FROM publish_result ORDER BY id DESC LIMIT 1)";
 
$result = $conn->query($sql);

$candidates = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $logo_base64 = base64_encode($row["member_logo"]);
        $logo_src = 'data:image/jpeg;base64,' . $logo_base64;
        $candidates[] = array(
            'id' => $row['id'],
            'logo' => $logo_src,
            'name' => $row['member_name'],
            'votes' => $row['votes']
        );
    }
}

$conn->close();

// Return the candidates array as JSON
header('Content-Type: application/json');
echo json_encode($candidates);
?>
