<?php
    // Start the session
    session_start();

    // Make sure we're logged in
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }

    if(empty($_GET['id']) || empty($_GET['type'])) {
        header("Location: index.php?err=missing_field");
        exit;
    }

    // Make sure we have been passed an acceptable type
    $acceptableTypes = Array("recipe","blogpost");
    if(!in_array($_GET['type'],$acceptableTypes)) {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    // Setup the page
    include_once 'header.php';
    $selected = $_GET['type']."s";
    include_once 'topnav.php';
    include_once 'admin.inc.php';
    include_once '../includes/admin_validate.inc.php';

    $readyToDelete = isset($_POST['submit']) && checkCredentials($_POST['uid'],$_POST['pwd']);

    $sql = "SELECT title FROM ".$_GET['type']."s WHERE id = :id";
    $stmt = $admin_pdo->prepare($sql);
    $stmt->bindValue('id',$_GET['id'],PDO::PARAM_INT);
    $stmt->execute();
    $postTitle = $stmt->fetch(PDO::FETCH_ASSOC)['title'];
?>
<div class="message_wrapper">
    <?php if(!$readyToDelete): ?>
    <h1>Are you sure you want to delete this post?</h1>
    <p><?= $postTitle ?> - <?= $_GET['type'] ?></p>
    <form action="delete_post.php?id=<?= $_GET['id']?>&type=<?= $_GET['type'] ?>" method= "POST">
        <input type="text" name="uid" placeholder="Username...">
        <input type="password" name="pwd" placeholder="Password...">
        <br><br>
        <?php if(isset($_POST['submit']) && !checkCredentials($_POST['uid'],$_POST['pwd'])): ?>
            <p>Your username and password were not recognised.</p>
        <?php endif; ?>
        <button class="action_button" type="submit" name="submit">Yes</button>
        &nbsp; &nbsp;
        <a href="<?= $_SERVER['HTTP_REFERER'] ?>"><button class="action_button" type="button">No</button></a>
    </form>
    <?php else: ?>
    <?php
        // Here we actually delete the post
        $sql = "DELETE FROM " . $_GET['type'] ."s WHERE id=:id";
        $stmt = $admin_pdo->prepare($sql);
        $stmt->bindValue('id',$_GET['id']);
        $stmt->execute();
     ?>
    <h1>Post deleted</h1>
    <a href="<?= $_GET['type'] ?>s.php"><button class="action_button">Return</button></a>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
