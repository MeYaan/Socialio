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

    <style>
        #container {
            margin: 0px auto;
            width: 520px;
            height: 395px;
            margin-top:5%;
            border: 10px #333 solid;
            
        }

        #videoElement {
            width: 500px;
            height: 375px;
            background-color: #666;
            transform: scaleX(-1)
            
        }

        #buttonContainer {
            text-align: center;
        }

        #captureBtn,
        #downloadLink {
            display: block;
            margin: 10px auto;
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        #removeCapturedBtn{
            display: block;
            margin: 10px auto;
            padding: 10px;
            background-color: red;
            color: #fff;
            border: none;
            cursor: pointer;
        }
    </style>

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
    <div id="container">
        <video autoplay="true" id="videoElement"></video>
    </div>
    <div id="buttonContainer">
    
        <canvas id="canvas" style="display:none;"></canvas></br></br>
        
        <canvas id="canvas" style="display:none;"></canvas>
        <button id="webcamToggleButton">Toggle Webcam</button>
        <button id="audioToggleButton">Test Mic</button><br><br>
        <audio id="audioElement" controls style="display:none;"></audio>
        <canvas id="volumeCanvas" style="display:none;"></canvas>
        

        
        
        
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

        // Declare variables globally
var video = document.querySelector("#videoElement");
var canvas = document.querySelector("#canvas");
var capturedImage = document.querySelector("#capturedImage");
var downloadLink = document.querySelector("#downloadLink");
var stream; // Declare the stream variable globally

// Function to toggle webcam
function toggleWebcam() {
    // If the stream is active, stop it to close the webcam
    if (stream && stream.active) {
        stopWebcam();
        document.getElementById("captureBtn").style.display = "none";
    } else {
        // If the stream is not active, start it to open the webcam
        startWebcam();
        document.getElementById("captureBtn").style.display = "block";
    }
}

// Function to start the webcam
function startWebcam() {
    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(function (newStream) {
                stream = newStream;
                video.srcObject = stream;
            })
            .catch(function (error) {
                console.log("Something went wrong!", error);
            });
    }
}

// Function to stop the webcam
function stopWebcam() {
    if (stream && stream.active) {
        var tracks = stream.getTracks();
        tracks.forEach(function (track) {
            track.stop();
        });
        video.srcObject = null;
    }
}

// Declare audio variable globally
var audio = document.querySelector("#audioElement");

// Function to toggle audio
function toggleAudio() {
    // If the audio is playing, pause it
    if (!audio.paused) {
        audio.pause();
    } else {
        // If the audio is paused, play it
        startAudio();
    }
}

// Function to start the audio
function startAudio() {
    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(function (audioStream) {
                // Connect the audio stream to the audio element
                audio.srcObject = audioStream;

                // Play the audio
                audio.play();
            })
            .catch(function (error) {
                console.log("Something went wrong with audio!", error);
            });
    }
}

// Function to stop the audio
function stopAudio() {
    // Pause the audio and stop the audio stream
    audio.pause();
    if (audio.srcObject) {
        var audioTracks = audio.srcObject.getTracks();
        audioTracks.forEach(function (track) {
            track.stop();
        });
        audio.srcObject = null;
    }
}

// Example: Call toggleAudio function when a button is clicked
var audioToggleButton = document.getElementById("audioToggleButton");
audioToggleButton.addEventListener("click", toggleAudio);

// Example: Call toggleWebcam function when a button is clicked
var webcamToggleButton = document.getElementById("webcamToggleButton");
webcamToggleButton.addEventListener("click", toggleWebcam);
        

    

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