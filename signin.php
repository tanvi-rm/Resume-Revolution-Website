<?php
include 'config.php';
session_start();

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Match email and plain password
    $query = "SELECT * FROM registerationt WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Remove user_id since no id field exists
        // Store email and name in session instead
        $_SESSION['user_email'] = $user['email']; 
        $_SESSION['user_name'] = $user['name'];

        header("Location: homepage.php");
        exit();
    } else {
        $error_message = "Invalid email or password!";
    }

    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign In</title>
  <link rel="stylesheet" href="css/register.css" />
  <style>
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px;
      background-color: #4caf50;
      color: white;
      border-radius: 5px;
      display: none;
      z-index: 1000;
      transition: opacity 0.5s ease-in-out;
    }
    .notification.error {
      background-color: #f44336;
    }
    .notification.success {
      background-color: #4caf50;
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="logo">
      <img src="images/logo.png" alt="Logo" class="logo-image" />
      <span class="navbar-brand-text">Resume <span>Revolution</span></span>
    </div>
  </header>

  <!-- Notification -->
  <div id="notification" class="notification"></div>

  <div class="container">
    <div class="signup-card">
      <h1 class="title">Sign In</h1>
      <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your email" class="input-field" required />
        <input type="password" name="password" placeholder="Enter your password" class="input-field" required />
        <button type="submit" class="submit-button">Sign In</button>
      </form>
      <p class="signin-text">
        Don't have an account? <a href="signup.php" class="signin-link">Sign Up</a>
      </p>
    </div>
  </div>

  <script>
    // Show notification message
    function showNotification(message, type) {
      var notification = document.getElementById("notification");
      notification.innerHTML = message;
      notification.className = "notification " + type;
      notification.style.display = "block";

      setTimeout(function () {
        notification.style.opacity = "0";
        setTimeout(function () {
          notification.style.display = "none";
          notification.style.opacity = "1";
        }, 500);
      }, 3000);
    }
  </script>

  <?php if (!empty($error_message)) : ?>
    <script>
      window.onload = function () {
        showNotification(<?php echo json_encode($error_message); ?>, "error");
      };
    </script>
  <?php endif; ?>
</body>
</html>
