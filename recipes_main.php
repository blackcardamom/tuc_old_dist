<?php
    $selected = "recipes";
    $titleSuffix=" - Recipes";
    include_once 'header.php';
    include_once 'includes/conn.inc.php';

?>

<div class="container">
    <img src="assets/Celebration_cake_crop.jpg">
    <div class="bottom-right">Cakes</div>
    <a href=#cakes>
        <div class="container_overlay"></div>
    </a>
</div>
<div class="container">
    <img src="assets/tart_crop.JPG">
    <div class="bottom-right">Tarts</div>
    <a href=#tarts>
        <div class="container_overlay"></div>
    </a>
</div>
<div class="container">
    <img src="assets/cookies_crop.JPG">
    <div class="bottom-right">Biscuits</div>
    <a href=#chocolate>
        <div class="container_overlay"></div>
    </a>
</div>
<div class="container">
    <img src="assets/Bread_crop.jpg">
    <div class="bottom-right">Other</div>
    <a href=#other>
        <div class="container_overlay"></div>
    </a>
</div>
<div class="my_content">
    <h1>Recent Recipes</h1>
</div>
<div class="card_wrapper">
<?php
    $sql = "SELECT * FROM recipes ORDER BY date_published DESC LIMIT 5";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);

    if($resultCheck == 0) {
        echo "No recipes returned from datbase";
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $title = $row['title'];
            $recipe_active_time = $row['recipe_active_time'];
            $recipe_wait_time = $row['recipe_wait_time'];
            $recipe_serves = $row['recipe_serves'];
            $intro_html = $row['intro_html'];
            $card_img = $row['card_img'];

            $recipe_card =
            '<div class="post_card">
                <div class="recipe_card_img"><a href="recipe_view.php?id='.$id.'"><img src="'.$card_img.'" alt="'.$title.'"></a></div>
                <div class="recipe_card_title_info">
                    <a href="recipe_view.php?id='.$id.'"><h3>'.$title.'</h3></a>
                    <p><i class="fas fa-clock"></i> '.$recipe_active_time.' &nbsp;&nbsp;
                    <i class="fas fa-bed"></i> '.$recipe_wait_time.' &nbsp;&nbsp; <br id="mobile_linebreak">
                    <i class="fas fa-utensils"></i> '.$recipe_serves.'</p>
                </div>
                <div class="recipe_card_text">'.$intro_html.'</div>
            </div>';

            echo $recipe_card;
        }
    }
?>
</div>
<!-- Include link to recipe search with no restrictions -->

<?php include_once 'footer.php' ?>
