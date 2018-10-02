<?php
    // Start the session
    session_start();

    // Make sure we're logged in
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }

    // Make sure we have an ID to work with
    if(empty($_GET['id'])) {
        header("Location: features.php?err=missing_field");
        exit;
    }

    // Setup the page
    include_once 'header.php';
    $selected = "features";
    include_once 'topnav.php';
    include_once 'admin.inc.php';
    include_once '../includes/admin_validate.inc.php';

    $readyToDelete = isset($_POST['submit']) && checkCredentials($_POST['uid'],$_POST['pwd']);

    $sql = "SELECT r.title FROM feature_cards f, recipes r WHERE r.id=f.recipe_id AND f.feature_id = :id";
    $stmt = $admin_pdo->prepare($sql);
    $stmt->bindValue('id',$_GET['id'],PDO::PARAM_INT);
    $stmt->execute();
    if(!($postTitle = $stmt->fetch(PDO::FETCH_ASSOC)['title'])) {
        // We don't have a feature with that ID
        header("Location: features.php?err=no_feature_".$_GET['id']);
        exit;
    }
?>
<div class="message_wrapper">
    <?php if(!$readyToDelete): ?>
    <h1>Are you sure you want to delete this feature card?</h1>
    <p><?= $postTitle ?></p>
    <form action="delete_feature.php?id=<?= $_GET['id'] ?>" method= "POST">
        <input type="text" name="uid" placeholder="Username...">
        <input type="password" name="pwd" placeholder="Password...">
        <br><br>
        <?php if(isset($_POST['submit']) && !checkCredentials($_POST['uid'],$_POST['pwd'])): ?>
            <p>Your username and password were not recognised.</p>
        <?php endif; ?>
        <button class="action_button" type="submit" name="submit">Yes</button>
        &nbsp; &nbsp;
        <a href="features.php"><button class="action_button" type="button">No</button></a>
    </form>
    <?php else: ?>
    <?php
        // Here we actually delete the post
        $sql = "DELETE FROM feature_cards WHERE feature_id=:id";
        $stmt = $admin_pdo->prepare($sql);
        $stmt->bindValue('id',$_GET['id']);
        $stmt->execute();
     ?>
    <h1>Post deleted</h1>
    <a href="features.php"><button class="action_button">Return</button></a>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
