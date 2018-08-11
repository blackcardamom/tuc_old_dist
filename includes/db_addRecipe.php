<?php
    include_once 'includes/parsedown/Parsedown.php';
    echo $_POST['intro_md'];
    $parsedown = new Parsedown;
    $parsedown->setSafeMode(true);
    echo $parsedown->text($_POST['intro_md']);
