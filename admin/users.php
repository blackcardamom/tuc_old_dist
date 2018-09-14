<?php
    include_once 'header.php';
    $selected = "users";
    include_once 'topnav.php';
    session_start();
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }
    print_r($_SESSION);
    if(isset($_POST['logout'])) {
        session_destroy();
        header("Location: index.php");
        exit;
    }
?>

<form action="users.php" method="post">
    <button name="logout" type="submit" class="action_button">Logout</button>
</form>

<?php include_once 'footer.php'; ?>
