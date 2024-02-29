<?php
require_once("../classes/db_connect.php");
require_once("../classes/home.php");
require_once("../classes/users.php");
session_start();
$db = new Database();
$home = new Home($db->getConnection());
$user = new User($db->getConnection());

if (isset($_POST['logout'])) {
  $home->logoutUser();
}

if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
}

$user_id = $_SESSION['user_id'];
$userInfo = $home->getUserInfo($user_id);
$firstName = $userInfo['firstname'];
$lastName = $userInfo['lastname'];
$phonenumber = $userInfo['phonenumber'];
$address = $userInfo['address'];
$userEmail = $home->getUserEmail($user_id);

if (isset($_POST['update'])) {
  // Handle form submission for updating user details
  $updatedFirstName = $_POST['updatedFirstName'];
  $updatedLastName = $_POST['updatedLastName'];
  $updatedEmail = $_POST['updatedEmail'];
  $updatedAddress = $_POST['updatedAddress'];
  $updatedPhoneNumber = $_POST['updatedPhoneNumber'];
  $updatedPassword = $_POST['updatedPassword'];
  $confirmPassword = $_POST['confirmPassword'];

  // Call the updateUserDetails method from the Home class
  $home->updateUserDetails($user_id, $updatedFirstName, $updatedLastName, $updatedEmail, $updatedAddress, $updatedPhoneNumber, $updatedPassword, $_FILES['profileImage']);

  // Handle image upload to gallery
  if (!empty($_FILES['galleryImage']['name'][0])) {
    foreach ($_FILES['galleryImage']['name'] as $key => $value) {
      $image = array(
        'name' => $_FILES['galleryImage']['name'][$key],
        'tmp_name' => $_FILES['galleryImage']['tmp_name'][$key]
      );
      $title = $_POST['title'][$key] ?? "";
      $description = $_POST['description'][$key] ?? "";
      $home->uploadImageToGallery($user_id, $image, $title, $description);
    }
  }
}

if (isset($_POST['deleteAccount'])) {
  // Assuming your deleteAccount method returns true on success
  if ($home->deleteAccount($user_id)) {
    echo json_encode(array('status' => 'success'));
    exit;
  } else {
    echo json_encode(array('status' => 'error', 'message' => 'Failed to delete the account.'));
    exit;
  }
}
?>

<html>

<head>
  <title> Home </title>
  <link rel="stylesheet" href="../css/styles1.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../css/w3css.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <script src="home.js"></script>

  <link rel="icon" href="../images/usericon.png">
</head>

