<?php

class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function loginUser($email, $password)
    {
        $hashedPassword = hash('sha256', $password);

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $hashedPassword == $user['password']) {
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            echo "<script>window.location = 'home.php';</script>";
            exit;
        } else {
            echo "<script>alert('Wrong Email or Password'); window.location = 'login.php';</script>";
            exit;
        }
    }
    public function registerUser($email, $password, $confirmPassword, $firstname, $lastname, $phonenumber, $address)
    {
        // Check if the email already exists
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            echo "<script>alert('Email already exists. Please choose a different email.');</script>";
            return; // Do not proceed with the registration
        }

        // Continue with the registration process if the email is unique
        if ($password !== $confirmPassword) {
            echo "<script>alert('Passwords do not match');</script>";
            return; // Do not proceed with the registration
        }

        $hashedPassword = hash('sha256', $password);

        $stmt = $this->db->prepare("INSERT INTO userinfo (user_id, firstname, lastname, phonenumber, address) VALUES (NULL, ?, ?, ?, ?)");
        $stmt->execute([$firstname, $lastname, $phonenumber, $address]);

        $user_id = $this->db->lastInsertId();

        $stmt = $this->db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $hashedPassword]);

        echo "<script>alert('Registered Successfully'); window.location = 'login.php';</script>";
    }



    public function getOtherUsers($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM userinfo WHERE user_id != ?");
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function resetPassword($email, $newPassword, $confirmPassword)
    {
        // Validate email, new password, and confirm password here

        if ($newPassword !== $confirmPassword) {
            echo "<script>alert('Passwords do not match'); window.location = 'forgetpassword.php';</script>";
            exit;
        }

        $hashedPassword = hash('sha256', $newPassword);

        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);

        echo "<script>alert('Password reset successfully'); window.location = 'login.php';</script>";
    }

    public function likeImage($userId, $imageId)
    {
        $this->db->prepare("UPDATE gallery SET likes = likes + 1 WHERE user_id = ? AND id = ?")
            ->execute([$userId, $imageId]);
    }
}
