<?php
session_start();
include 'config.php';  // Make sure this connects to your DB

if (!isset($_SESSION['user_name'])) {
    header("Location: signin.php");
    exit();
}

$user_name = $_SESSION['user_name'];

// Handle saving phone or designation to DB
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['phone'])) {
        $phone = trim($_POST['phone']);
        $stmt = $conn->prepare("UPDATE registerationt SET phone = ? WHERE name = ?");
        $stmt->bind_param("ss", $phone, $user_name);
        $stmt->execute();
        $stmt->close();
    }
    if (isset($_POST['designation'])) {
        $designation = trim($_POST['designation']);
        $stmt = $conn->prepare("UPDATE registerationt SET designation = ? WHERE name = ?");
        $stmt->bind_param("ss", $designation, $user_name);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch email, phone and designation from database
$email = $phone = $designation = '';
$stmt = $conn->prepare("SELECT email, phone, designation FROM registerationt WHERE name = ?");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$stmt->bind_result($email, $phone, $designation);
$stmt->fetch();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Profile</title>
  <link rel="stylesheet" href="css/profile.css" />
</head>

<script>
  function previewProfileImage() {
    const input = document.getElementById('profile-upload');
    const preview = document.getElementById('previewImage');
    const file = input.files[0];

    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }

  function editDesignation() {
    const p = document.getElementById('designation-text');
    const input = document.getElementById('profile-role-input');
    
    input.value = p.textContent;
    p.style.display = 'none';
    input.style.display = 'block';
    input.focus();
  }

  function saveDesignation() {
  const p = document.getElementById('designation-text');
  const input = document.getElementById('profile-role-input');
  const designation = input.value.trim();

  // Send designation to server
  const formData = new FormData();
  formData.append('designation', designation);

  fetch('', {
    method: 'POST',
    body: formData
  }).then(() => {
    if(designation === '') {
      input.value = p.textContent;
    } else {
      p.textContent = designation;
    }
    input.style.display = 'none';
    p.style.display = 'block';
  });
}

  function editPhone() {
    const span = document.getElementById('phone-text');
    const input = document.getElementById('phone-input');

    input.value = span.textContent === 'Click to enter phone number' ? '' : span.textContent;
    span.style.display = 'none';
    input.style.display = 'inline';
    input.focus();
  }

  function savePhone() {
    const span = document.getElementById('phone-text');
    const input = document.getElementById('phone-input');
    const phone = input.value.trim();

    // Send value to server via POST
    const formData = new FormData();
    formData.append('phone', phone);

    fetch('', {
      method: 'POST',
      body: formData
    }).then(() => {
      span.textContent = phone || 'Click to enter phone number';
      input.style.display = 'none';
      span.style.display = 'inline';
    });
  }

  function checkPhoneEnter(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      event.target.blur();  // triggers savePhone()
    }
  }


</script>

<body>

  <header class="header1">
    <div class="logo">
      <img src="images/logo.png" alt="Logo" class="logo-image">
      <span class="navbar-brand-text">Resume <span>Revolution</span></span>
    </div>
  </header>

  <div class="profile-container">
    <div class="profile-left">
      <form action="upload.php" method="post" enctype="multipart/form-data">
        <div class="profile-pic-wrapper">
          <img src="images/profile.png" alt="Profile Picture" class="profile-pic" id="previewImage">
          <label for="profile-upload" class="upload-icon">+</label>
          <input type="file" name="profile" id="profile-upload" accept="image/*" onchange="previewProfileImage()">
        </div>
        <h2 class="profile-name"><?php echo htmlspecialchars($user_name); ?></h2>
        <div class="profile-divider"></div>
        <p id="designation-text" class="profile-role" onclick="editDesignation()">
  <?php echo $designation ? htmlspecialchars($designation) : 'Add designation'; ?>
</p>
<input 
  type="text" 
  id="profile-role-input" 
  name="designation" 
  class="profile-role-input" 
  style="display:none;" 
  value="<?php echo htmlspecialchars($designation); ?>"
  onblur="saveDesignation()" 
  onkeydown="checkEnter(event)"
/>

      </form>
    </div>

    <div class="profile-right">
  <form method="POST">
    <p style="margin-bottom: 15px;"><strong>Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
    <p style="margin-bottom: 15px;"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p style="margin-bottom: 25px;"><strong>Phone:</strong>
  <span id="phone-text" onclick="editPhone()" style="cursor:pointer;">
    <?php echo $phone ? htmlspecialchars($phone) : 'Click to enter phone number'; ?>
  </span>
  <input type="text" name="phone" id="phone-input" value="<?php echo htmlspecialchars($phone); ?>" style="display:none;" onblur="savePhone()" onkeydown="checkPhoneEnter(event)" />
</p>

  </form>
  
  <form action="logout.php" method="post" style="margin-top: 10px;">
    <button class="logout-button">Logout</button>
  </form>
</div>

  </div>

</body>
</html>
