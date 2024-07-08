<?php
include 'db_connection.php';
$conn = OpenCon();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $election_name = $_POST['election_name'];
    $election_date = $_POST['election_date'];

    // Generate new election ID
    $result = $conn->query("SELECT MAX(id) AS max_id FROM elections");
    $row = $result->fetch_assoc();
    $max_id = $row['max_id'] + 1;
    $election_id = "ELEC" . str_pad($max_id, 3, "0", STR_PAD_LEFT);

    // Insert new election
    $stmt = $conn->prepare("INSERT INTO elections (election_id, election_name, election_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $election_id, $election_name, $election_date);

    if ($stmt->execute()) {
        echo "<script>
            alert('New election added successfully');
            window.location.href = './admindashboard.php';
        </script>";
    } else {
        echo "<script>
            alert('Error: " . addslashes($stmt->error) . "');
            window.location.href = './admindashboard.php';
        </script>";
    }

    $stmt->close();
}

CloseCon($conn);
?>
