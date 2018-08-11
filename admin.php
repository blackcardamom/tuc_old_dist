<?php
    include_once 'header.php';
    include_once 'includes/conn.inc.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>

<div class="admin_container">
    <h1>Create new recipe</h1><br>
    <form action="new_recipe.php" method="get">
        <strong>Recipe Title</strong><br>
        <input type="text" name="title" placeholder="Lemon Meringue Pie"><br>
        <strong>Time spent cooking</strong><br>
        <input type="text" name="recipe_active_time" placeholder="2 hours 30 minutes"><br>
        <strong>Time spent waiting</strong><br>
        <input type="text" name="recipe_wait_time" placeholder="3 hours + overnight"><br>
        <strong>Intro Markdown Editor</strong><br>
        <textarea id="intro_mde"></textarea>
        <input type="hidden" name="intro_md">
        <strong>Ingredients Markdown Editor</strong><br>
        <textarea id="ingredients_mde"></textarea>
        <input type="hidden" name="ingredients_md">
        <strong>Method Markdown Editor</strong><br>
        <textarea id="method_mde"></textarea>
        <input type="hidden" name="method_md">
        <strong>Path to intro image</strong><br>
        <input type="text" name="intro_img" placeholder="recipes/id/intro_img.jpg"><br>
        <strong>Path to card image</strong><br>
        <input type="text" name="card_img" placeholder="recipes/id/card_img.jpg"><br>
        <strong>Path to printable pdf</strong><br>
        <input type="text" name="print_pdf" placeholder="recipes/id/lemon_meringue.pdf"><br>
        <input type="submit">
    </form>
</div>

<script>
var intro_mde = new SimpleMDE({ element: document.getElementById("intro_mde") });
var ingredients_mde = new SimpleMDE({ element: document.getElementById("ingredients_mde") });
var method_mde = new SimpleMDE({ element: document.getElementById("method_mde") });
</script>

<?php include_once 'footer.php'; ?>
