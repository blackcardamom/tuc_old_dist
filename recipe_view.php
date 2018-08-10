<?php
    include_once 'header.php';
    include_once 'includes/conn.inc.php';

    $id = $_GET["id"];
    $sql = "SELECT * FROM recipes WHERE id=".$id;
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);

    if (!$resultCheck == 1) {
        echo "Something went terribly wrong";
    } else {
        $row = mysqli_fetch_assoc($result);
        $title = $row['title'];
        $recipe_active_time = $row['recipe_active_time'];
        $recipe_serves = $row['recipe_serves'];
        $intro_html = $row['intro_html'];
        $ingredients_html = $row['ingredients_html'];
        $method_html = $row['method_html'];
        $intro_img = $row['intro_img'];
    }
?>

<div class="recipe_intro">
    <div class="recipe_intro_img">
        <img src="<?= $intro_img ?>">
    </div>
    <h1><?= $title ?></h1>
    <p><i class="fas fa-clock" title="Cooking time"></i> <?= $recipe_active_time ?> &nbsp;&nbsp;
        <i class="fas fa-utensils" title="Serving size"></i> <?= $recipe_serves ?> &nbsp;&nbsp;
        <br id="mobile_linebreak"><br id="mobile_linebreak">
        <a><i class="fab fa-facebook button"title="Share to Facebook"></i></a> &nbsp;&nbsp;
        <a><i class="fab fa-twitter button" title="Share to Twitter"></i></a> &nbsp;&nbsp;
        <a><i class="fab fa-pinterest button" title="Share to Pinterest"></i></a> &nbsp;&nbsp;
        <a><i class="fab fa-reddit button" title="Share to Reddit"></i></a> &nbsp;&nbsp;
        <a><i class="fas fa-copy button" title="Copy to clipboard" onclick="copyToClip()"></i></a> &nbsp;&nbsp;
        <a><i class="fas fa-print button"  title="Print recipe"></i></a>
    </p>
    <p><?= $intro_html ?></p>
</div>
<div class="recipe_content_container">
    <div class="ingredients">
        <h2>Ingredients</h2>
        <?= $ingredients_html ?>
    </div>
    <div class="method">
        <h2>Method</h2>
        <?= $method_html ?>
    </div>
</div>


<?php include_once 'footer.php'; ?>
