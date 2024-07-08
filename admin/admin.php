<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./admin.css">
    <link rel="icon" href="../public/image.png" type="image/x-icon">
    <title>Admin Login</title>
</head> 
<body>
      <div class="wrapper">
        <form id="loginForm" action="verify_admin.php" method="post">
          <h2>Admin</h2>
            <div class="input-field">
            <input type="text" id="email" name="email" required>
            <label>Enter your email</label>
          </div>
          <div class="input-field">
            <input type="password" id="password" name="password" required>
            <label>Enter your password</label>
          </div>
          <br>
          <button type="submit" id="login" class="button">Log In</button>
          <br>
          <a href="../index.html">Home</a>
        </form>
      </div>
      <script>
          // Handle errors (e.g., invalid credentials)
          <?php if (isset($_GET['error'])): ?>
              alert('Invalid email or password');
          <?php endif; ?>
      </script>
</body>
</html>
