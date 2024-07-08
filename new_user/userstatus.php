<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./userstatus.css">
    <link rel="icon" href="../public/image.png" type="image/x-icon">
    <title>User Login</title>
</head>
<body>
    <div class="wrapper">
        <form id="loginForm" action="verify_user.php" method="POST">
            <h2>User Registration Status</h2>
            <div class="input-field">
                <input type="text" name="aadhaarNumber" id="aadhaarNumber" required>
                <label>Aadhaar card number</label>
            </div>
            <br>
            <button type="submit" class="button" id="login">Log In</button>
            <br>
            <a href="../index.html">Home</a>
            <div class="register">
                <p>Don't have an account? <a href="./newlogin.html">Register</a></p>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Ensure Aadhaar number input only accepts numbers
        document.getElementById('aadhaarNumber').addEventListener('input', function(event) {
            this.value = this.value.replace(/\D/g, '');
        });

        // Function to display user status and voter ID
        function displayUserStatus(status, voterId) {
            let message = status;
            if (voterId) {
                message += `\nVoter ID: ${voterId}`;
            }
            Swal.fire({
                title: "User Status",
                text: message,
                showCloseButton: true,
            });
        }

        <?php if (isset($_GET['status']) && isset($_GET['voter_id'])): ?>
            displayUserStatus('<?php echo htmlspecialchars($_GET['status']); ?>', '<?php echo htmlspecialchars($_GET['voter_id']); ?>');
        <?php elseif (isset($_GET['status'])): ?>
            displayUserStatus('<?php echo htmlspecialchars($_GET['status']); ?>');
        <?php endif; ?>
    </script>
</body>
</html>
