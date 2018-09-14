<?php
    include_once 'admin.inc.php';

    $sql = 'SELECT * FROM tags WHERE name LIKE :query ORDER BY name ASC';
    $stmt = $admin_pdo->prepare($sql);
    $stmt->bindValue('query','%'.$_GET['q'].'%');
    $stmt->execute();
    $postedSomething = false;
    $foundCurrentTag = false;

    echo "<table class='search_results_table'>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $postedSomething = true;
        if($row['name'] === $_GET['q']) {
            $foundCurrentTag = true;
        }
        echo "<tr><td><a onclick='addTag(".'"'.$row['name'].'"'.")'>".$row['name']."</a></td></tr>";
    }

    if(!$foundCurrentTag) {
        echo "<tr><td class='new_tag'><a onclick='addTag(".'"'.$_GET['q'].'"'.")'>".$_GET['q']."</a></td></tr>";
    }

    echo "</table>";
