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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bio'])) {
        $newBio = $_POST['bio'];
        $home->updateUserBio($user_id, $newBio);
        // You may choose to redirect or refresh the page after saving the bio
        header('Location: home.php');
    }
}

$userGallery = $home->getUserGallery($user_id);

$otherUsers = $user->getOtherUsers($user_id);

if (isset($_POST['deleteImage'])) {
    $imageId = $_POST['deleteImage'];
    $imageName = $_POST['imageName'];

    // Delete the image from the gallery
    $home->deleteImageFromGallery($user_id, $imageId, $imageName);

    // Output a response if needed (e.g., success message)
    echo "Image deleted successfully";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['editImageId'])) {
        $editImageId = $_POST['editImageId'];
        $editTitle = $_POST['editTitle'];
        $editDetails = $_POST['editDetails'];

        // Perform the update
        $home->updateImageDetails($user_id, $editImageId, $editTitle, $editDetails);

        // You may choose to redirect or refresh the page after saving the changes
        header('Location: home.php');
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">


    <script src="home.js"></script>

    <link rel="icon" href="../images/usericon.png">
</head>

<body class="w3-content" style="max-width:1600px">

    <!-- Para Top Navigation -->
    <div class="topnav" id="myTopnav">
        <a href="#home" class="active">Dashboard</a>
        <a href="accountsettings.php">Account Settings</a>
        <a href="socialio.php">About Socialio</a>
        <a href="videocam.php">Video Call</a>
        <a href="javascript:void(0);" class="logout-link" onclick="logout()">Logout</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars navbar-toggler"></i>
        </a>
    </div>

    <!-- First Grid: Logo & About -->
    <div class="w3-row w3-center">
        <div class="w3-container w3-center w3-dark-grey">
            <?php
            $imgFileName = $userInfo['img'];

            if (!empty($imgFileName)) {
                echo "<img src='../uploads/$imgFileName' alt='Profile Image' class = 'socials-image'>";
            } else {
                echo "<img src='../images/usericon.png' alt='Default Profile Image' style='width: 50%; max-height: 100px; max-width: 100px; border: 2px solid rgba(0,0,0,0.5); margin-top: 5px;'>";
            }
            ?>
            <!-- Display User's Name -->
            <div class="user-name">
                <?php
                echo $firstName . " " . $lastName;
                ?>
            </div>
        </div>

        <div class=" w3-center w3-container w3-xlarge w3-text-grey">

            <div>
                <!-- Display User Bio -->
                <p class="w3-light-grey w3-border w3-round-large">
                    <?php
                    $userBio = $home->getUserBio($user_id);
                    echo !empty($userBio) ? $userBio : "The user didn't have a bio";
                    ?>
                </p>
                <!-- Add/Edit Bio Button -->
                <button onclick="editBio()" class="w3-button w3-round-large">
  <img id="editBioIcon" src="../images/edit.png" style="width:25px; margin-top:2px;">
</button>
            </div>
            <div>
            </div>
        </div>

        <!-- Second Grid: Socials -->
        <div class="w3-panel w3-text-grey w3-center">
            <h4 class="w3-black w3-text-white w3-round-medium">OTHER USERS:</h4>
        </div>

        <div class="social-profiles">
            <?php
            foreach ($otherUsers as $otherUser) {
                echo "<div class='social-profile'>";
                $otherUserId = $otherUser['user_id'];
                $otherUserInfo = $home->getUserInfo($otherUserId);
                $otherUserFirstName = $otherUserInfo['firstname'];
                $otherUserLastName = $otherUserInfo['lastname'];
                $otherUserImgFileName = $otherUserInfo['img'];
                $otherUserBio = $otherUserInfo['bio'];

                $profileImageSrc = !empty($otherUserImgFileName) ? "../uploads/$otherUserImgFileName" : "../images/usericon.png";

                // Update the link to point to viewuser.php with the user_id parameter

                echo "<a href='viewuser.php?user_id=$otherUserId' class='profile-link'>";
                echo "<img src='$profileImageSrc' alt='Profile Image' class='socials-image'>";
                echo "<p class='socials-name'>$otherUserFirstName $otherUserLastName</p>";
                echo "</a>";
                echo "</div>";
            }

            ?>

        </div>

        <!-- ... (Previous code) ... -->

<!-- Third Grid: Most Recent Work -->
<div class="w3-panel w3-text-grey w3-center">
    <h4 class="w3-black w3-text-white w3-round-medium">MOST RECENT WORK:</h4>
</div>

<?php
if (empty($userGallery)) {
    // Display a message when the gallery is empty
    echo "<div class='w3-container w3-center'><p>No recent works</p></div>";
} else {

    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;


    $totalImages = $home->getTotalImagesCount($user_id);
    
// Get user gallery for the current page
    $userGallery = $home->getUserGallery($user_id, $currentPage);

    echo "<div class='w3-row social-profiles2'>";
    foreach ($userGallery as $image) {
        echo "<div class=' w3-third  w3-container'>";
        echo "<img src='../uploads/{$image['image_filename']}' class='w3-round-large gallery-image' onclick=\"openModal('../uploads/{$image['image_filename']}', '{$image['title']}', '{$image['description']}')\">";

        echo "</br><span class='like-count'>Likes: {$image['likes']}</span>";

        echo "<br><a href='download_image.php?filename={$image['image_filename']}' download><button class = 'download-button'>Download</button></a>";

        // Button to open edit modal
        echo "<button class = 'edit-button' onclick=\"openEditModal( '../uploads/{$image['image_filename']}', {$image['id']},  '{$image['title']}', '{$image['description']}')\">Edit</button>";

        echo "<button class = 'delete-button2' onclick=\"deleteImage({$image['id']}, '{$image['image_filename']}')\">Delete</button>";

        echo "</div>";
    }

    echo "</div>";

    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Get total images count for the user
    $totalImages = $home->getTotalImagesCount($user_id);
    
    // Get user gallery for the current page
    $userGallery = $home->getUserGallery($user_id, $currentPage);
    
    echo "<div class='w3-row social-profiles2'>";
    foreach ($userGallery as $image) {
        echo "<div class=' w3-third  w3-container'>";
        // Display image as before...
        echo "</div>";
    }
    echo "</div>";
    
    // Display pagination links
    $totalPages = ceil($totalImages / 6); // Assuming 6 images per page
    echo "<div class='pagination'>";
    for ($i = 1; $i <= $totalPages; $i++) {
        echo "<a href='home.php?page=$i'>$i</a> ";
    }
    echo "</div>";
}
?>

<!-- Pagpreview sa Image -->

    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="previewImage" class="modal-content">
        <div id="imageDetails"></div>
    </div>


<!-- Edit Picture Modal -->
<div id="editImageModal" class="modal">

        <img id="editPreviewImage" class="modal-content">
    <span class="close" onclick="closeEditModal()">&times;</span>
    
    <form id="editImageForm" method="post" >
         
        <input type="hidden" id="editImageId" name="editImageId" value="">
        <label for="editTitle" style="color: white;">Title:</label>
        <input type="text" id="editTitle" name="editTitle" required>
        <br>
        <label for="editDetails" style="color: white;">Details:</label>
        <textarea id="editDetails" name="editDetails" rows="4" cols="50"></textarea>
        <br>
        <button type="submit" class = "savechanges">Save Changes</button>
    </form>
</div>

<!-- ... (Remaining code) ... -->


    <div class="w3-panel w3-text-grey w3-center">
        <h4 class="w3-black w3-text-white w3-round-medium">EVENTS:</h4>

        <!-- Eto ung Google Calendar -->
        <div>
            <iframe id="calendarFrame" src="https://calendar.google.com/calendar/embed?src=en.philippines%23holiday%40group.v.calendar.google.com&ctz=Asia%2FManila" style="border: 0; width: 100%; height: 600px; display: block; margin: 0 auto;" frameborder="0" scrolling="no"></iframe>
        </div>

    </div>

    <!-- Footer -->
    <div class="w3-row w3-section">
        <div class="w3-third w3-center w3-container w3-black w3-large" style="height:300px">
            <h2>Contact Info</h2>
            <p><i class="fa fa-map-marker" style="width:30px"></i> <?php echo $address; ?> </p>
            <p><i class="fa fa-phone" style="width:30px"></i> <?php echo $phonenumber; ?> </p>
            <p><i class="fa fa-envelope" style="width:30px"> </i> <?php echo $userEmail; ?></p>
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
    <div class="w3-container w3-text-grey">

    </div>
    <!-- Logout -->
    </center>
    <div style="display: none;">
        <form id="logout-form" action="home.php" method="post">
            <button class="submitbutton" type="submit" id="logout" name="logout" value="logout"></button>
        </form>
    </div>
    <!-- Modal para sa Bio -->
    <center>
        <div id="bioModal" class="modal">
            <span class="close" onclick="closeBioModal()">&times;</span>
            <form id="bioForm" method="post">
                <label for="bio" style="color: white;">Edit Bio:</label>
                <textarea id="bio" name="bio" rows="4" cols="50"><?php echo $userBio; ?></textarea>
                <br>
                <button type="submit">Save</button>
            </form>
        </div>
<!-- Dark Mode Switch Button -->
<button onclick="toggleDarkMode()" class="dark-mode-switch">
  <img id="darkModeIcon" src="../images/nightmode.png" alt="Dark Mode Icon" class="icon2">
</button>
</body>

</html>