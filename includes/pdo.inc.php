<?php
    $dbHost       = "localhost";
    $dbUsername   = "root";
    $dbPassword   = "";
    $dbName       = "tuc_develop";

    // Set DSN
    $dsn = "mysql:host=".$dbHost.";dbname=".$dbName;

    // Create PDO instance
    $pdo_conn = new PDO($dsn,$dbUsername,$dbPassword);
