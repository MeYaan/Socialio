// home.js

function openModal(imageSrc, title, description) {
  var modal = document.getElementById("imageModal");
  var previewImage = document.getElementById("previewImage");
  var imageDetails = document.getElementById("imageDetails");

  previewImage.src = imageSrc;
  imageDetails.innerHTML = "<h2>" + title + "</h2><p>" + description + "</p>";
  modal.style.display = "block";
}





function closeModal() {
  document.getElementById("imageModal").style.display = "none";
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
    xhr.onreadystatechange = function () {
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

function editBio() {
  var bioModal = document.getElementById("bioModal");
  bioModal.style.display = "block";
}

function closeBioModal() {
  var bioModal = document.getElementById("bioModal");
  bioModal.style.display = "none";
}

function deleteImage(imageId, imageName) {
  var confirmation = confirm("Are you sure you want to delete this image?");
  if (confirmation) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "home.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        // Reload the page after successful deletion
        window.location.reload();
      }
    };
    // Send a request to a PHP script to handle the deletion
    xhr.send("deleteImage=" + imageId + "&imageName=" + imageName);
  }
}

function openUserGalleryModal(userId, firstName, lastName) {
  var modal = document.getElementById("userGalleryModal");
  var modalContent = document.getElementById("userGalleryContent");

  // Clear previous content
  modalContent.innerHTML = "";

  // Set modal title
  var modalTitle = document.createElement("h2");
  modalTitle.textContent = firstName + " " + lastName + "'s Gallery";
  modalContent.appendChild(modalTitle);

  // Fetch user's gallery images
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "getUserGallery.php?userId=" + userId, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      var userGallery = JSON.parse(xhr.responseText);

      if (userGallery.length === 0) {
        // Display a message when the gallery is empty
        var noWorksMessage = document.createElement("p");
        noWorksMessage.textContent = "No recent works";
        modalContent.appendChild(noWorksMessage);
      } else {
        // Display user's gallery images
        userGallery.forEach(function (image) {
          var imageElement = document.createElement("img");
          imageElement.src = "../uploads/" + image.image_filename;
          imageElement.style.width = "100%";

          modalContent.appendChild(imageElement);
        });
      }

      // Open the modal
      modal.style.display = "block";
    }
  };
  xhr.send();
}

function closeUserGalleryModal() {
  var modal = document.getElementById("userGalleryModal");
  modal.style.display = "none";
}

function likeImage(userId, imageId, imageName) {
  sendLikeDislikeRequest(userId, imageId, imageName, "like");
}

function sendLikeDislikeRequest(userId, imageId, imageName, action) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "viewuser.php?user_id=" + userId, true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      // Reload the page after successful action
      window.location.reload();
    }
  };

  var params =
    "imageId=" + imageId + "&imageName=" + imageName + "&" + action + "Image=1";
  xhr.send(params);
}


var originalModalDisplay = '';

// Function to open the edit modal with pre-filled values
function openEditModal(imageSrc, imageId, title, details) {

  var editPreviewImage = document.getElementById("editPreviewImage");
  editPreviewImage.src = imageSrc;
    // Store the original display style
    

    // Hide the image details modal
    document.getElementById('imageModal').style.display = 'none';

    // Set the values in the edit modal
    document.getElementById('editImageId').value = imageId;
    document.getElementById('editTitle').value = title;
    document.getElementById('editDetails').value = details;
    document.getElementById('editImageModal').style.display = 'block';
}

// Function to close the edit modal
function closeEditModal() {
    // Restore the original display style of the image details modal
    document.getElementById('imageModal').style.display = originalModalDisplay;

    // Close the edit modal
    document.getElementById('editImageModal').style.display = 'none';
}

function toggleDarkMode() {
  const body = document.body;
  const darkModeIcon = document.getElementById("darkModeIcon");

  // Toggle dark mode class on the body
  body.classList.toggle("dark-mode");

  // Check if dark mode is active after toggling
  const isDarkMode = body.classList.contains("dark-mode");

  // Set the mode in localStorage
  localStorage.setItem("darkMode", isDarkMode);

  // Update the dark mode icon based on the mode
  darkModeIcon.src = isDarkMode ? "../images/lightmode.png" : "../images/nightmode.png";

  // Additional function calls or logic after toggling
  updateEditBioIcon();
}

function updateEditBioIcon() {
  const editBioIcon = document.getElementById("editBioIcon");
  const isDarkMode = document.body.classList.contains("dark-mode");

  // Update the image source based on dark mode status
  editBioIcon.src ="../images/edit.png" ;

}

document.addEventListener("DOMContentLoaded", function () {
  const body = document.body;
  const darkModeIcon = document.getElementById("darkModeIcon");

  // Check if the user's preferred mode is already set in localStorage
  const isDarkMode = localStorage.getItem("darkMode") === "true";

  // If not set, use light mode as the default
  if (isDarkMode === null) {
    localStorage.setItem("darkMode", "false");
  }

  // Apply the mode to the body and update the icon
  body.classList.toggle("dark-mode", isDarkMode);
  darkModeIcon.src = isDarkMode ? "../images/lightmode.png" : "../images/nightmode.png";
});



