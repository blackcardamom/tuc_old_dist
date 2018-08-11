<?php
    include_once 'header.php';
    include_once 'includes/conn.inc.php';

    $sql = "SELECT `AUTO_INCREMENT`
            FROM  INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = '$dbName'
            AND   TABLE_NAME   = 'recipes';";

    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    $nextID = mysqli_fetch_assoc($result)['AUTO_INCREMENT'];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>

<div class="admin_container">
    <h1>Create new recipe (with ID <?= $nextID ?>)</h1><br>
    <form action="includes/db_addRecipe.php" method="post">
        <strong>Recipe Title</strong><br>
        <input type="text" name="title" placeholder="Lemon Meringue Pie"><br>
        <strong>Time spent cooking</strong><br>
        <input type="text" name="recipe_active_time" placeholder="2 hours 30 minutes"><br>
        <strong>Time spent waiting</strong><br>
        <input type="text" name="recipe_wait_time" placeholder="3 hours + overnight"><br>
        <strong>Intro Markdown Editor</strong><br>
        <textarea id="intro_mde"></textarea>
        <input type="hidden" name="intro_md" id="intro_md_input">
        <strong>Ingredients Markdown Editor</strong><br>
        <textarea id="ingredients_mde"></textarea>
        <input type="hidden" name="ingredients_md" id="ingredients_md_input">
        <strong>Method Markdown Editor</strong><br>
        <textarea id="method_mde"></textarea>
        <input type="hidden" name="method_md" id="method_md_input">
        <strong>Path to intro image</strong><br>
        <input type="text" name="intro_img" placeholder="recipes/id/intro_img.jpg"><br>
        <strong>Path to card image</strong><br>
        <input type="text" name="card_img" placeholder="recipes/id/card_img.jpg"><br>
        <strong>Path to printable pdf</strong><br>
        <input type="text" name="print_pdf" placeholder="recipes/id/lemon_meringue.pdf"><br>
        <strong>Username</strong> &nbsp;&nbsp;
        <input type="password" name="uid"> &nbsp;&nbsp;
        <strong>Password</strong> &nbsp;&nbsp;
        <input type="password" name="pwd"> &nbsp;&nbsp;
        <input type="submit" onclick="onNewRecipeSubmit()">
    </form>
</div>

<script>
var intro_mde = new SimpleMDE({ element: document.getElementById("intro_mde") });
var ingredients_mde = new SimpleMDE({ element: document.getElementById("ingredients_mde") });
var method_mde = new SimpleMDE({ element: document.getElementById("method_mde") });

var intro_md_input = document.getElementById("intro_md_input")
var ingredients_md_input = document.getElementById("ingredients_md_input")
var method_md_input = document.getElementById("method_md_input")

function onNewRecipeSubmit() {
    intro_md_input.value = intro_mde.value();
    ingredients_md_input.value = ingredients_mde.value();
    method_md_input.value = method_mde.value();
}
</script>

<?php include_once 'footer.php'; ?>
