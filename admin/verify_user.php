<?php
include 'db_connection.php';
$conn = OpenCon();

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

// Update user status to verified
if ($conn->query("UPDATE new_user SET status='verified' WHERE id=$id") === TRUE) {
    // Fetch user details
    $result = $conn->query("SELECT * FROM new_user WHERE id=$id AND status='verified'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Generate a unique voter ID
        function generateVoterID($conn) {
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomLetters = substr(str_shuffle($letters), 0, 3);
            $randomNumbers = str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
            $voter_id = $randomLetters . $randomNumbers;
            
            // Ensure voter_id is unique
            $checkQuery = $conn->query("SELECT voter_id FROM voters WHERE voter_id='$voter_id'");
            while ($checkQuery->num_rows > 0) {
                $randomLetters = substr(str_shuffle($letters), 0, 3);
                $randomNumbers = str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
                $voter_id = $randomLetters . $randomNumbers;
                $checkQuery = $conn->query("SELECT voter_id FROM voters WHERE voter_id='$voter_id'");
            }
            return $voter_id;
        }

        $voter_id = generateVoterID($conn);
        
        // Insert user data into voters table
        $stmt = $conn->prepare("INSERT INTO voters (voter_id, name, useradhaarnumber, DOB, mobilenumber, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $voter_id, $user['username'], $user['useradhaarnumber'], $user['userDOB'], $user['usermobilenumber'], $user['userpassword']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'voter_id' => $voter_id]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found or not verified']);
    }
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

CloseCon($conn);
?>
