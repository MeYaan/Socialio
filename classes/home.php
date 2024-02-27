<?php

class Home
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getUserInfo($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM userinfo WHERE user_id = ?");
        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function logoutUser()
    {
        session_unset();
        session_destroy();
        echo "<script>alert('Logout Successful'); window.location = 'login.php';</script>";
    }

    public function getUserEmail($userId)
    {
        $stmt = $this->db->prepare("SELECT email FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['email'];
    }

    public function updateUserDetails($userId, $firstName, $lastName, $email, $address, $phonenumber, $password, $profileImage)
    {
        // Validate inputs
        if (empty($firstName) || empty($lastName) || empty($email)) {
            echo "<script>alert('Please fill in all required fields.');</script>";
            return;
        }

        // Check if the email is already in use by another user
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->execute([$email, $userId]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Email is already in use by another user.');</script>";
            return;
        }

        // If a new password is provided, hash and update it
        $hashedPassword = empty($password) ? null : hash('sha256', $password);

        // Update user details in the userinfo table
        $this->db->prepare("UPDATE userinfo SET firstname = ?, lastname = ?, address = ?, phonenumber = ? WHERE user_id = ?")
            ->execute([$firstName, $lastName, $address, $phonenumber, $userId]);

        // Update user details in the users table, including the password if provided
        if ($hashedPassword) {
            $this->db->prepare("UPDATE users SET email = ?, password = ? WHERE user_id = ?")
                ->execute([$email, $hashedPassword, $userId]);
        } else {
            $this->db->prepare("UPDATE users SET email = ? WHERE user_id = ?")
                ->execute([$email, $userId]);
        }

        // Handle image upload if a file is provided
        if (!empty($profileImage['name'])) {
            $imgFileName = $this->uploadImage($profileImage);
            $this->db->prepare("UPDATE userinfo SET firstname = ?, lastname = ?, img = ? WHERE user_id = ?")
                ->execute([$firstName, $lastName, $imgFileName, $userId]);
        }

        echo "<script>alert('User details updated successfully.'); location = 'accountsettings.php';</script>";
    }

    private function uploadImage($image)
    {
        $targetDirectory = "../uploads/";  // Create a directory named 'uploads' to store images
        $targetFile = $targetDirectory . basename($image['name']);
        move_uploaded_file($image['tmp_name'], $targetFile);
        return basename($image['name']);
    }

    public function getUserBio($userId)
    {
        $stmt = $this->db->prepare("SELECT bio FROM userinfo WHERE user_id = ?");
        $stmt->execute([$userId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['bio'];
    }

    public function updateUserBio($userId, $bio)
    {
        $this->db->prepare("UPDATE userinfo SET bio = ? WHERE user_id = ?")
            ->execute([$bio, $userId]);
    }

    public function getTotalImagesCount($userId)
{
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM gallery WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

public function getUserGallery($userId, $page = 1, $perPage = 6)
{
    $offset = ($page - 1) * $perPage;

    $stmt = $this->db->prepare("SELECT * FROM gallery WHERE user_id = ? ORDER BY id DESC LIMIT ?, ?");
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->bindParam(2, $offset, PDO::PARAM_INT);
    $stmt->bindParam(3, $perPage, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




    public function uploadImageToGallery($userId, $image, $title, $description)
    {
        $imgFileName = $this->uploadImage($image);

        $this->db->prepare("INSERT INTO gallery (user_id, image_filename, title, description) VALUES (?, ?, ?, ?)")
            ->execute([$userId, $imgFileName, $title, $description]);

        $this->db->prepare("UPDATE gallery SET likes = 0, dislikes = 0 WHERE user_id = ? AND image_filename = ?")
            ->execute([$userId, $imgFileName]);
    }

    public function deleteImageFromGallery($userId, $imageId, $imageName)
    {
        // Delete the image record from the database
        // Assuming $imageId is the ID of the image you want to delete
        $this->db->prepare("DELETE FROM likes WHERE image_id = ?")->execute([$imageId]);
        
        $this->db->prepare("DELETE FROM gallery WHERE id = ?")->execute([$imageId]);


        // Delete the image file from the uploads directory
        $filePath = "../uploads/$imageName";
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function getUserContactInfo($userId)
    {
        $query = "SELECT u.email, ui.address, ui.phonenumber FROM userinfo ui
                  JOIN users u ON ui.user_id = u.user_id
                  WHERE ui.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    public function deleteAccount($userId)
    {
        // Add logic to delete user account
        // For example, you can use the following query to delete the user from the database
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);

        $stmt = $this->db->prepare("DELETE FROM userinfo WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Additional cleanup tasks can be added here if needed

        // Logout the user after deleting the account
        $this->logoutUser();
    }


    // Inside the Home class in home.php




    public function likeImage($viewUserId, $userId, $imageId)
    {

        if (empty($_SESSION['user_id'])) {
            echo "User not logged in.";
            return;
        }

        $userId = $_SESSION['user_id'];


        if ($this->hasUserLikedImage($userId, $imageId)) {
            // User has already liked the image, so unlike it
            $this->db->prepare("UPDATE gallery SET likes = likes - 1 WHERE user_id = ? AND id = ?")
                ->execute([$viewUserId, $imageId]);

            // Remove the like record from the likes table
            $this->db->prepare("DELETE FROM likes WHERE user_id = ? AND image_id = ?")
                ->execute([$userId, $imageId]);

            echo "Image unliked successfully.";
        } else {
            // User hasn't liked the image yet, so add a like
            $this->db->prepare("UPDATE gallery SET likes = likes + 1 WHERE user_id = ? AND id = ?")
                ->execute([$viewUserId, $imageId]);

            // Insert a record in the likes table
            $this->db->prepare("INSERT INTO likes (user_id, image_id, likes_count) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE likes_count = likes_count + 1")
                ->execute([$userId, $imageId]);

            echo "Image liked successfully.";
        }
    }

    public function hasUserLikedImage($userId, $imageId)
    {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM likes WHERE user_id = ? AND image_id = ?");
        $stmt->execute([$userId, $imageId]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }
    public function updateImageDetails($userId, $imageId, $newTitle, $newDetails)
{
    // Use a prepared statement to update the title and details
    $stmt = $this->db->prepare("UPDATE gallery SET title = ?, description = ? WHERE user_id = ? AND id = ?");
    $stmt->execute([$newTitle, $newDetails, $userId, $imageId]);
    // Add error handling if needed
}

public function getViewTotalImagesCount($viewUserId)
{
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM gallery WHERE user_id = ?");
    $stmt->execute([$viewUserId]);
    return $stmt->fetchColumn();
}

public function getViewUserGallery($viewUserId, $page = 1, $perPage = 6)
{
    $offset = ($page - 1) * $perPage;

    $stmt = $this->db->prepare("SELECT * FROM gallery WHERE user_id = ? ORDER BY id DESC LIMIT ?, ?");
    $stmt->bindParam(1, $viewUserId, PDO::PARAM_INT);
    $stmt->bindParam(2, $offset, PDO::PARAM_INT);
    $stmt->bindParam(3, $perPage, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}