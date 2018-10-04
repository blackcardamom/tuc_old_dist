<?php session_start();
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }
    include_once 'header.php';
    $selected = "blogposts";
    include_once 'topnav.php';


    if(!isset($_GET['order'])) {
        $_GET['order'] = 0;
    }
    if(!isset($_GET['page'])) {
        $_GET['page'] = 1;
    }
?>

<div class="top_options">
    <div class="top_filters">
        <span>Filter:</span>
        <form action="recipes.php" method="GET">
            <input type="text" name="title" id="title_input" placeholder="Recipe title..."<?= (!empty($_GET['title'])) ? " value='".$_GET['title']."'" : "" ?>>
            <input type="text" name="id" id="id_input" placeholder="ID..."<?= (!empty($_GET['id'])) ? " value='".$_GET['id']."'" : "" ?>>
            <button type="submit" name="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="top_sort">
        <span>Sort:</span>
        <select name="order" class="order_dropdown" onchange="updateOrder()" id="order_dropdown">
            <option value="0" <?= ($_GET['order']==0) ? "selected" : null ?>>New -> Old</option>
            <option value="1" <?= ($_GET['order']==1) ? "selected" : null ?>>Old -> New</option>
            <option value="2" <?= ($_GET['order']==2) ? "selected" : null ?>>Title a -> z</option>
            <option value="3" <?= ($_GET['order']==3) ? "selected" : null ?>>Title z -> a</option>
        </select>
    </div>
</div>
<?php
    include_once 'admin.inc.php';
    include_once '../includes/Paginator/Paginator.class.php';

    // We figure out the master query

    $master_query = "SELECT * FROM blogposts";
    $searchTitle = !empty($_GET['title']);
    $searchID = !empty($_GET['id']);

    // Search parameters

    if($searchTitle || $searchID) {
        $master_query .= " WHERE";
    }
    if($searchTitle) {
        $master_query .= " title LIKE :title";
    }
    if($searchTitle && $searchID) {
        $master_query .= " AND";
    }
    if($searchID) {
        $master_query .= " id = :id";
    }

    // Order
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

    // Parameter bind callback

    function blogpostSearchCallback($stmt) {
        if (!empty($_GET['title'])) {
            $stmt->bindValue('title','%'.$_GET['title'].'%');
        }
        if (!empty($_GET['id'])) {
            $stmt->bindValue('id',$_GET['id']);
        }
    }

    // We create our paginator object and update the page

    $paginator = new Paginator($admin_pdo, $master_query, 'blogpostSearchCallback');
    $paginator->updatePage($_GET['page'],20);                                            // Hard coding in 5 results per page

?>
<div class="results_table_wrapper">
    <table>
        <tr class="results_table_header">
            <th>ID</th>
            <th>Title</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $paginator->fetchNextRow()) : ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['title'] ?></td>
            <td class="actions_cell"><a href="edit_post.php?type=blogpost&id=<?= $row['id'] ?>"><i class="fas fa-pencil-alt"></i></a> &nbsp; &nbsp;
                 <a href="delete_post.php?type=blogpost&id=<?= $row['id'] ?>"><i class="fas fa-trash-alt"></i></a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<div class="pagination_wrapper">
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
<div class="new_post_button"><a href="new_post.php?type=blogpost">+</a></div>

<script>

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
    window.location.search = objectToQuery(queryObj);
}

</script>

<?php include_once 'footer.php'; ?>
