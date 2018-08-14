<?php
    $dbHost       = "localhost";
    $dbUsername   = "root";
    $dbPassword   = "";
    $dbName       = "tuc_develop";

    // Set DSN
    $dsn = "mysql:host=$dbHost;dbname=$dbName";

    // Create PDO instance
    $conn_pdo = new PDO($dsn,$dbUsername,$dbPassword);