<body class="w3-content" style="max-width:1600px">

  <!-- Top Navigation Menu -->
  <div class="topnav" id="myTopnav">
    <a href="#accountsettings" class="active">Account Settings</a>
    <a href="home.php">Dashboard</a>
    <a href="socialio.php">About Socialio</a>
    
    <a href="javascript:void(0);" class="logout-link" onclick="logout()">Logout</a>
    <a href="javascript:void(0);" class="icon" onclick="myFunction()">
      <i class="fa fa-bars navbar-toggler"></i>
    </a>
  </div>

  <!-- Account Settings Form -->
  <center>
    <div id="account-settings" class="w3-container">
      <h2>Update Account</h2>
      <form action="accountsettings.php" method="post" enctype="multipart/form-data">
        <label for="profileImage">Profile Image:</label>
        <center>
          <div>
            <?php
            $imgFileName = $userInfo['img'];
            if (!empty($imgFileName)) {
              echo "<img src='../uploads/$imgFileName' alt='Profile Image' class = 'socials-image'>";
            } else {
              echo "<p>No profile image uploaded</p>";
            }
            ?>
          </div>
        </center>

        <div class="form-group">
          <label for="profileImage">Upload New Profile Image:</label>
          <div id="imagePreview"></div>
          <button class="buttonfile"><input type="file" name="profileImage" accept="image/*" onchange="previewImage(this)"></button>
          <br><a href="videocam.php">Capture via Video Cam</a>
        </div>



        <div class="form-group">
          <label for="updatedFirstName">First Name:</label>
          <input type="text" name="updatedFirstName" value="<?php echo $firstName; ?>" required>
        </div>

        <div class="form-group">
          <label for="updatedLastName">Last Name:</label>
          <input type="text" name="updatedLastName" value="<?php echo $lastName; ?>" required>
        </div>

        <div class="form-group">
          <label for="updatedEmail">Email:</label>
          <input type="email" name="updatedEmail" value="<?php echo $userEmail; ?>" required>
        </div>

        <div class="form-group">
          <label for="updatedAddress">Address:</label>
          <input type="text" name="updatedAddress" value="<?php echo $address; ?>">
        </div>

        <div class="form-group">
          <label for="updatedPhoneNumber">Phone Number:</label>
          <input type="text" name="updatedPhoneNumber" value="<?php echo $phonenumber; ?>">
        </div>

        <div class="form-group">
          <label for="updatedPassword">New Password:</label>
          <input type="password" name="updatedPassword">
        </div>

        <div class="form-group">
          <label for="confirmPassword">Confirm Password:</label>
          <input type="password" name="confirmPassword">
        </div>

        <div class="form-group">
          <label for="galleryImage">
            <h3>Upload to Gallery:</h3>
          </label>
          <button class="buttonfile"><input type="file" name="galleryImage[]" accept="image/*" multiple></button>
          <label for="title">Title:</label>

          <input type="text" name="title[]" placeholder="Enter title">
          <label for="description">Description:</label>
          <textarea class="textarea1" name="description[]" placeholder="Enter description"></textarea>
        </div>
        <button type="submit" name="update" class="update-button">Update Details</button>
      </form>

      <form id="deleteAccountForm" action="accountsettings.php" method="post">
        <button type="button" name="delete" class="delete-button" onclick="confirmDelete()">Delete Account</button>
      </form>

    </div>
  </center>

  <script>
    function openModal(imageSrc, details) {
      var modal = document.getElementById('imageModal');
      var previewImage = document.getElementById('previewImage');
      var imageDetails = document.getElementById('imageDetails');

      previewImage.src = imageSrc;
      imageDetails.innerHTML = details;
      modal.style.display = 'block';
    }

    function closeModal() {
      document.getElementById('imageModal').style.display = 'none';
    }

    function myFunction() {
      var x = document.getElementById("myLinks");
      if (x.style.display === "block") {
        x.style.display = "none";
      } else {
        x.style.display = "block";
      }
    }

    function logout() {
      var confirmation = confirm("Are you sure you want to logout?");
      if (confirmation) {
        // Call the logoutUser function from the Home class
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "home.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
          if (xhr.readyState == 4 && xhr.status == 200) {
            // Reload the page after successful logout
            window.location.reload();
          }
        };
        xhr.send("logout=logout");
      }
    }

    function myFunction() {
      var x = document.getElementById("myTopnav");
      if (x.className === "topnav") {
        x.className += " responsive";
      } else {
        x.className = "topnav";
      }
    }

    // In your JavaScript

    function confirmDelete() {
      var confirmation = confirm("Are you sure you want to delete your account? This action cannot be undone.");

      if (confirmation) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "accountsettings.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
          if (xhr.readyState == 4) {
            if (xhr.status == 200) {
              try {
                var response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                  alert("Account deleted successfully!");
                  // Redirect or perform additional actions as needed
                  window.location.href = 'login.php';
                } else {
                  console.log("Failed to delete the account. Server response:", response.message);
                }
              } catch (error) {
                console.log("Error parsing server response:", error);
                alert("Account deleted successfully!");
                window.location.href = 'login.php';
              }
            } else {
              console.log("Error occurred while processing the request. Status code:", xhr.status);

            }
          }
        };
        xhr.send("deleteAccount=deleteAccount"); // Change to match the PHP key
      }
    }

    function previewImage(input) {
      var preview = document.getElementById('imagePreview');

      // Clear previous preview
      preview.innerHTML = '';

      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          var img = document.createElement('img');
          img.setAttribute('src', e.target.result);
          img.setAttribute('alt', 'Profile Image');
          img.classList.add('socials-image');
          preview.appendChild(img);
        };

        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>

  <div style="display: none;">
    <form id="logout-form" action="home.php" method="post">
      <button class="submitbutton" type="submit" id="logout" name="logout" value="logout"></button>
    </form>
  </div>

  <button onclick="toggleDarkMode()" class="dark-mode-switch">
    <img id="darkModeIcon" src="../images/nightmode.png" alt="Dark Mode Icon" class="icon2">
  </button>

</body>

</html>