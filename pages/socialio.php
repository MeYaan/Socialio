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
}

?>

<html>

<head>
    <title> Home </title>
    <link rel="stylesheet" href="../css/styles1.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="home.js"></script>
    <link rel="icon" href="../images/usericon.png">
</head>

<body class="w3-content" style="max-width:1600px">

    <!-- Top Navigation Menu -->
    <div class="topnav" id="myTopnav">

        <a href="#sociolio" class="active">About Sociolio</a>
        <a href="home.php">Dashboard</a>
        <a href="accountsettings.php">Account Settings</a>
        <a href="javascript:void(0);" class="logout-link" onclick="logout()">Logout</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars navbar-toggler"></i>
        </a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="logo">
            <img src="../images/logowhite.png" alt="Socialio Logo">
        </div>
        <h1>Welcome to Socialio</h1>
        <p>Socialio is your all-in-one platform that seamlessly combines social media and portfolio management. It's designed to provide you with a unified space for showcasing your creativity, connecting with others, and managing your online presence.</p>
        <p>Our mission is to empower individuals, artists, and professionals by offering a versatile platform where you can express yourself, share your achievements, and build meaningful connections.</p>
        <!-- Add more details as needed -->
    </div>

    <script>
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