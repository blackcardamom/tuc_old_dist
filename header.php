<?php
    if(!isset($selected)) {
        $selected = "";
    }
    if(!isset($titleSuffix)) {
        $titleSuffix = "";
    }
 ?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
        <link rel="stylesheet" href="tuc_poc.css">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <title>The Ugly Croissant<?= $titleSuffix ?></title>
    </head>

    <body id="myBody">
        <div class="topnav" id="myTopnav">
            <a href="index.php"><img src="assets/logos/the_ugly_croissant_long_EDIT.jpeg"></a>
            <a href="about.php" <?php if ($selected == "about") echo 'class="active_topnav"'; ?>>About</a>
            <a href="recipes_main.php" <?php if ($selected == "recipes") echo 'class="active_topnav"'; ?>>Recipes</a>
            <a href="gallery.php" <?php if ($selected == "gallery") echo 'class="active_topnav"'; ?>>Gallery</a>
            <a href="contact.php" <?php if ($selected == "contact") echo 'class="active_topnav"'; ?>>Contact</a>
            <a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fas fa-bars"></i></a>
        </div>
