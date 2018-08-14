<?php
    include_once '../includes/pdo.inc.php';
    include_once '../includes/Paginator/Paginator.class.php';

    $base_query = "SELECT * FROM test_table";

    $page = (empty($_GET['page'])) ? 1 : $_GET['page'];
    $limit = (empty($_GET['limit'])) ? 5 : $_GET['limit'];
    $paginator = new Paginator($pdo_conn,$base_query,6);
    $paginator->updatePage($page,$limit);

    while($row = $paginator->fetchNextRow()) {
        foreach($row as $value) {
            echo "$value --- ";
        }
        echo "<br>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<style>
a {
    color: inherit;
    text-decoration: none;
}
.horizontal_list {
    min-width: 696px;
	list-style: none;
	padding-top: 20px;
}

.pagination{
    display: inline;
    padding: 16px;
    background-color: white;
    color:black;
}

.pagination:hover {
    background-color: rgb(175,175,175);
    color:white;
}

.current_page{
    background-color: rgb(84, 148, 249);
}

.current_page a{
    color:white;

}

.limit_link {
    color: rgb(84, 148, 249);
    text-decoration: underline;
}

</style>

<body>
    <ul class = "horizontal_list"><?= $paginator->getPagination(5, 'pagination', 'current_page'); ?></ul>
    <a class="limit_link" href="?<?= $paginator->getNewLimitQuery(1)?>"> 1 per page </a><br>
    <a class="limit_link" href="?<?= $paginator->getNewLimitQuery(2)?>"> 2 per page </a><br>
    <a class="limit_link" href="?<?= $paginator->getNewLimitQuery(3)?>"> 3 per page </a><br>
    <a class="limit_link" href="?<?= $paginator->getNewLimitQuery(5)?>"> 5 per page </a><br>
</body>
</html>
