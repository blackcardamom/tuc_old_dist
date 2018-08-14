<?php
    include_once '../includes/pdo.inc.php';
    include_once '../includes/Paginator/Paginator.class.php';

    function bindValues($stmt) {
        $stmt->bindValue(':author', 'Tom');
    }

    $base_query = "SELECT * FROM test_table WHERE author = :author";

    $paginator = new Paginator($pdo_conn,$base_query,'bindValues',6);
    $paginator->updatePage(2,1);

    while($row = $paginator->fetchNextRow()) {
        print_r($row);
        echo "<br>";
    }
