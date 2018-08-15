<?php
    $selected = "recipes";
    $titleSuffix = " - Search Recipes";
    include_once 'header.php';
    include_once 'includes/Paginator/Paginator.class.php';
    include_once 'includes/pdo.inc.php';
?>


<div class="search_results_grid-container">
    <div class="search_results_sidebar">
    </div>
    <div class="search_results_options">
    </div>
    <div class="search_results_output">
        <?php
        // SWITCH TO PDO IF ADDING SEARCH PARAMTERS

        $query = "SELECT * FROM recipes";
        $count_query = "SELECT COUNT(*) FROM recipes";
        $total_posts = (int) $pdo_conn->query($count_query)->execute();

        if(!isset($total_posts)) {
            echo "<h1 style='text-alight:center'>Sorry we are currently experiencing technical issues.</h1>";
        } else {
            $page = (empty($_GET['page'])) ? 1 : $_GET['page'];
            $limit = (empty($_GET['limit'])) ? 5 : $_GET['limit'];
            $paginator = new Paginator($pdo_conn,$query,$total_posts);
            $paginator->updatePage($page,$limit);

            while($row = $paginator->fetchNextRow()) {
                $id = $row['id'];
                $title = $row['title'];
                $recipe_active_time = $row['recipe_active_time'];
                $recipe_wait_time = $row['recipe_wait_time'];
                $recipe_serves = $row['recipe_serves'];
                $intro_html = $row['intro_html'];
                $card_img = $row['card_img'];

                $recipe_card =
                '<div class="recipe_card">
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
    <div class="search_results_pagination">
        <ul class = "horizontal_list"><?= $paginator->getPagination(5, 'pagination_link', 'current_page'); ?></ul>
    </div>
</div>

<?php include_once 'footer.php'; ?>
