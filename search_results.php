<?php

    /*
    Useful query

    SELECT 'recipes' AS origin_table, id, title, intro_html, date_published, recipe_active_time, recipe_wait_time, recipe_serves, card_img FROM recipes
    UNION
    SELECT 'blogposts' AS origin_table, id, title, intro, date_published, '','','','' FROM blogposts
    ORDER BY date_published DESC
    LIMIT 1 OFFSET 2

    Tag query

    SELECT 'recipes' AS origin_table, r.id, r.title, r.intro_html, r.date_published, r.recipe_active_time, r.recipe_wait_time, r.recipe_serves, r.card_img
    FROM MOCK_recipes_tagmap tm, MOCK_DATA r, tags t
    WHERE tm.tag_id = t.id
    AND (t.name IN ('bread', 'cake'))
    AND r.id = tm.recipe_id
    GROUP BY r.id

    UNION

    SELECT 'blogposts' AS origin_table, b.id, b.title, b.intro, b.date_published, '', '', '', ''
    FROM MOCK_blogposts_tagmap tm, MOCK_BLOGS b, tags t
    WHERE tm.tag_id = t.id
    AND (t.name IN ('bread', 'cake'))
    AND b.id = tm.blogpost_id
    GROUP BY b.id

    ORDER BY date_published DESC
    LIMIT 5 OFFSET 10

    */

    $selected = "recipes";
    $titleSuffix = " - Search Recipes";
    include_once 'header.php';
    include_once 'includes/Paginator/Paginator.class.php';
    include_once 'includes/pdo.inc.php';
    include_once 'includes/base_assumptions.inc.php';

    // Setup default values for unspecified paramaters

    $_GET['page'] = (!isset($_GET['page'])) ? 1 : $_GET['page'];
    $_GET['limit'] = (!isset($_GET['limit'])) ? 5 : $_GET['limit'];
    $_GET['order'] = (!isset($_GET['order'])) ? 0 : $_GET['order'];
    $_GET['show'] = (!isset($_GET['show'])) ? 'all' : $_GET['show'];
    $_GET['tag'] = (!isset($_GET['tag'])) ? array() : $_GET['tag'];

    // Setting some flags to help construct the query and sanitizing 'show'

    switch($_GET['show']) {
        case 'all':
            $searchRecipes = true;
            $searchBlogposts = true;
            break;
        case 'recipes':
            $searchRecipes = true;
            $searchBlogposts = false;
            break;
        case 'blogposts':
            $searchRecipes = false;
            $searchBlogposts = true;
            break;
        default:
            echo "<h1 style='text-align:center; background-color:white; margin:0; padding:30px;'>Sorry we are currently experiencing technical issues.</h1>";
            exit;
    }

    // Let's construct the search query
    $master_query = "";

    if($searchRecipes) {
        // Build recipe query
        $master_query .= "SELECT 'recipes' AS origin_table, r.id, r.title, r.intro_html, r.date_published, r.recipe_active_time, r.recipe_wait_time, r.recipe_serves, r.card_img
                          FROM MOCK_recipes_tagmap tm, MOCK_DATA r, tags t
                          WHERE tm.tag_id = t.id ";

        // We only want to add this clause if we have tags
        if(!empty($_GET['tag'])) {
            $master_query .= "AND (t.name IN (";
            $first_tag = true;
            // Loop through tags and add placeholders
            foreach($_GET['tag'] as $num => $tag) {
                if($first_tag) {
                    $master_query .= ":recipe_tag".$num;
                    $first_tag = false;
                } else {
                    $master_query .= ", :recipe_tag".$num;
                }
            }
            $master_query .=")) ";
        }

        $master_query .=  "AND r.id = tm.recipe_id GROUP BY r.id";
    }
    if($searchRecipes && $searchBlogposts) {
        // If we're searching both tables then we need to union the results
        $master_query .= " UNION ";
    }
    if($searchBlogposts) {
        // Build blogpost query
        $master_query .= "SELECT 'blogposts' AS origin_table, b.id, b.title, b.intro, b.date_published, '', '', '', ''
                          FROM MOCK_blogposts_tagmap tm, MOCK_BLOGS b, tags t
                          WHERE tm.tag_id = t.id ";

        // We only want to add this clause if we have tags
        if(!empty($_GET['tag'])) {
            $master_query .= "AND (t.name IN (";
            $first_tag = true;
            // Loop through tags and add placeholders
            foreach($_GET['tag'] as $num => $tag) {
                if($first_tag) {
                    $master_query .= ":blogpost_tag".$num;
                    $first_tag = false;
                } else {
                    $master_query .= ", :blogpost_tag".$num;
                }
            }
            $master_query .=")) ";
        }

        $master_query .= "AND b.id = tm.blogpost_id GROUP BY b.id";
    }

    // Now we need to add the order to sort the posts

    switch ($_GET['order']) {
        case 0:
            $master_query .= " ORDER BY date_published DESC";
            break;
        case 1:
            $master_query .= " ORDER BY date_published ASC";
            break;
        case 2:
            $master_query .= " ORDER BY title ASC";
            break;
        case 3:
            $master_query .= " ORDER BY title DESC";
            break;
        default:
            echo "<h1 style='text-align:center; background-color:white; margin:0; padding:30px;'>Sorry we are currently experiencing technical issues.</h1>";
            exit;
    }

    echo $master_query;


    // Now we need the callback function that binds the actual value of the tags to the prepared statement
    function bindTags($stmt) {
        foreach($_GET['tag'] as $num => $tag) {
            $stmt->bindValue(':recipe_tag'.$num,$_GET['tag'][$num]);
            $stmt->bindValue(':blogpost_tag'.$num,$_GET['tag'][$num]);
        }
    }


    $paginator = new Paginator($pdo_conn,$master_query, 'bindTags');
    $paginator->updatePage($_GET['page'],$_GET['limit']);

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
            echo "Hi";
            print_r($row);
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
                    <p><span class="no_break"><i class="fas fa-clock"></i> '.$recipe_active_time.' &nbsp;&nbsp; </span>
                    <span class="no_break"><i class="fas fa-bed"></i> '.$recipe_wait_time.' &nbsp;&nbsp; </span>
                    <span class="no_break"><i class="fas fa-utensils"></i> '.$recipe_serves.' </span></p>
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
