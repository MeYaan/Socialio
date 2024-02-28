<?php
require_once("../classes/db_connect.php");
require_once("../classes/home.php");
require_once("../classes/users.php");
session_start();

$db = new Database();
$home = new Home($db->getConnection());
$user = new User($db->getConnection());

// Check if the user is logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the user ID from the URL parameter
// Inside viewuser.php
if (isset($_GET['user_id'])) {
    $viewUserId = $_GET['user_id'];
    $viewUserInfo = $home->getUserInfo($viewUserId);

    // Check if $viewUserInfo is false, indicating an error in getUserInfo
    if ($viewUserInfo === false) {
        // Log the error or redirect to an error page
        error_log("Error: Unable to retrieve user information.");
        // You can redirect or display an error message here
        echo "Error: Unable to retrieve user information.";
        exit;
    }

    $viewUserFirstName = $viewUserInfo['firstname'];
    $viewUserLastName = $viewUserInfo['lastname'];
    $viewUserImgFileName = $viewUserInfo['img'];
    $viewUserBio = $viewUserInfo['bio'];
    $viewUserGallery = $home->getViewUserGallery($viewUserId);
} else {
    // Redirect to home if user_id is not provided
    header('Location: home.php');
    exit;
}


// Inside viewuser.php
if (isset($_POST['likeImage'])) {
    $imageId = $_POST['imageId'];
    // Perform like action with $imageId
    $home->likeImage($viewUserId, $userId, $imageId);
    echo "Like action triggered for image ID: $imageId";
    exit;
}

?>

<!-- Rest of your HTML content -->

<html lang="en">

<head>
    <title> Home </title>
    <link rel="stylesheet" href="../css/styles1.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/w3css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">


    <script src="home.js"></script>

    <link rel="icon" href="../images/usericon.png">
</head>

<body class="w3-content" style="max-width:1600px">

    <!-- Top Navigation -->
    <div class="topnav" id="myTopnav">
        <a href="home.php">Home</a>
        <a href="accountsettings.php">Account Settings</a>
        <a href="socialio.php">About Socialio</a>
        <a href="javascript:void(0);" class="logout-link" onclick="logout()">Logout</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars navbar-toggler"></i>
        </a>
    </div>

    <!-- User Profile Section -->
    <div class="w3-row w3-center ">
        <div class="w3-container w3-center w3-dark-grey">
            <?php
            $profileImageSrc = !empty($viewUserImgFileName) ? "../uploads/$viewUserImgFileName" : "../images/usericon.png";
            echo "<img src='$profileImageSrc' alt='Profile Image' class='socials-image'>";
            ?>
            <div class="user-name">
                <?php echo $viewUserFirstName . " " . $viewUserLastName; ?>
            </div>
        </div>

        <div class="w3-center w3-container w3-xlarge w3-text-grey">
            <div>
                <!-- Display User Bio -->
                <p class="w3-light-grey">
                    <?php echo !empty($viewUserBio) ? $viewUserBio : "This user doesn't have a bio."; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- User Gallery Section -->
    <div class="w3-panel w3-text-grey w3-center">
        <h4 class="w3-black w3-text-white w3-round-medium">USER GALLERY:</h4>
    </div>

    <div class="w3-row social-profiles2 w3-center">
        <?php
        if (empty($viewUserGallery)) {
            echo "<div class='w3-container w3-center'><p>No images in the gallery</p></div>";
        } else {

            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;


            $totalImages = $home->getTotalImagesCount($viewUserId);

            // Get user gallery for the current page
            $viewUserGallery = $home->getUserGallery($viewUserId, $currentPage);

            // Get user gallery for the current page


            foreach ($viewUserGallery as $image) {
                echo "<div class='w3-third w3-container '>";
                echo "<img src='../uploads/{$image['image_filename']}' class='w3-round-large gallery-image' onclick=\"openModal('../uploads/{$image['image_filename']}', '{$image['title']}', '{$image['description']}')\">";

                // Check if the user has already liked the photo
                $likeButtonText = $home->hasUserLikedImage($viewUserId, $image['id']) ? 'Unlike' : 'Like';

                echo "<center><button class='like-button' onclick=\"likeImage({$viewUserId}, {$image['id']}, '{$image['image_filename']}')\">$likeButtonText</button>";

                echo "<span class='like-count'>&nbsp Likes: {$image['likes']}</span></center>";

                echo "</div>";
            }
            // Get current page number from the query string
            echo "</div>";
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

            $viewUserGallery = $home->getUserGallery($viewUserId, $currentPage);

            echo "<div class='w3-row social-profiles2'>";
            foreach ($viewUserGallery as $image) {
                echo "<div class=' w3-third  w3-container'>";
                // Display image as before...
                echo "</div>";
            }
            echo "</div>";
            // Display pagination links
            $totalPages = ceil($totalImages / 6); // Assuming 6 images per page
            echo "<div class='pagination'>";
            for ($i = 1; $i <= $totalPages; $i++) {
                echo "<a href='viewuser.php?user_id=$viewUserId&page=$i'>$i</a> ";
            }
            echo "</div>";
        }
        ?>
    </div>


    <!-- Pagpreview sa Image -->

    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="previewImage" class="modal-content">
        <div id="imageDetails"></div>
    </div>

    <!-- Footer Section -->
    <div class="w3-row w3-section">
        <div class="w3-third w3-center w3-container w3-black w3-large" style="height: 300px;">
            <h2>Contact Info</h2>
            <?php
            // Fetch user contact information
            $viewUserContactInfo = $home->getUserContactInfo($viewUserId);

            // Display user contact information
            echo "<p><i class='fa fa-map-marker' style='width:30px'></i> " . $viewUserContactInfo['address'] . "</p>";
            echo "<p><i class='fa fa-phone' style='width:30px'></i> " . $viewUserContactInfo['phonenumber'] . "</p>";
            echo "<p><i class='fa fa-envelope' style='width:30px'></i> " . $viewUserContactInfo['email'] . "</p>";
            ?>
        </div>
        <div class="w3-third w3-center w3-large w3-dark-grey w3-text-white" style="height:300px">
            <h2>Contact Us</h2>
            <p>If you have an idea.</p>
            <p>What are you waiting for?</p>

        </div>
        <div class="w3-third w3-center w3-large w3-grey w3-text-white" style="height:300px">
            <h2>Socials</h2>
            <i class="w3-xlarge fa fa-facebook-official"></i><br>
            <i class="w3-xlarge fa fa-pinterest-p"></i><br>
            <i class="w3-xlarge fa fa-twitter"></i><br>
            <i class="w3-xlarge fa fa-flickr"></i><br>
            <i class="w3-xlarge fa fa-linkedin"></i>
        </div>
    </div>

    <!-- Logout Form -->
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