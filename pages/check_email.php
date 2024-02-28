<?php
require_once("../classes/db_connect.php");
$db = new Database();

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $stmt = $db->getConnection()->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        echo 'exists';
    } else {
        echo 'not_exists';
    }
}
