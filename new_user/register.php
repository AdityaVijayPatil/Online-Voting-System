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
$name = $_POST['name'];
$aadhaarNumber = $_POST['aadhaarNumber'];
$dob = $_POST['dob'];
$mobileNumber = $_POST['mobileNumber'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt the password

// Validate Aadhaar number
if (!preg_match('/^\d{12}$/', $aadhaarNumber)) {
    die("Aadhaar number must be a 12-digit number");
}

// Validate mobile number
if (!preg_match('/^\d{10}$/', $mobileNumber)) {
    die("Mobile number must be a 10-digit number");
}

// Prepare SQL query
$sql = "INSERT INTO new_user (username, useradhaarnumber, userDOB, usermobilenumber, userpassword) 
        VALUES ('$name', '$aadhaarNumber', '$dob', '$mobileNumber', '$password')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Registration successful!'); window.location.href = 'userstatus.php';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
