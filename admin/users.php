<?php
    include_once 'header.php';
    $selected = "users";
    include_once 'topnav.php';
    session_start();
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }
    if(isset($_POST['logout'])) {
        session_destroy();
        header("Location: index.php");
        exit;
    }
?>
<div class="form_container">
<h1 style="text-align:center"> Are you sure you want to logout?</h1>
<form action="users.php" method="post">
    <button name="logout" type="submit" class="action_button" style="position: absolute; left: 50%; transform: translateX(-50%);">Logout</button>
</form>
</div>

<?php include_once 'footer.php'; ?>
