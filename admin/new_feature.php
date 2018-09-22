<?php
    session_start();

    // Make sure we're logged in
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }

    // Setup the page
    include_once 'header.php';
    $selected = "features";
    include_once 'topnav.php';
