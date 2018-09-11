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

    include_once 'includes/Paginator/Paginator.class.php';
    include_once 'includes/pdo.inc.php';
    include_once 'includes/base_assumptions.inc.php';

    // Setup default values for unspecified paramaters

    $_GET['page'] = (!isset($_GET['page'])) ? 1 : $_GET['page'];
    $_GET['limit'] = (!isset($_GET['limit'])) ? 5 : $_GET['limit'];
    $_GET['order'] = (!isset($_GET['order'])) ? 0 : $_GET['order'];
    $_GET['search'] = (!isset($_GET['search'])) ? 'all' : $_GET['search'];
    $_GET['tag'] = (!isset($_GET['tag'])) ? array() : $_GET['tag'];

    // Setting some flags to help construct the query and sanitizing 'search'

    $searchRecipes = ($_GET['search'] === "all") || ($_GET['search'] === "recipes");
    $searchBlogposts = ($_GET['search'] === "all") || ($_GET['search'] === "blogposts");

    // Including header

    if ($searchRecipes && $searchBlogposts) {
        $selected = "";
        $titleSuffix = " - Search site";
    } elseif ($searchRecipes) {
        $selected = "recipes";
        $titleSuffix = " - Search recipes";
    } elseif ($searchBlogposts) {
        $titleSuffix = " - Search blogposts";
    }
    include_once 'header.php';


    // Let's construct the search query
    $master_query = "";

    if($searchRecipes) {
        // Build recipe query
        $master_query .= "SELECT 'recipes' AS origin_table, r.id, r.title, r.intro_html AS intro, r.date_published, r.recipe_active_time, r.recipe_wait_time, r.recipe_serves, r.card_img
                          FROM recipes_tagmap tm, recipes r, tags t ";

        // We only want to add this clause if we have tags
        if(!empty($_GET['tag'])) {
            $master_query .= "WHERE tm.tag_id = t.id AND (t.name IN (";
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
            $master_query .=")) AND r.id = tm.recipe_id ";
        }

        $master_query .=  "GROUP BY r.id";
    }
    if($searchRecipes && $searchBlogposts) {
        // If we're searching both tables then we need to union the results
        $master_query .= " UNION ";
    }
    if($searchBlogposts) {
        // Build blogpost query
        $master_query .= "SELECT 'blogposts' AS origin_table, b.id, b.title, b.intro AS intro, b.date_published,  '' AS recipe_active_time, '' AS recipe_wait_time, '' AS recipe_serves, '' AS card_img
                          FROM blogposts_tagmap tm, blogposts b, tags t ";

        // We only want to add this clause if we have tags
        if(!empty($_GET['tag'])) {
            $master_query .= "WHERE tm.tag_id = t.id AND (t.name IN (";
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
            $master_query .=")) AND b.id = tm.blogpost_id ";
        }

        $master_query .= "GROUP BY b.id";
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

    // Now we need the callback function that binds the actual value of the tags to the prepared statement
    function bindTags($stmt) {
        foreach($_GET['tag'] as $num => $tag) {
            if ($GLOBALS['searchRecipes']) {
                $stmt->bindValue("recipe_tag".$num,$tag);
            }
            if ($GLOBALS['searchBlogposts']) {
                $stmt->bindValue("blogpost_tag".$num,$tag);
            }
        }
    }

    // Now we pass this query onto our paginator

    $paginator = new Paginator($pdo_conn,$master_query, 'bindTags');
    $paginator->updatePage($_GET['page'],$_GET['limit']);

    // We need a list of all possible tags and how many recipes have that tag in order to fill out the filters sidebar

    // NEED TO CHANGE TAGS BASED ON CURRENT SEARCHING


    $tag_table = "";
    if ($searchRecipes) {
        $tag_table .= "SELECT 'recipes' AS tagging, tm_id,tag_id FROM recipes_tagmap";
    }
    if ($searchRecipes && $searchBlogposts) {
        $tag_table .= " UNION ";
    }
    if ($searchBlogposts) {
        $tag_table .= "SELECT 'blogposts' AS tagging, tm_id,tag_id FROM blogposts_tagmap";
    }
    $tag_query =   "SELECT a.tag_id, b.name, COUNT(*) FROM
                    (".$tag_table.") a, tags b
                    WHERE a.tag_id=b.id
                    GROUP BY tag_id
                    ORDER BY COUNT(*) DESC";
    $tag_stmt = $pdo_conn->prepare($tag_query);
    $tag_stmt->execute();

    // Helpful functions to create user interface

    function addTagToQuery($name) {
        $getCopy=$_GET;
        // Add new tag to the tags array
        array_push($getCopy["tag"],$name);
        // Return to page 1
        $getCopy['page'] = 1;
        return http_build_query($getCopy);
    }

    function removeTagFromQuery($index) {
        $getCopy=$_GET;
        // Remove tag at $index from the tags array
        array_splice($getCopy['tag'],$index,1);
        // Return to page 1
        $getCopy['page'] = 1;
        return http_build_query($getCopy);
    }

    // Now we just need a session variable to keep the filters open when adding tags in mobile
    if(!isset($_COOKIE['sidebar_open'])) {
        setcookie('sidebar_open','false',time() + 3600, "/");
    }

?>


<div class="search_results_grid-container<?= $_COOKIE['sidebar_open']==="true" ? " sidebar_open" : "" ?>" id="search_grid">
    <div class="search_results_sidebar">
        <div class="search_results_sidebar_title" onclick="toggleSearchSidebar()" id="search_results_sidebar_title">
            <h3>Refine search <i class="fas fa-caret-<?= $_COOKIE['sidebar_open']==="true" ? "up" : "down" ?>" id="sidebar_toggle"></i> </h3>
        </div>
        <div class="search_results_showing">
            <strong>Showing: </strong><br>
            <input type="checkbox" id="recipes_check" onclick="updateSearching()" name="recipes"<?= $searchRecipes ? "checked" : null ?>> Recipes <br>
            <input type="checkbox" id="blogposts_check" onclick="updateSearching()" name="blogposts"<?= $searchBlogposts ? "checked" : null ?>> Blogposts
        </div>
        <strong>Filters: </strong><br>
        <div class="search_results_filters">
            <div class="search_results_active_filters">
                <?php foreach($_GET['tag'] as $index => $tag) : ?>
                    <div class="active_filter">
                        <?= $tag ?>
                        <a href="?<?= removeTagFromQuery($index) ?>"><i class="fas fa-times-circle"></i></a>
                    </div>
                <?endforeach; ?><br>
            </div>
            <div class="search_results_inactive_filters">
            <?php while ($row = $tag_stmt->fetch(PDO::FETCH_ASSOC)) :
                if (!in_array($row['name'], $_GET['tag'])) : ?>
                <a href="?<?= addTagToQuery($row['name']) ?>"><span class="inactive_filter"><?= $row['name'] ." (".$row['COUNT(*)'].")" ?></span></a>
            <?php endif; endwhile; ?>
            </div>
        </div>
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
            if($row['origin_table'] === "recipes") {
                $id = $row['id'];
                $title = $row['title'];
                $recipe_active_time = $row['recipe_active_time'];
                $recipe_wait_time = $row['recipe_wait_time'];
                $recipe_serves = $row['recipe_serves'];
                $intro = $row['intro'];
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
                    <div class="recipe_card_text">'.$intro.'</div>
                </div>';

                echo $recipe_card;
            } elseif($row['origin_table'] === "blogposts") {
                $post_card = "
                <div class='post_card'>
                    <h3><a href='$website_root/blogpost_view.php?id=".$row['id']."'>".$row['title']."</a></h3>
                    <p><span class='blog_date_published'>". date('jS F Y.',strtotime($row['date_published'])) ."</span> ".$row['intro']."</p>
                    <div class='blog_link'><a href='$website_root/bloindex.phpgpost_view.php?id=".$row['id']."'><i class='fas fa-plus-circle'></i> Read more...</a></div>
                </div>";
                echo $post_card;
            }
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

console.log(queryToObject(window.location.search));

function queryToObject(query) {
    var outObj = {};
    var expr = new RegExp('[^?&]+', 'g');
    while( match = expr.exec(query) ) {
        // We have found a key-value pair in the query so we split and decode it
        var splitMatch = match[0].split("=");
        var key = decodeURIComponent(splitMatch[0]);
        var value = decodeURIComponent(splitMatch[1]);
        // We need to check if its an array item
        var bracketExpr = new RegExp("\\[.*\\]",'g');
        if(bracketExpr.test(key)) {
            // We need to figure out the name of the array
            var arrayName = key.slice(0,key.search(bracketExpr));
            // We need to check the array exists
            if (!Array.isArray(outObj[arrayName])) {
                outObj[arrayName] = [];
            }
            // Now we need to find what position in the array we are
            var subkey = key.match(bracketExpr)[0].slice(1,-1)
            // If there isn't a subkey (i.e. arrayName[]=value) then we just push to the array
            if (subkey== "") {
                outObj[arrayName].push(value);
            } else {
                outObj[arrayName][subkey]=value;
            }
        } else {
            // If its not an array item we just add it to the object
            outObj[key]=value;
        }
    }
    return outObj;
}

function objectToQuery(obj) {
    var outStr = "?";
    var first_loop = true;
    for(var key in obj) {
        if(Array.isArray(obj[key])) {
            for(var subkey in obj[key]) {
                outStr += key+"["+subkey+"]="+encodeURIComponent(obj[key][subkey])+"&";
            }
        } else {
            outStr += key+"="+encodeURIComponent(obj[key])+"&";
        }
    }
    return outStr.slice(0,-1);
}

function updateOrder() {
    var dropdown = document.getElementById("order_dropdown");
    var newOrder = dropdown.options[dropdown.selectedIndex].value;
    var queryObj = queryToObject(window.location.search);
    queryObj['order'] = newOrder;
    // After changing search order return to page 1
    queryObj['page'] = 1;
    window.location.search = objectToQuery(queryObj);
}

function updateSearching() {
    var queryObj = queryToObject(window.location.search);
    var recipes = document.getElementById("recipes_check").checked;
    var blogposts = document.getElementById("blogposts_check").checked;
    if (recipes && blogposts) {
        queryObj['search'] = 'all';
    } else if (recipes) {
        queryObj['search'] = 'recipes';
    } else if (blogposts) {
        queryObj['search'] = 'blogposts';
    } else{
        queryObj['search'] = 'none';
    }
    // After changing search radius return to page 1
    queryObj['page'] = 1;
    window.location.search = objectToQuery(queryObj);
}

// setCookie taken from https://www.w3schools.com/js/js_cookies.asp
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function toggleSearchSidebar() {
    var x = document.getElementById("search_grid");
    var y = document.getElementById("sidebar_toggle");
    if (x.className === "search_results_grid-container") {
        x.className += " sidebar_open";
        y.className = "fas fa-caret-up";
        setCookie("sidebar_open","true",1)
    } else {
        x.className = "search_results_grid-container";
        y.className = "fas fa-caret-down";
        setCookie("sidebar_open","false",1)
    }
}

</script>

<?php include_once 'footer.php'; ?>
