<?php
include 'db_connection.php';
$conn = OpenCon();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $election_id = $_POST['election_id'];
    // Insert new result
    $stmt = $conn->prepare("INSERT INTO publish_result (election_id) VALUES (?)");
    $stmt->bind_param("s", $election_id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Result Publish successfully');
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
