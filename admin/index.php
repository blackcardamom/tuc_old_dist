<?php
    include_once '../includes/pdo.inc.php';
    include_once '../includes/base_assumptions.inc.php';
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
                <img src="../assets/logos/the_ugly_croissant_long_EDIT.jpeg" alt="The Ugly Croissant logo">
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
<?php else : ?>
    <?php include_once 'header.php'; ?>
    <!-- Show admin index page -->
    Login
<?php endif; ?>
<?php include_once 'footer.php'; ?>
