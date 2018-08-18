<?php
    include_once '../includes/admin_validate.inc.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
<p>Does blackcardamom exist? <?= userExists('blackcardamom') ? 'yes' : 'no' ?> </p>
<p>Does randomuser exist? <?= userExists('randomuser') ? 'yes' : 'no' ?> </p>
</body>
</html>
