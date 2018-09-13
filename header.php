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
    if( !isset($meta_index) ) {
        $meta_index = true;
    }
    if(!isset($meta_jsonmarkup)) {
        $meta_jsonmarkup = false;
    }
    include_once 'includes/base_assumptions.inc.php';
    include_once 'includes/json_markup.inc.php';
 ?>

<!DOCTYPE html>
<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
        <link rel="stylesheet" href="tuc_poc.css">
        <meta name="theme-color" content="#48494B"/>
        <title><?= $titlePrefix ?>The Ugly Croissant<?= $titleSuffix ?></title>

        <!-- Favicons -->
        <link rel="shortcut icon" type="image/png" href="<?= $website_root ?>/apple-touch-icon.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= $website_root ?>/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= $website_root ?>/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= $website_root ?>/favicon-16x16.png">
        <link rel="manifest" href="<?= $website_root ?>/site.webmanifest">
        <link rel="mask-icon" href="<?= $website_root ?>/safari-pinned-tab.svg" color="#48494b">
        <meta name="msapplication-TileColor" content="#48494b">
        <meta name="theme-color" content="#48494B">

        <!-- Google meta tags -->

        <meta name="google-site-verification" content="qBP1cWkJvFTJuHy_OWfnHZvXzSaF1ya0FMYSaE3JOVo" />

        <?php if(!empty($meta_desciption)) : ?>
            <meta name="description" content="<?= $meta_desciption?>">
        <?php endif; ?>

        <!-- Open Graph meta tags -->
        <?php if($meta_iscontent) : ?>
            <meta property="og:title" content="<?= $titlePrefix ?>The Ugly Croissant<?= $titleSuffix ?>">
            <meta property="og:type" content="website">
            <meta property="og:image" content="<?= $meta_image ?>">
            <meta property="og:url" content="<?= $meta_url ?>">
        <?php endif; ?>

        <!-- Robots tags -->
        <?php if(!$meta_index) : ?>
            <meta property="robots" content="noindex">
        <?php endif; ?>

        <!-- Structured data markup -->
        <?php if($meta_jsonmarkup) :?>
            <script type="application/ld+json">
                <?= markupRecipeJSON($pdo_conn,$_GET['id']) ?>
            </script>
        <?php endif; ?>
    </head>

    <body id="myBody">
        <nav>
        <div class="topnav" id="myTopnav">
            <a href="<?= $website_root ?>/index.php"><img src="assets/logos/the_ugly_croissant_long_EDIT.jpeg" alt="The Ugly Croissant logo"></a>
            <a href="<?= $website_root ?>/about.php" <?php if ($selected == "about") echo 'class="active_topnav"'; ?>>About</a>
            <a href="<?= $website_root ?>/recipes_main.php" <?php if ($selected == "recipes") echo 'class="active_topnav"'; ?>>Recipes</a>
            <a href="<?= $website_root ?>/gallery.php" <?php if ($selected == "gallery") echo 'class="active_topnav"'; ?>>Gallery</a>
            <a href="<?= $website_root ?>/contact.php" <?php if ($selected == "contact") echo 'class="active_topnav"'; ?>>Contact</a>
            <a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fas fa-bars"></i></a>
        </div>
    </nav>
