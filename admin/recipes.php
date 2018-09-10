<?php
    include_once 'header.php';
    $selected = "recipes";
    include_once 'topnav.php';
    session_start();
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }
    if(!isset($_GET['order'])) {
        $_GET['order'] = 0;
    }
?>

<div class="top_options">
    <div class="top_filters">
        <span>Filter:</span>
        <form action="recipes.php" method="GET">
            <input type="text" name="title" id="title_input" placeholder="Recipe title...">
            <input type="text" name="id" id="id_input" placeholder="ID...">
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
    include_once '../includes/pdo.inc.php';
?>
<div class="results_table">
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Actions</th>
        </tr>
    </table>
</div>

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
