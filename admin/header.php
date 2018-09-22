<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
    <?php if(isset($useJqueryUI) && $useJqueryUI ): ?>
        <link rel="stylesheet" href="../includes/jquery-ui.min.css">
    <?php endif; ?>
    <title>The Ugly Croissant - Admin</title>
</head>

<?php if(isset($useJqueryUI) && $useJqueryUI ): ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="../includes/jquery-ui.min.js"></script>
    <!--script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script-->
    <script src="../includes/jquery.ui.touch-punch.min.js"></script>
<?php endif; ?>

<?php if(!isset($loginBody)) { $loginBody = false; } ?>
<body id = "<?= $loginBody ? 'loginBody' : 'adminBody' ?>">
