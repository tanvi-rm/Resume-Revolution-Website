<?php
include 'config.php';
session_start();

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if email already exists
    $check_query = "SELECT * FROM registerationt WHERE email = '$email'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        $error_message = "Email already registered!";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        // Store plain text password (NOT RECOMMENDED FOR PRODUCTION)
        $insert_query = "INSERT INTO registerationt (name, email, password) VALUES ('$name', '$email', '$password')";
        if (mysqli_query($conn, $insert_query)) {
            $success_message = "Signup successful. You can now log in.";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="css/register.css">
  <style>
    /* Success message notification */
    .notification {
      background-color: #4CAF50; /* Green for success */
      color: white;
      padding: 15px;
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 8px;
      font-size: 16px;
      display: none;
      z-index: 999;
    }

    /* Error message notification */
    .error-notification {
      background-color: #f44336; /* Red for error */
      color: white;
      padding: 15px;
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 8px;
      font-size: 16px;
      display: none;
      z-index: 999;
    }
  </style>
</head>
<body>
  <!-- Display success message if available -->
  <?php if ($success_message != ""): ?>
    <div id="notification" class="notification"><?php echo $success_message; ?></div>
  <?php endif; ?>

  <!-- Display error message if available -->
  <?php if ($error_message != ""): ?>
    <div id="error-notification" class="error-notification"><?php echo $error_message; ?></div>
  <?php endif; ?>

  <header class="header">
    <div class="logo">
      <img src="images/logo.png" alt="Logo" class="logo-image">
      <span class = "navbar-brand-text">Resume <span>Revolution</span></span>
    </div>
  </header>
  <div class="container">
    <div class="signup-card">
      <h1 class="title">Sign Up</h1>
      <form method="POST" action="" onsubmit="return validateForm()">
        <input type="text" name="name" placeholder="Enter your name" class="input-field" required>
        <input type="email" name="email" placeholder="Enter your email" class="input-field" required>
        <input type="password" name="password" placeholder="Enter your password" class="input-field" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" class="input-field" required>
        <button type="submit" class="submit-button">Sign Up</button>
      </form>
      <p class="signin-text">
        Already have an account? <a href="signin.php" class="signin-link">Sign In</a>
      </p>
    </div>
  </div>

  <script>

    
    function validateForm() {
      var name = document.getElementById("name").value;
      var email = document.getElementById("email").value;
      var password = document.getElementById("password").value;
      var confirmPassword = document.getElementById("confirm_password").value;

      // Validate Name: It should not be empty and should contain only letters and spaces
      if (name == "" || !/^[a-zA-Z\s]+$/.test(name)) {
        alert("Name is required and must only contain letters and spaces.");
        return false;
      }

      // Validate Email: Check if the email format is correct
      var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
      if (email == "" || !emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return false;
      }

      // Validate Password: It should be at least 6 characters long
      if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        return false;
      }

      // Validate Password Confirmation: It should match the password
      if (password != confirmPassword) {
        alert("Passwords do not match.");
        return false;
      }

      return true;
    }

    // Display notifications for success and error
    window.onload = function() {
      var notification = document.getElementById('notification');
      var errorNotification = document.getElementById('error-notification');
      
      if (notification) {
        notification.style.display = 'block';
        setTimeout(function() {
          notification.style.display = 'none';
        }, 5000); // Hide after 5 seconds
      }

      if (errorNotification) {
        errorNotification.style.display = 'block';
        setTimeout(function() {
          errorNotification.style.display = 'none';
        }, 5000); // Hide after 5 seconds
      }
    }
  </script>

  <script src="js\app.js"></script>
  </body>
</html>
