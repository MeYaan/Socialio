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

if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $user->loginUser($email, $password);
}
?>

<html>

<head>
  <title>Login Form</title>
  <link rel="stylesheet" href="../css/styles.css">
  <script src="home.js"></script>
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

    body.dark-mode #togglePassword {
      font-size: 25px;
      color: black;
      position: absolute;
      top: 68%;
      right: 10px;
      background-color: lightgray;
      border-radius: 5px;
      /* Adjust the right position as needed */
      cursor: pointer;
      transform: translateY(-50%);
    }
  </style>

  <link rel="icon" href="../images/usericon.png">
</head>

<body>

  <div class="login">


    <form action="login.php" method="POST">

      <center><img src="../images/logowhite.png" alt="Logo" class="logo"></center>
      <h3>Login Here</h3>

      <label for="email">Email</label>
      <input type="text" placeholder="Email" name="email" id="email" required>

      <div class="password-container">
        <label for="password">Password</label>
        <input type="password" placeholder="Password" name="password" id="password" required>
        <i class="bi bi-eye-slash" id="togglePassword"></i>
      </div>

      <button class="submitbutton" type="submit" id="login" name="login" value="Log in">Login</button>

      <center>
        <p>Didn't have an account? <b><a href="register.php">Register here!</a> </p>
        <center><b><a href="forgetpassword.php">Forget Password</a>
    </form>
  </div>


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
<button onclick="toggleDarkMode()" class="dark-mode-switch">
  <img id="darkModeIcon" src="../images/nightmode.png" alt="Dark Mode Icon" class="icon2">
</button>

</html>