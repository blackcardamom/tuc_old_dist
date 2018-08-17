<?php
    if(empty($selected)) {
        $selected = "";
    }
    if(empty($titleSuffix)) {
        $titleSuffix = "";
    }
    if(empty($titlePrefix)) {
        $titlePrefix = "";
    }
    if( !isset($meta_iscontent) || !isset($meta_image) || !isset($meta_url) ) {
        $meta_iscontent = false;
    }
 ?>

<!DOCTYPE html>
<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
        <link rel="stylesheet" href="tuc_poc.css">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <title><?= $titlePrefix ?>The Ugly Croissant<?= $titleSuffix ?></title>

        <!-- Google meta tags -->
        <?php if(!empty($meta_desciption)) : ?>
            <meta name="desription" content="<?= $meta_desciption?>">
        <?php endif; ?>

        <!-- Open Graph meta tage -->
        <?php if($meta_iscontent) : ?>
            <meta property="og:title" content="<?= $titlePrefix ?>The Ugly Croissant<?= $titleSuffix ?>">
            <meta property="og:type" contet="website">
            <meta property="og:image" content="<?= $meta_image ?>">
            <meta property="og:url" content="<?= $meta_url ?>">
        <?php endif; ?>
    </head>

    <body id="myBody">
        <nav>
        <div class="topnav" id="myTopnav">
            <a href="index.php"><img src="assets/logos/the_ugly_croissant_long_EDIT.jpeg" alt="The Ugly Croissant logo"></a>
            <a href="about.php" <?php if ($selected == "about") echo 'class="active_topnav"'; ?>>About</a>
            <a href="recipes_main.php" <?php if ($selected == "recipes") echo 'class="active_topnav"'; ?>>Recipes</a>
            <a href="gallery.php" <?php if ($selected == "gallery") echo 'class="active_topnav"'; ?>>Gallery</a>
            <a href="contact.php" <?php if ($selected == "contact") echo 'class="active_topnav"'; ?>>Contact</a>
            <a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fas fa-bars"></i></a>
        </div>
    </nav>
