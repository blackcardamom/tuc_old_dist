<?php session_start();
if (isset($_SESSION['uid'])) {
    header("Location: recipes.php");
    exit;
}

    include_once 'admin.inc.php';
    include_once '../includes/admin_validate.inc.php';
    $goodLogin = isset($_POST['submit']) && checkCredentials($_POST['uid'],$_POST['pwd']);

?>
<?php if(!$goodLogin) :?>
    <!-- Show login page -->
    <?php
        $loginBody = true;
        include_once 'header.php';
    ?>
    <div class="login_wrapper">
        <div class="login_logo">
            <img src="https://www.theuglycroissant.com/assets/logos/the_ugly_croissant_long_EDIT.jpeg" alt="The Ugly Croissant logo">
        </div>
        <div class="login_form">
            <form action="index.php" method="post">
                <?= isset($_POST['submit']) ? "<p>Your username and password were not recognised</p>" : "" ?>
                <input type="text" name="uid" placeholder="Username...">
                <input type="password" name="pwd" placeholder="Password...">
                <button type="submit" name="submit">Log in</button>
            </form>
        </div>
    </div>
<?php
    else :
        $loginBody = false;
        $_SESSION['uid'] = $_POST['uid'];
        header("Location: recipes.php");
    endif;
    include_once 'footer.php';
?>
