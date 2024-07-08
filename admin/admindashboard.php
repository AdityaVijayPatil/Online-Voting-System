<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit();
}

include 'db_connection.php';
$conn = OpenCon();

// Fetch various counts
$total_voters = $conn->query("SELECT COUNT(*) AS total_voters FROM voters")->fetch_assoc()['total_voters'];
$total_requests = $conn->query("SELECT COUNT(*) AS total_requests FROM new_user WHERE status='pending'")->fetch_assoc()['total_requests'];
$total_elections = $conn->query("SELECT COUNT(*) AS total_elections FROM elections")->fetch_assoc()['total_elections'];
$total_votes = $conn->query("SELECT COUNT(*) AS total_votes FROM hash")->fetch_assoc()['total_votes'];

// Fetch recent voting information
$recent_voting_details = $conn->query("
    SELECT h.voting_date, h.hash, e.election_name 
    FROM hash h
    JOIN elections e ON h.election_id = e.election_id
    ORDER BY h.voting_date DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

// Fetch all election details
$election_details = $conn->query("SELECT election_id, election_name, election_date FROM elections ORDER BY election_date DESC")->fetch_all(MYSQLI_ASSOC);

// Fetch user information
$user_details = $conn->query("SELECT voter_id, name, useradhaarnumber, DOB, mobilenumber FROM voters")->fetch_all(MYSQLI_ASSOC);

// Fetch new user requests
$new_user_requests = $conn->query("SELECT id, username, useradhaarnumber, userDOB, usermobilenumber, status FROM new_user WHERE status='pending'")->fetch_all(MYSQLI_ASSOC);


// sample

// Fetch election details along with winner information
$election_results = $conn->query("
    SELECT e.election_id, e.election_name, e.election_date, v.member_name AS winner_name
    FROM elections e
    LEFT JOIN (
        SELECT election_id, member_name
        FROM (
            SELECT election_id, voter_id, member_name,
                   ROW_NUMBER() OVER (PARTITION BY election_id ORDER BY member_total_vote DESC) as rn
            FROM (
                SELECT election_id, voter_id, member_name, COUNT(*) AS member_total_vote
                FROM election_member_and_all
                GROUP BY election_id, voter_id, member_name
            ) as vote_counts
        ) as ranked_votes
        WHERE rn = 1
    ) v ON e.election_id = v.election_id
    ORDER BY e.election_date DESC
")->fetch_all(MYSQLI_ASSOC);



CloseCon($conn);

$admin_name = $_SESSION['admin_name'];


?>





<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="admindashboard.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="../public/image.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body> 
  <div class="sidebar">
    <div class="logo-details">
      <!-- <i class='bx bxl-c-plus-plus'></i> -->
      <span class="logo_name">Admin</span>
    </div>
    <ul class="nav-links">
      <li>
        <a href="#" id="dashboard-link" class="active">
          <i class='bx bx-grid-alt'></i>
          <span class="links_name">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="#" id="user-info-link">
          <i class='bx bx-box'></i>
          <span class="links_name">User Information</span>
        </a>
      </li>
      <li>
        <a href="#" id="newuser-info-link">
          <i class='bx bx-list-ul'></i>
          <span class="links_name">New User Request</span>
        </a>
      </li>
      <li>
          <a href="#" id="add-election-link">
            <i class='bx bx-plus'></i>
            <span class="links_name">Add Election</span>
          </a>
      </li>
      <li>
          <a href="#" id="result-link">
            <i class='bx bx-plus'></i>
            <span class="links_name">Result Authority</span>
          </a>
      </li>
      <li class="log_out">
        <a href="logout.php">
          <i class='bx bx-log-out'></i>
          <span class="links_name">Log out</span>
        </a>
      </li>
    </ul>
  </div>

  <!-- Dashboard -->
  <section class="home-section">
    <nav>
      <div class="sidebar-button">
        <i class='bx bx-menu sidebarBtn'></i>
        <span class="dashboard">ONLINE VOTING SYSTEM</span>
      </div>
      
      <div class="profile-details">
        <img src="../public/man.png" alt="">
        <span class="admin_name"><?php echo $admin_name; ?></span>
        <i class='bx bx-chevron-down'></i>
      </div>
    </nav>

    <div class="home-content" id="dashboard-content">
      <div class="overview-boxes">
        <div class="box">
          <div class="right-side">
            <div class="box-topic">Total Voters</div>
            <div class="number"><?php echo number_format($total_voters); ?></div>
          </div>
          <i class='bx bx-cart-alt cart'></i>
        </div>
        <div class="box">
          <div class="right-side">
            <div class="box-topic">New Registration Requests</div>
            <div class="number"><?php echo number_format($total_requests); ?></div>
          </div>
          <i class='bx bxs-cart-add cart two'></i>
        </div>
        <div class="box">
          <div class="right-side">
            <div class="box-topic">No. of Elections</div>
            <div class="number"><?php echo number_format($total_elections); ?></div>
          </div>
          <i class='bx bx-cart cart three'></i>
        </div>
        <div class="box">
          <div class="right-side">
            <div class="box-topic">No. of Votes</div>
            <div class="number"><?php echo number_format($total_votes); ?></div>
          </div>
          <i class='bx bxs-cart-download cart four'></i>
        </div>
      </div>

      <div class="sales-boxes">
        <div class="recent-sales box">
          <div class="title">Recent Voting</div>
          <div class="sales-details">
            <ul class="details">
              <li class="topic">Date</li>
              <?php foreach ($recent_voting_details as $voting): ?>
                <li><a href="#"><?php echo htmlspecialchars($voting['voting_date']); ?></a></li>
              <?php endforeach; ?>
            </ul>
            <ul class="details">
              <li class="topic">Voter Hash</li>
              <?php foreach ($recent_voting_details as $voting): ?>
                <li><a href="#"><?php echo htmlspecialchars($voting['hash']); ?></a></li>
              <?php endforeach; ?>
            </ul>
            <ul class="details">
              <li class="topic">Election</li>
              <?php foreach ($recent_voting_details as $voting): ?>
                <li><a href="#"><?php echo htmlspecialchars($voting['election_name']); ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="top-sales box">
          <div class="title">Elections</div>
          <ul class="top-sales-details">
            <?php foreach ($election_details as $election): ?>
              <li>
                <span class="product"><?php echo htmlspecialchars($election['election_name']); ?></span>
                <span class="price"><?php echo htmlspecialchars($election['election_date']); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

    <!-- User information Page -->
    <div class="home-content" id="user-info-content" style="display:none;">
      <div class="sales-boxes">
        <div class="recent-sales box">
          <div class="title">Voters Information</div>
          <div class="sales-details">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Adhaar Number</th>
                  <th>Date of Birth</th>
                  <th>Mobile number</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($user_details as $user): ?>
                <tr>
                  <td data-label="ID"><?php echo htmlspecialchars($user['voter_id']); ?></td>
                  <td data-label="Name"><?php echo htmlspecialchars($user['name']); ?></td>
                  <td data-label="Adhaar Number"><?php echo htmlspecialchars($user['useradhaarnumber']); ?></td>
                  <td data-label="Date of Birth"><?php echo htmlspecialchars($user['DOB']); ?></td>
                  <td data-label="Mobile number"><?php echo htmlspecialchars($user['mobilenumber']); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>


    <!-- New User Request -->
    <div class="home-content" id="newuser-info-content" style="display:none;">
      <div class="sales-boxes">
        <div class="recent-sales box">
          <div class="title">New User Request</div>
          <div class="sales-details">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Adhaar Number</th>
                  <th>Date of Birth</th>
                  <th>Mobile number</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($new_user_requests as $request): ?>
                <tr>
                  <td data-label="ID"><?php echo htmlspecialchars($request['id']); ?></td>
                  <td data-label="Name"><?php echo htmlspecialchars($request['username']); ?></td>
                  <td data-label="Adhaar Number"><?php echo htmlspecialchars($request['useradhaarnumber']); ?></td>
                  <td data-label="Date of Birth"><?php echo htmlspecialchars($request['userDOB']); ?></td>
                  <td data-label="Mobile number"><?php echo htmlspecialchars($request['usermobilenumber']); ?></td>
                  <td data-label="Action"><button class="verify-btn" data-id="<?php echo $request['id']; ?>">Verify</button></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Election Page -->
    <div class="home-content" id="add-election-content" style="display:none;">
      <div class="sales-boxes">
        <div class="recent-sales box">
          <div class="title">Add Election</div>
          <div class="sales-details">
            <table>
              <thead>
                <tr>
                  <th>Election ID</th>
                  <th>Election Name</th>
                  <th>Election Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($election_details as $election): ?>
                <tr>
                  <td><?php echo htmlspecialchars($election['election_id']); ?></td>
                  <td><?php echo htmlspecialchars($election['election_name']); ?></td>
                  <td><?php echo htmlspecialchars($election['election_date']); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div>
            <button id="show-add-election-form" class="addelection">Add Election</button>
            <button id="show-add-member-form" class="addelection">Add Member</button>
            <button id="show-show-member-form" class="addelection">Show Member</button>

            <form id="add-election-form" style="display:none;" method="post" action="./add_election.php">
              <label for="election_name">Election Name:</label>
              <input type="text" id="election_name" name="election_name" required>
              <label for="election_date">Election Date:</label>
              <input type="date" id="election_date" name="election_date" required>
              <button type="submit">Submit</button>
            </form>
 
            <form id="add-member-form" style="display:none;" method="post" action="./add_member.php" enctype="multipart/form-data">
                <label for="member_name" class="label">Member Name:</label>
                <input type="text" id="member_name" class="input" name="member_name" required><br>
                <label for="voter_id" class="label">Voter ID:</label>     
                <input type="text" id="voter_id" class="input" name="voter_id" required><br>
                <label for="election_id" class="label">Election ID:</label>
                <input type="text" id="election_id" class="input" name="election_id" required><br>
                <label for="logo" class="label">Election Logo:</label>
                <input type="file" id="logo" class="input" name="logo" accept="image/*" required><br>
                <button type="submit">Add Member</button>
            </form>


            <form id="show-member-form" style="display:none;" action="show_member.php" method="post">
                <label for="election_id" class="label">Election ID:</label>
                <input type="text" id="election_id" class="input" name="election_id" required><br>
                <button type="submit" id="show-show-member-details" class="addelection">Show Member</button>
            </form>


            <div class="sales-details" style="display:none;" id="show-member-details">
              <table>
                <thead>
                  <tr>
                    <th>Voter ID</th>
                    <th>Member Name</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><?php echo htmlspecialchars($election['voter_id']); ?></td>
                    <td><?php echo htmlspecialchars($election['member_name']); ?></td>
                  </tr>
                </tbody>
              </table>
            </div>


          </div>
        </div>
      </div>
    </div>


    <!-- Result Authority page -->


    <div class="home-content" id="result-content" style="display:none;">
      <div class="sales-boxes">
        <div class="recent-sales box">
          <div class="title">Election Results</div>
          <div class="sales-details">
            <table>
              <thead>
                <tr>
                  <th>Election ID</th>
                  <th>Election Name</th>
                  <th>Winner</th>
                  <th>Election Date</th>
                </tr>
              </thead>
              <tbody>
                  <?php foreach ($election_results as $election): ?>
                  <tr>
                      <td data-label="Election id"><?php echo htmlspecialchars($election['election_id']); ?></td>
                      <td data-label="Election Name"><?php echo htmlspecialchars($election['election_name']); ?></td>
                      <td data-label="Winner"><?php echo htmlspecialchars($election['winner_name']); ?></td>
                      <td data-label="Election Date"><?php echo htmlspecialchars($election['election_date']); ?></td>
                  </tr>
                  <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <button id="show-add-result-form" class="addelection">Publish Result</button>
          <form id="add-result-form" style="display:none;" action="./publish_result.php" method="post">
                <label for="election_id" class="label">Election ID:</label>
                <input type="text" id="election_id" class="input" name="election_id" required><br>
                <button type="submit" class="addelection">Publish</button>
          </form>
        </div>
      </div>
    </div>




  </section>

  <script>
    let sidebar = document.querySelector(".sidebar");
    let sidebarBtn = document.querySelector(".sidebarBtn");
    let dashboardLink = document.getElementById("dashboard-link");
    let userInfoLink = document.getElementById("user-info-link");
    let newuserInfoLink = document.getElementById("newuser-info-link");
    let addElectionLink = document.getElementById("add-election-link");
    let resultLink = document.getElementById("result-link");
    let dashboardContent = document.getElementById("dashboard-content");
    let userInfoContent = document.getElementById("user-info-content");
    let newuserInfoContent = document.getElementById("newuser-info-content");
    let addElectionContent = document.getElementById("add-election-content");
    let resultContent = document.getElementById('result-content');

    sidebarBtn.onclick = function() {
      sidebar.classList.toggle("active");
      if (sidebar.classList.contains("active")) {
        sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
      } else {
        sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
      }
    };

    userInfoLink.onclick = function(event) {
      event.preventDefault();
      dashboardContent.style.display = "none";
      userInfoContent.style.display = "block";
      newuserInfoContent.style.display = "none";
      addElectionContent.style.display = "none";
      resultContent.style.display = 'none';
      userInfoLink.classList.add("active");
      newuserInfoLink.classList.remove("active");
      dashboardLink.classList.remove("active");
      addElectionLink.classList.remove("active");
      resultLink.classList.remove("active");
    };

    newuserInfoLink.onclick = function(event) {
      event.preventDefault();
      dashboardContent.style.display = "none";
      userInfoContent.style.display = "none";
      newuserInfoContent.style.display = "block";
      addElectionContent.style.display = "none";
      resultContent.style.display = 'none';
      userInfoLink.classList.remove("active");
      newuserInfoLink.classList.add("active");
      dashboardLink.classList.remove("active");
      addElectionLink.classList.remove("active");
      resultLink.classList.remove("active");
    };

    addElectionLink.onclick = function(event) {
      event.preventDefault();
      dashboardContent.style.display = "none";
      userInfoContent.style.display = "none";
      newuserInfoContent.style.display = "none";
      addElectionContent.style.display = "block";
      resultContent.style.display = 'none';
      dashboardLink.classList.remove("active");
      userInfoLink.classList.remove("active");
      newuserInfoLink.classList.remove("active");
      addElectionLink.classList.add("active");
      resultLink.classList.remove("active");
    };

    resultLink.onclick = function(event) {
      event.preventDefault();
      dashboardContent.style.display = "none";
      userInfoContent.style.display = "none";
      newuserInfoContent.style.display = "none";
      addElectionContent.style.display = "none";
      resultContent.style.display = 'block';
      dashboardLink.classList.remove("active");
      userInfoLink.classList.remove("active");
      newuserInfoLink.classList.remove("active");
      addElectionLink.classList.remove("active");
      resultLink.classList.add("active");
    };

    dashboardLink.onclick = function(event) {
      event.preventDefault();
      dashboardContent.style.display = "block";
      userInfoContent.style.display = "none";
      newuserInfoContent.style.display = "none";
      addElectionContent.style.display = "none";
      resultContent.style.display = "none";
      dashboardLink.classList.add("active");
      newuserInfoLink.classList.remove("active");
      userInfoLink.classList.remove("active");
      addElectionLink.classList.remove("active");
      resultLink.classList.remove("active");
    };


    // verify button functionality for admin to verify user
    document.querySelectorAll('.verify-btn').forEach(button => {
      button.onclick = function() {
        const userId = this.getAttribute('data-id');
        // AJAX request to update user status
        fetch('verify_user.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ id: userId })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('User verified successfully!');
            location.reload();
          } else {
            alert('Error verifying user.');
          }
        });
      };
    });

    document.getElementById("show-add-election-form").onclick = function() {
      document.getElementById("add-election-form").style.display = "block";
      document.getElementById("add-member-form").style.display = "none";
      document.getElementById("show-member-form").style.display = "none";
      document.getElementById("show-member-details").style.display = "none";
    };

    document.getElementById("show-add-member-form").onclick = function() {
      document.getElementById("add-member-form").style.display = "block";
      document.getElementById("add-election-form").style.display = "none";
      document.getElementById("show-member-form").style.display = "none";
      document.getElementById("show-member-details").style.display = "none";
    };

    document.getElementById("show-show-member-form").onclick = function() {
      document.getElementById("add-member-form").style.display = "none";
      document.getElementById("add-election-form").style.display = "none";
      document.getElementById("show-member-details").style.display = "none";
      document.getElementById("show-member-form").style.display = "block";
    };

    document.getElementById("show-show-member-details").onclick = function() {
      document.getElementById("add-member-form").style.display = "none";
      document.getElementById("add-election-form").style.display = "none";
      document.getElementById("show-member-details").style.display = "block";
      document.getElementById("show-member-form").style.display = "none";
    };

    document.getElementById("show-add-result-form").onclick = function() {
      document.getElementById("add-result-form").style.display = "block";
    };


  </script>
</body>
</html>
