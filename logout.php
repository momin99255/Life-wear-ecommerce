<?php
include 'db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

session_destroy();
header('Location: index.php');
exit;
?>

