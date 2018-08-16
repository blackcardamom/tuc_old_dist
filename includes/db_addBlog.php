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

    // Check user has actually clicked submit

    if(!isset($_POST['submit'])) {
        header("Location: ../new_blog.php?err=no_submit");
        exit;
    }

    // Extract username and password for verification

    if(empty($_POST['uid']) || empty($_POST['pwd'])) {
        header("Location: ../new_blog.php?err=no_creds");
        exit;
    }

    $uid = $_POST['uid'];
    $pwd = $_POST['pwd'];

    if(!checkCredentials($uid,$pwd)) {
        header("Location: ../new_blog.php?err=bad_creds");
        exit;
    }

    // Should check that required paramaters are set

    // Creating array of data we wish to enter into the new entry

    $data = $_POST;

    // Parseing Markdown into HTML and adding current datetime

    $Parsedown = new Parsedown();
    $data['intro_html']=$Parsedown->text($_POST['intro_md']);
    $data['content_html']=$Parsedown->text($_POST['content_md']);

    $newURL_enc = rawurlencode("http://www.theuglycroissant.com/recipe_view?id=".$data['nextID']);
    $data['social_twtr']="http://twitter.com/home?status=" . rawurlencode("Check out this ".$data['title']." on the Ugly Croissant! ") . $newURL_enc;
    $data['social_pnt']="http://pinterest.com/pin/create/link/?url=" . $newURL_enc;
    $data['social_snoo']="http://www.reddit.com/submit?url=" . $newURL_enc ."&title=" . rawurlencode("Check out this ".$data['title']);

    unset($data['uid']);
    unset($data['pwd']);
    unset($data['submit']);
    unset($data['nextID']);

    // Getting list of keys for which we have data

    $keys = array_keys($data);

    // Get list of acceptable keys

    $sql = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='$dbName' AND table_name='blogposts'";
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
            header("Location: ../new_blog.php?err=bad_key&key=$key");
            exit;
            //exit('Key "'.$key.'" was not a recognised column. Potential SQL injection attack.');
        }
    }

    // At this point we know all the columns are safe
    // Probably worth doing some data validation here

    //Now we can prepare an SQL statement to input the recipe into the database

    $format = "";
    $sql = "INSERT into blogposts (";
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
    // Add the statement and the format to the array of arguments for mysqli_stmt_bind_param
    array_unshift($data, $stmt, $format);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
        header("Location: ../new_blog.php?err=sql_fail");
        exit;
    } else {
        // Bind Paramaters
        call_user_func_array("mysqli_stmt_bind_param",makeValuesReferenced($data));
        // Submit new recipe
        mysqli_stmt_execute($stmt);
        // Determine id of new recipe
        $sql = "SELECT LAST_INSERT_ID();";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $id = $row['LAST_INSERT_ID()'];
        // Return to original page for success message and link to new recipe
        header("Location: ../new_blog.php?id=".$id);
    }
