<?php session_start();


    // Make sure we're logged in
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }

    // Make sure we have an ID to work with
    if(empty($_GET['id'])) {
        header("Location: features.php");
        exit;
    }

    // Setup the page
    include_once 'header.php';
    $selected = "features";
    include_once 'topnav.php';
?>

<?php include_once 'footer.php'; ?>
