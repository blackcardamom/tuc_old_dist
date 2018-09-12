<?php
    $dbHost       = "localhost";
    $creds = parse_ini_file("/home/vwmnpccl/etc/creds.ini");
    $dbName = $creds['dbName'];
    $dbUsername = $creds['dbUser'];
    $dbPassword = $creds['dbPwd'];


    // Set DSN
    $dsn = "mysql:host=".$dbHost.";dbname=".$dbName;

    // Create PDO instance
    try {
        $pdo_conn = new PDO($dsn,$dbUsername,$dbPassword);
    }  catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
