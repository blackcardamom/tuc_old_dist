<?php
    include_once '../includes/pdo.inc.php';
    $sql = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema=:dbName AND table_name='blogposts'";
    $stmt = $pdo_conn->prepare($sql);
    $stmt->bindValue(':dbName',$dbName);
    $stmt->execute();
    $keys = $stmt->fetchAll(PDO::FETCH_COLUMN);
    print_r($keys);
?>
