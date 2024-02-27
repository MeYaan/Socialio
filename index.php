<?php
// index.php

// Include the database connection file
require_once("classes/db_connect.php");

// Redirect to login.php
header('Location: pages/login.php');
exit;
?>