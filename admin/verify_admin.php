<?php
session_start();
include 'db_connection.php'; // Make sure to have a file to connect to your database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connect to the database
    $conn = OpenCon(); // Make sure you have a function OpenCon() in db_connection.php to establish DB connection

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare('SELECT id, name, email FROM admin WHERE email = ? AND a_password = ?');
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $email);
        $stmt->fetch();

        // Store data in session variables
        $_SESSION['admin_id'] = $id;
        $_SESSION['admin_name'] = $name;
        $_SESSION['admin_email'] = $email;

        // Redirect to the admin dashboard
        header('Location: admindashboard.php');
    } else {
        // Redirect back to login page with error message
        header('Location: adminlogin.php?error=1');
    }

    $stmt->close();
    CloseCon($conn); // Make sure you have a function CloseCon() in db_connection.php to close DB connection
}
?>
