<?php

    /*
        Admin Database PDO object


    $dbHost       = "localhost";
    $creds = parse_ini_file("/home/vwmnpccl/etc/creds.ini");
    $dbName = $creds['dbName'];
    $dbUsername = $creds['adminUser'];
    $dbPassword = $creds['adminPwd'];


    // Set DSN
    $dsn = "mysql:host=".$dbHost.";dbname=".$dbName;

    // Create PDO instance
    try {
        $admin_pdo = new PDO($dsn,$dbUsername,$dbPassword);
    }  catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
    */

    /*
        For testing purposes we use the normal database
    */

    $dbHost       = "localhost";
    $dbUsername   = "root";
    $dbPassword   = "";
    $dbName       = "tuc_develop";

    // Set DSN
    $dsn = "mysql:host=".$dbHost.";dbname=".$dbName;

    // Create PDO instance
    $admin_pdo = new PDO($dsn,$dbUsername,$dbPassword);



    /*
        Admin root
    */

    $admin_root = "localhost/admin";
