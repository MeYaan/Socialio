<?php
require_once("../classes/db_connect.php");
require_once("../classes/users.php");
session_start();
$db = new Database();
$user = new User($db->getConnection());

if (!empty($_SESSION['user_id'])) {
  header('Location: home.php');
  exit(); // Ensure that the script stops execution after the redirect
}

if (isset($_POST['reset'])) {
  $email = $_POST['email'];
  $newPassword = $_POST['new_password'];
  $confirmPassword = $_POST['confirm_password'];
  $user->resetPassword($email, $newPassword, $confirmPassword);
}
?>

<html>

<head>
  <title>Login Form</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="path/to/animation.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
  <style>
    .password-container {
      position: relative;
      margin-bottom: 20px;
    }

    #password {
      padding-right: 30px;
      /* Adjust the padding to make space for the icon */
    }

    #togglePassword {
      font-size: 25px;
      color: black;
      position: absolute;
      top: 68%;
      right: 10px;
      /* Adjust the right position as needed */
      cursor: pointer;
      transform: translateY(-50%);
    }
  </style>

  <link rel="icon" href="../images/usericon.png">
</head>

<body>

  <div class="login">

    <form action="forgetpassword.php" method="POST">
      <center><img src="../images/logowhite.png" alt="Logo" class="logo"></center>
      <h3>Forget Password</h3>

      <label for="email">Email</label>
      <input type="email" placeholder="Your Email" name="email" id="email" required>

      <label for="new_password">New Password</label>
      <input type="password" placeholder="New Password" name="new_password" id="new_password" required>

      <label for="confirm_password">Confirm Password</label>
      <input type="password" placeholder="Confirm Password" name="confirm_password" id="confirm_password" required>

      <button class="submitbutton" type="submit" id="reset" name="reset" value="Reset Password">Reset Password</button>
    </form>



    <script>
      const togglePassword = document.querySelector('#togglePassword');
      const password = document.querySelector('#password');
      togglePassword.addEventListener('click', () => {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        togglePassword.classList.toggle('bi-eye');
      });
    </script>

</body>

</html>