<?php
    // Start the session
    session_start();

    // Make sure we're logged in
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }

    // Make sure we have been passed an acceptable type
    $acceptableTypes = Array("recipe","blogpost");
    if(!in_array($_GET['type'],$acceptableTypes)) {
        header("Location: index.php");
        exit;
    }

    // Setup the page
    include_once 'header.php';
    $selected = $_GET['type']."s";
    include_once 'topnav.php';
