<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./userdashboard.css">
    <link rel="icon" href="../public/image.png" type="image/x-icon">
    <title>User Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    
    
    <div class="wrapper">
        <form id="vote-form" method="post" action="./hashgenerater.php">
          <h2>Vote</h2>
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

            // Get today's date
            $today_date = date('Y-m-d');

            // SQL query to fetch election details for today
            $sql_election = "SELECT election_name, election_id FROM elections WHERE election_date = '$today_date'";
            $result_election = $conn->query($sql_election);
    
            if ($result_election->num_rows > 0) {
                // Election found for today
                $row_election = $result_election->fetch_assoc();
                $election_name = $row_election['election_name'];
                $election_id = $row_election['election_id'];
                echo "<h3>Election Name : $election_name</h3>";
                echo "<h3>Election ID : $election_id</h3>";
            } else {
                // No election found for today
                echo "<h3>Today is not an election day</h3>";
            }
            $conn->close();
        ?>
          <div class="input-field">
            <input type="text" id="election_id" name="election_id" value="<?php echo isset($election_id) ? $election_id : ''; ?>" readonly>
            <label>Election ID</label>
          </div>
          <div class="input-field">
            <input type="text" id="voter_id" name="voter_id" required>
            <label>Voter ID</label>
          </div>
          <div class="input-field">
            <input type="password" id="password" name="password" required>
            <label>Password</label>
          </div>
          <br>
          <button type="submit" id="login" class="button">Submit</button>
          <br>
          <a href="../index.html">Home</a>
        </form>
    </div>
    
</body>
</html>
