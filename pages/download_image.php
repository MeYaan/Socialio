<?php
// download_image.php

if (isset($_GET['filename'])) {
    $filename = $_GET['filename'];
    $filepath = '../uploads/' . $filename;

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        echo 'File not found.';
    }
} else {
    echo 'Invalid request.';
}
