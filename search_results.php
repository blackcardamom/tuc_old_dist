<?php
    $selected = "recipes";
    $titleSuffix = " - Search Recipes";
    include_once 'header.php';
    include_once 'includes/Paginator/Paginator.class.php';
    include_once 'includes/pdo.inc.php';

    // SWITCH TO PDO IF ADDING SEARCH PARAMTERS

    $query = "SELECT * FROM recipes";
    $count_query = "SELECT COUNT(*) FROM recipes";
    $total_posts = (int) $pdo_conn->query($count_query)->execute();

    if(!isset($total_posts)) {
        echo "<h1 style='text-align:center; background-color:white; margin:0; padding:30px;'>Sorry we are currently experiencing technical issues.</h1>";
        exit;
    } else {
        $page = (empty($_GET['page'])) ? 1 : $_GET['page'];
        $limit = (empty($_GET['limit'])) ? 5 : $_GET['limit'];
        $paginator = new Paginator($pdo_conn,$query,$total_posts);
        $paginator->updatePage($page,$limit);
    }
?>


<div class="search_results_grid-container">
    <div class="search_results_sidebar">
    </div>
    <div class="search_results_options">
        <div class="limit_select">
            <ul class="horizontal_list options_list">
                <li>Results per page: </li>
                <li class="limit_link"><a href="?<?= $paginator->getNewLimitQuery(5);?>">5</a></li>
                <li class="limit_link"><a href="?<?= $paginator->getNewLimitQuery(10);?>">10</a></li>
                <li class="limit_link"><a href="?<?= $paginator->getNewLimitQuery(15);?>">15</a></li>
            </ul>
        </div>
        <div class="order_select">
            <ul class="horizontal_list options_list">
                <li>Order by: </li>
                <li>
                    <select name="order" class="order_dropdown">
                        <option value="new2old">New -> Old</option>
                    </select>
                </li>
            </ul>
        </div>
    </div>
    <div class="search_results_output">
        <?php
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
        ?>
    </div>
    <div class="search_results_pagination">
        <ul class = "horizontal_list pagination_list">
            <li class="pagination_nav_arrow"><a href="?<?= $paginator->getFirstPageQuery()?>"><i class="fas fa-angle-double-left"></i></a></li>
            <li class="pagination_nav_arrow"><a href="?<?= $paginator->getPrevPageQuery()?>"><i class="fas fa-angle-left"></i></a></li>
            <li class="pagination_ellipses">...</li>
            <?= $paginator->getPagination(5, 'pagination_link', 'current_page'); ?>
            <li class="pagination_ellipses">...</li>
            <li class="pagination_nav_arrow"><a href="?<?= $paginator->getNextPageQuery()?>"><i class="fas fa-angle-right"></i></a></li>
            <li class="pagination_nav_arrow"><a href="?<?= $paginator->getLastPageQuery()?>"><i class="fas fa-angle-double-right"></i></a></li>
        </ul>
    </div>
</div>

<?php include_once 'footer.php'; ?>
