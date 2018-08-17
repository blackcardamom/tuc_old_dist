<?php
    $titleSuffix=" - New blog";
    $meta_index = false;
    include_once 'header.php';
    include_once 'includes/conn.inc.php';
    include_once 'includes/base_assumptions.inc.php';

    $sql = "SELECT `AUTO_INCREMENT`
            FROM  INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = '$dbName'
            AND   TABLE_NAME   = 'blogposts';";

    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    $nextID = mysqli_fetch_assoc($result)['AUTO_INCREMENT'];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>

<div class="admin_container">
    <h1>Create new recipe (with ID <?= $nextID ?>)</h1><br>
    <form action="<?= $website_root ?>/includes/db_addBlog.php" method="post">
        <input type="hidden" name="nextID" value="<?= (string)$nextID?>">
        <strong>Blog Title</strong><br>
        <input type="text" name="title" placeholder="Lemon Meringue Pie"><br>
        <strong>Intro Text</strong><br>
        <textarea id="intro_mde"></textarea>
        <input type="hidden" name="intro" id="intro_input">
        <strong>Content Markdown Editor</strong><br>
        <textarea id="content_mde"></textarea>
        <input type="hidden" name="content_md" id="content_md_input">
        <strong>Username</strong> &nbsp;&nbsp;
        <input type="password" name="uid"> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">
        <strong>Password</strong> &nbsp;&nbsp;
        <input type="password" name="pwd"> &nbsp;&nbsp; <br id="mobile_linebreak"> <br id="mobile_linebreak">
        <input type="submit" name="submit" onclick="onNewBlogSubmit()">
    </form>
</div>

<script>
//var intro_mde = new SimpleMDE({ element: document.getElementById("intro_mde") });
var content_mde = new SimpleMDE({ element: document.getElementById("content_mde") });

//var intro_md_input = document.getElementById("intro_md_input")
var content_md_input = document.getElementById("content_md_input")

function onNewBlogSubmit() {
    //intro_md_input.value = intro_mde.value();
    content_md_input.value = content_mde.value();
}
</script>

<?php include_once 'footer.php'; ?>
