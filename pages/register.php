<?php
require_once("../classes/db_connect.php");
require_once("../classes/users.php");
session_start();
$db = new Database();
$user = new User($db->getConnection());

if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $phonenumber = $_POST['phonenumber'];
    $address = $_POST['address'];

    $user->registerUser($email, $password, $confirmPassword, $firstname, $lastname, $phonenumber, $address);
}
?>

<head>
    <title> Register Page </title>
    <link rel="stylesheet" href="../css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="../images/usericon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="home.js"></script>
    <script>


    
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;

            // Password rules regex
            var passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!passwordRegex.test(password)) {
                alert("Password should have a minimum of 8 characters, at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.");
                return false;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return false;
            }

            return true;

            
        }


        
    </script>

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

    #toggleConfirmPassword {
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

    body.dark-mode #toggleConfirmPassword {
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
</head>

<body>
    <div class="register">
        <form action="register.php" method="post" onsubmit="return validatePassword()"  id="registrationForm">
            <center><img src="../images/logowhite.png" alt="Logo" class="logo"></center>
            <h3>Registration</h3>

            <label for="email">Email</label>
            <input type="text" name="email" id="email" required onblur="checkExistingEmail(this.value)"></br>
            <span id="emailExists" style=" display: none;">Email already exists. Please choose a different email.</span>

            <div class="password-container">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" minlength="8" required></input>
            <i class="bi bi-eye-slash" id="togglePassword"></i>
            </div>

            <div class="password-container">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" minlength="8" required></input>
            <i class="bi bi-eye-slash" id="toggleConfirmPassword"></i>
            </div>

            <label for="firstname">First Name</label>
            <input type="text" name="firstname" id="firstname" required></input></br>

            <label for="lastname">Last Name</label>
            <input type="text" name="lastname" id="lastname" required></input></br>

            <label for="phonenumber">Phone Number</label>
            <input type="text" name="phonenumber" id="phonenumber" maxlength="11" required></input></br>

            <label for="address">Address</label>
            <input type="text" name="address" id="address" required></input></br>

            <button class="submitbutton" type="submit" id="register" name="register" value="Register">Register</button>
            <center>
                <p>Already have an account? <b><a href="login.php">Login here!</a></p>
        </form>
    </div>

    <script>
    function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);

        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        toggleIcon.classList.toggle('bi-eye');
        toggleIcon.classList.toggle('bi-eye-slash');
    }

    document.getElementById('togglePassword').addEventListener('click', () => {
        togglePasswordVisibility('password', 'togglePassword');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', () => {
        togglePasswordVisibility('confirm_password', 'toggleConfirmPassword');
    });

    function checkExistingEmail(email) {
                    $.ajax({
                        type: 'POST',
                        url: 'check_email.php', // Create a new PHP file for checking email
                        data: { email: email },
                        success: function (response) {
                            if (response === 'exists') {
                                $('#emailExists').show();
                            } else {
                                $('#emailExists').hide();
                            }
                        }
                    });
                }

                function submitRegistration() {
                    if ($('#emailExists').is(':visible')) {
                        alert('Email already exists. Please choose a different email.');
                    } else {
                        // Proceed with form submission
                        document.getElementById('registrationForm').submit();
                    }
                }

</script>
<button onclick="toggleDarkMode()" class="dark-mode-switch">
  <img id="darkModeIcon" src="../images/nightmode.png" alt="Dark Mode Icon" class="icon2">
</button>

</body>
