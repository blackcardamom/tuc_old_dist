<?php

    include_once 'includes/pdo.inc.php';
    include_once 'includes/base_assumptions.inc.php';

    if (empty($_GET['id'])) {
        header("Location: $website_root/pagenotfound.php?err=no_id_provided");
    } else {

        $sql = "SELECT * FROM recipes WHERE id = :id";
        $stmt = $pdo_conn->prepare($sql);
        $stmt->bindValue('id',$_GET['id']);
        $stmt->execute();

        if (!($row = $stmt->fetch(PDO::FETCH_ASSOC))) {
            header("Location: $website_root/pagenotfound.php?err=no_recipe_".$_GET['id']);
        } else {
            $title = $row['title'];
            $recipe_active_time = $row['recipe_active_time'];
            $recipe_wait_time = $row['recipe_wait_time'];
            $recipe_serves = $row['recipe_serves'];
            $social_fb = $row['social_fb'];
            $social_twtr = $row['social_twtr'];
            $social_pnt = $row['social_pnt'];
            $social_snoo = $row['social_snoo'];
            $print_pdf = $row['print_pdf'];
            $intro_html = $row['intro_html'];
            $ingredients_html = $row['ingredients_html'];
            $method_html = $row['method_html'];
            $intro_img = $website_root . "/" . $row['intro_img'];

            $active_time_span = (empty($recipe_active_time)) ? "" : '<span class="no_break"><i class="fas fa-clock"></i> '.$recipe_active_time.' &nbsp;&nbsp; </span>';
            $wait_time_span = (empty($recipe_wait_time)) ? "" : '<span class="no_break"><i class="fas fa-bed"></i> '.$recipe_wait_time.' &nbsp;&nbsp; </span>';
            $serves_span = (empty($recipe_serves)) ? "" : '<span class="no_break"><i class="fas fa-utensils"></i> '.$recipe_serves.' </span>';

            $info_span = $active_time_span . $wait_time_span . $serves_span;
        }
    }

    $selected="recipes";
    $titlePrefix="$title | ";
    $meta_desciption = strip_tags($intro_html);

    $meta_iscontent = 1;
    $meta_image = $intro_img;
    $meta_url = $website_root ."/recipe_view.php?id=" . htmlspecialchars($_GET['id']);
    $meta_jsonmarkup = "recipe";
    include_once 'header.php';
?>

<div class="recipe_intro">
    <div class="recipe_intro_img">
        <img src="<?= $intro_img ?>" alt="<?=$title?>">
    </div>
    <h1><?= $title ?></h1>
    <p style="line-height:1.5em;"><?= $info_span ?>
        <br id="mobile_linebreak"><span class='no_break'>
        <a href="<?= $social_fb ?>"><i class="fab fa-facebook button" title="Share to Facebook"></i></a> &nbsp;&nbsp;
        <a href="<?= $social_twtr ?>"><i class="fab fa-twitter button" title="Share to Twitter"></i></a> &nbsp;&nbsp;
        <a href="<?= $social_pnt ?>"><i class="fab fa-pinterest button" title="Share to Pinterest"></i></a> &nbsp;&nbsp;
        <a href="<?= $social_snoo ?>"><i class="fab fa-reddit button" title="Share to Reddit"></i></a> &nbsp;&nbsp;
        <a><i class="fas fa-copy button" title="Copy to clipboard" onclick="copyToClip()"></i></a> &nbsp;&nbsp;
        <a href="<?= $print_pdf ?>"><i class="fas fa-print button"  title="Print recipe"></i></a>
        </span>
    </p>
    <?= $intro_html ?>
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
