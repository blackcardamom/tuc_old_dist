<?php
    $selected = "recipes";
    $titleSuffix=" - Recipes";
    include_once 'header.php';
    include_once 'includes/pdo.inc.php';
    include_once 'includes/base_assumptions.inc.php';
?>

<div class="container">
    <img src="<?= $website_root ?>/assets/Celebration_cake_crop.jpg">
    <div class="bottom-right">Cakes</div>
    <a href=#cakes>
        <div class="container_overlay"></div>
    </a>
</div>
<div class="container">
    <img src="<?= $website_root ?>/assets/tart_crop.JPG">
    <div class="bottom-right">Tarts</div>
    <a href=#tarts>
        <div class="container_overlay"></div>
    </a>
</div>
<div class="container">
    <img src="<?= $website_root ?>/assets/cookies_crop.JPG">
    <div class="bottom-right">Biscuits</div>
    <a href=#chocolate>
        <div class="container_overlay"></div>
    </a>
</div>
<div class="container">
    <img src="<?= $website_root ?>/assets/Bread_crop.jpg">
    <div class="bottom-right">Other</div>
    <a href=#other>
        <div class="container_overlay"></div>
    </a>
</div>
<div class="recent_recipes_title">
    <h1>Recent Recipes</h1>
</div>
<?php
    $sql = "SELECT * FROM recipes ORDER BY date_published DESC LIMIT 5";
    //$result = mysqli_query($conn, $sql);
    //$resultCheck = mysqli_num_rows($result);

    if(!($stmt = $pdo_conn->prepare($sql)) ) {
        echo "Database error";
    } else {
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $title = $row['title'];$row = mysqli_fetch_assoc($result)
            $recipe_active_time = $row['recipe_active_time'];
            $recipe_wait_time = $row['recipe_wait_time'];
            $recipe_serves = $row['recipe_serves'];
            $intro_html = $row['intro_html'];
            $card_img = $website_root . '/' .$row['card_img'];

            $recipe_card =
            '<div class="post_card">
                <div class="recipe_card_img"><a href="'. $website_root .'/recipe_view.php?id='.$id.'"><img src="'.$card_img.'" alt="'.$title.'"></a></div>
                <div class="recipe_card_title_info">
                    <a href="'. $website_root .'/recipe_view.php?id='.$id.'"><h3>'.$title.'</h3></a>
                    <p><span class="no_break"><i class="fas fa-clock"></i> '.$recipe_active_time.' &nbsp;&nbsp; </span>
                    <span class="no_break"><i class="fas fa-bed"></i> '.$recipe_wait_time.' &nbsp;&nbsp; </span>
                    <span class="no_break"><i class="fas fa-utensils"></i> '.$recipe_serves.'</span></p>
                </div>
                <div class="recipe_card_text">'.$intro_html.'</div>
            </div>';

            echo $recipe_card;
        }
    }
?>
<!-- Include link to recipe search with no restrictions -->

<?php include_once 'footer.php' ?>
