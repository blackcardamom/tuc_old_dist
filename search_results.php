<?php

    /*
    Useful query

    SELECT 'recipes' AS origin_table, id, title, intro_html, date_published, recipe_active_time, recipe_wait_time, recipe_serves, card_img FROM recipes
    UNION
    SELECT 'blogposts' AS origin_table, id, title, intro, date_published, '','','','' FROM blogposts
    ORDER BY date_published DESC
    LIMIT 1 OFFSET 2
    */

    $selected = "recipes";
    $titleSuffix = " - Search Recipes";
    include_once 'header.php';
    include_once 'includes/Paginator/Paginator.class.php';
    include_once 'includes/pdo.inc.php';
    include_once 'includes/base_assumptions.inc.php';

    // Setup default values for unspecified paramaters

    $_GET['page'] = (empty($_GET['page'])) ? 1 : $_GET['page'];
    $_GET['limit'] = (empty($_GET['limit'])) ? 5 : $_GET['limit'];
    $_GET['order'] = (empty($_GET['order'])) ? 0 : $_GET['order'];

    switch ($_GET['order']) {
        case 0:
            $query = "SELECT * FROM recipes ORDER BY date_published DESC";
            break;
        case 1:
            $query = "SELECT * FROM recipes ORDER BY date_published ASC";
            break;
        case 2:
            $query = "SELECT * FROM recipes ORDER BY title ASC";
            break;
        case 3:
            $query = "SELECT * FROM recipes ORDER BY title DESC";
            break;
        default:
            echo "<h1 style='text-align:center; background-color:white; margin:0; padding:30px;'>Sorry we are currently experiencing technical issues.</h1>";
            exit;
    }

    // SWITCH TO PDO IF ADDING SEARCH PARAMTERS

    $count_query = "SELECT COUNT(*) FROM recipes";
    $stmt = $pdo_conn->query($count_query);
    $stmt->execute();
    $total_posts = $stmt->fetch()['COUNT(*)'];

    if(!isset($total_posts)) {
        echo "<h1 style='text-align:center; background-color:white; margin:0; padding:30px;'>Sorry we are currently experiencing technical issues.</h1>";
        exit;
    } elseif ($total_posts  == 0) {
        echo "<h1 style='text-align:center; background-color:white; margin:0; padding:30px;'>Sorry we don't have any posts to show you.</h1>";
        exit;
    } else {
        $paginator = new Paginator($pdo_conn,$query,$total_posts);
        $paginator->updatePage($_GET['page'],$_GET['limit']);
    }
?>


<div class="search_results_grid-container">
    <div class="search_results_sidebar">
    </div>
    <div class="search_results_options">
        <div class="limit_select">
            <ul class="horizontal_list options_list">
                <li>Results per page: &nbsp;&nbsp;</li>
                <li class="limit_link"><a href="?<?= $paginator->getNewLimitQuery(5);?>">5</a></li>
                <li class="limit_link"><a href="?<?= $paginator->getNewLimitQuery(10);?>">10</a></li>
                <li class="limit_link"><a href="?<?= $paginator->getNewLimitQuery(15);?>">15</a></li>
            </ul>
        </div>
        <div class="order_select">
            <ul class="horizontal_list options_list">
                <li>Order by: &nbsp;&nbsp;</li>
                <li>
                    <select name="order" class="order_dropdown" onchange="updateOrder()" id="order_dropdown">
                        <option value="0" <?= ($_GET['order']==0) ? "selected" : null ?>>New -> Old</option>
                        <option value="1" <?= ($_GET['order']==1) ? "selected" : null ?>>Old -> New</option>
                        <option value="2" <?= ($_GET['order']==2) ? "selected" : null ?>>Title a -> z</option>
                        <option value="3" <?= ($_GET['order']==3) ? "selected" : null ?>>Title z -> a</option>
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
            $card_img = $website_root . "/" . $row['card_img'];

            $recipe_card =
            '<div class="post_card">
                <div class="recipe_card_img"><a href="'. $website_root .'/recipe_view.php?id='.$id.'"><img src="'.$card_img.'" alt="'.$title.'"></a></div>
                <div class="recipe_card_title_info">
                    <a href="'. $website_root .'/recipe_view.php?id='.$id.'"><h3>'.$title.'</h3></a>
                    <p><i class="fas fa-clock"></i> '.$recipe_active_time.' &nbsp;&nbsp; <br id="mobile_linebreak">
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

<script>

// From https://stackoverflow.com/a/5158301
function getParameterByName(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}

function updateOrder() {
    var dropdown = document.getElementById("order_dropdown");
    var newOrder = dropdown.options[dropdown.selectedIndex].value;
    var page = getParameterByName('page');
    var limit = getParameterByName('limit');
    if (page == null) { page = 1; }
    if (limit == null) { limit = 5; }
    var query = "?page=" + page + "&limit=" + limit + "&order=" + newOrder;
    document.location.search = query;
}

</script>

<?php include_once 'footer.php'; ?>
