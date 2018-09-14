<?php
    include_once 'header.php';
    $selected = "features";
    include_once 'topnav.php';
    session_start();
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }
?>

<?php include_once 'footer.php'; ?>
