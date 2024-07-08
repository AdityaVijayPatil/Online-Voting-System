<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="votingmachine.css">
    <title>Vote Here</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>ONLINE VOTING MACHINE</h1>
        <div class="form-group">
            <label for="hashInput">Enter your hash:</label>
            <input type="text" id="hashInput" placeholder="Enter your hash">
        </div>
        <div class="form-group">
            <button onclick="verifyHash()">Verify</button>
        </div>

        <div id="electionSection" style="display: none;">
            <h1>Vote Here</h1>
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
            $sql_election = "SELECT election_name FROM elections WHERE election_date = '$today_date'";
            $result_election = $conn->query($sql_election);

            if ($result_election->num_rows > 0) {
                // Election found for today
                $row_election = $result_election->fetch_assoc();
                $election_name = $row_election['election_name'];
                echo "<h2>$election_name</h2>";
            } else {
                // No election found for today
                echo "<h2>Today is not an election day</h2>";
            }

            // SQL query to fetch members for today's election
            $sql_members = "SELECT ema.id, ema.member_logo, ema.member_name 
                            FROM election_member_and_all ema
                            INNER JOIN elections e ON ema.election_id = e.election_id
                            WHERE e.election_date = '$today_date'";
            $result_members = $conn->query($sql_members);

            if ($result_members->num_rows > 0) {
                // Output table headers
                echo "<table id='votingTable' style='display: none;'>";
                echo "<thead><tr><th>Logo</th><th>Member Name</th><th>Vote</th></tr></thead>";
                echo "<tbody>";
 
                // Output data of each row
                while($row = $result_members->fetch_assoc()) {
                    $logo_base64 = base64_encode($row["member_logo"]);
                    $logo_src = 'data:image/jpeg;base64,' . $logo_base64;
                    echo "<tr>
                            <td><img src='$logo_src' alt='Logo' width='50'></td>
                            <td>" . $row["member_name"] . "</td>
                            <td><button class='vote-button' onclick='vote(" . $row["id"] . ")'>Vote</button></td>
                        </tr>";
                }
                echo "</tbody></table>";
            } else {
                // No members found for today's election
                echo "<p>No members found for today's election.</p>";
            }

            $conn->close();
            ?>
        </div>

    </div>

    <script>
        function verifyHash() {
            var hashInput = document.getElementById('hashInput').value;
            $.ajax({
                url: 'verify_hash.php',
                type: 'post',
                data: { hash: hashInput },
                success: function(response) {
                    if (response.trim() === "valid") {
                        alert('Hash Code verified!');
                        document.getElementById('electionSection').style.display = 'block';
                        document.getElementById('votingTable').style.display = 'table';
                        localStorage.setItem('hash', hashInput); // Store the hash in local storage
                    } else {
                        alert('Invalid hash.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX error: ' + error);
                }
            });
        }

        function vote(memberId) {
            var hash = localStorage.getItem('hash'); // Retrieve the hash from local storage
            $.ajax({
                url: 'vote.php',
                type: 'post',
                data: { id: memberId, hash: hash },
                success: function(response) {
                    if (response.trim() === "success") {
                        alert('Vote submitted successfully!');
                        window.location.href = '../index.html';
                    } else {
                        alert('Error: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX error: ' + error);
                }
            });
        }
    </script>
</body>
</html>
