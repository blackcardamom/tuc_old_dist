<?php
    include_once 'header.php';
    $selected = "blogposts";
    include_once 'topnav.php';
    session_start();
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }
    print_r($_SESSION);
    session_destroy();
?>

<?php include_once 'footer.php'; ?>
