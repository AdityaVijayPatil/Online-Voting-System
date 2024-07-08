<?php
include 'db_connection.php';
$conn = OpenCon();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_name = $_POST['member_name'];
    $voter_id = $_POST['voter_id'];
    $election_id = $_POST['election_id'];

    // Check if a file was uploaded without errors
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        // Check if the file type is an image
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['logo']['name'];
        $filetype = $_FILES['logo']['type'];
        $filesize = $_FILES['logo']['size'];
        
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)) {
            die("Error: Please select a valid file format.");
        }

        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            die("Error: File size is larger than the allowed limit.");
        }

        // Read the image file into a binary string
        $logo = file_get_contents($_FILES['logo']['tmp_name']);

        // Prepare the SQL statement to check if the member is a valid voter
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM voters WHERE voter_id = ? AND name = ?");
        $check_stmt->bind_param("ss", $voter_id, $member_name);
        $check_stmt->execute();
        $check_stmt->bind_result($voter_count);
        $check_stmt->fetch();
        $check_stmt->close();

        // Prepare the SQL statement to check if the election ID is valid
        $check_stmt1 = $conn->prepare("SELECT COUNT(*) FROM elections WHERE election_id = ?");
        $check_stmt1->bind_param("s", $election_id);
        $check_stmt1->execute();
        $check_stmt1->bind_result($election_count);
        $check_stmt1->fetch();
        $check_stmt1->close();

        if ($voter_count > 0 && $election_count > 0) {
            // Member is a valid voter and election ID is valid, proceed to add to election_member_and_all
            $stmt = $conn->prepare("INSERT INTO election_member_and_all (member_name, voter_id, election_id, member_total_vote, member_logo) VALUES (?, ?, ?, ?, ?)");
            $member_total_vote = 0;
            $stmt->bind_param("sssis", $member_name, $voter_id, $election_id, $member_total_vote, $logo);

            if ($stmt->execute()) {
                echo "<script>
                    alert('Member added successfully.');
                    window.location.href = 'admindashboard.php';
                </script>";
            } else {
                echo "<script>
                    alert('Error: " . addslashes($stmt->error) . "');
                    window.location.href = 'admindashboard.php';
                </script>";
            }

            $stmt->close();
        } else {
            // Member is not a valid voter or election ID is invalid
            echo "<script>
                alert('Error: Member name and voter ID do not match any voter or invalid election ID.');
                window.location.href = 'admindashboard.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Error: File upload error.');
            window.location.href = 'admindashboard.php';
        </script>";
    }

    $conn->close();
} else {
    echo "<script>
        alert('Invalid request method.');
        window.location.href = 'admindashboard.php';
    </script>";
}
?>
