<?php
    include_once 'conn.inc.php';
    include_once 'admin_validate.inc.php';
    include_once 'parsedown/Parsedown.php';

    function makeValuesReferenced(&$arr){
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }

    // Extract username and password for verification

    if(empty($_POST['uid']) || empty($_POST['pwd'])) {
        exit('Please provide all login credentials');
    }

    $uid = $_POST['uid'];
    $pwd = $_POST['pwd'];

    if(!checkCredentials($uid,$pwd)) {
        exit('Credentials incorrect please try again');
    }

    // Creating array of data we wish to enter into the new entry

    $data = $_POST;
    unset($data['uid']);
    unset($data['pwd']);

    // Parseing Markdown into HTML and adding current datetime

    $Parsedown = new Parsedown();
    $data['intro_html']=$Parsedown->text($_POST['intro_md']);
    $data['ingredients_html']=$Parsedown->text($_POST['ingredients_md']);
    $data['method_html']=$Parsedown->text($_POST['method_md']);
    date_default_timezone_set('Greenwich');
    $data['date_published'] = date('Y-m-d H:i:s', time());

    // Getting list of keys for which we have data

    $keys = array_keys($data);

    // Get list of acceptable keys

    $sql = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='$dbName' AND table_name='recipes'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck == 0) {
        echo "No acceptable keys returned from datbase";
    } else {
        $acceptableKeys = Array();
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($acceptableKeys, $row['COLUMN_NAME']);
        }
    }

    // Make sure all keys are acceptable

    foreach ($keys as $key) {
        if(!in_array($key, $acceptableKeys)) {
            exit('Key "'.$key.'" was not a recognised column. Potential SQL injection attack.');
        }
    }

    // At this point we know all the columns are safe so we can prepare an SQL statement to input the recipe into the database

    $format = "";
    $sql = "INSERT into recipes (";
    foreach($keys as $key) {
        $sql = $sql.$key.", ";
    }
    $sql = substr($sql,0,-2).") VALUES (";
    foreach($keys as $key) {
        $sql = $sql."?, ";
        $format = $format."s";
    }
    $sql = substr($sql,0,-2).")";

    $stmt = mysqli_stmt_init($conn);
    array_unshift($data, $stmt, $format);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
        echo "SQL statement failed to prepare";
    } else {
        call_user_func_array("mysqli_stmt_bind_param",makeValuesReferenced($data));
        mysqli_stmt_execute($stmt);
        $sql = "SELECT LAST_INSERT_ID();";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $id = $row['LAST_INSERT_ID()'];
        header("Location: ../recipe_view.php?id=".$id);
    }
