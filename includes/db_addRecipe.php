<?php
    include_once 'conn.inc.php';
    include_once 'pdo.inc.php';
    include_once 'admin_validate.inc.php';
    include_once 'parsedown/Parsedown.php';
    include_once 'base_assumptions.inc.php';

    function makeValuesReferenced(&$arr){
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }

    // Check user has actually clicked submit

    if(!isset($_POST['submit'])) {
        header("Location: $website_root/new_recipe.php?err=no_submit");
        exit;
    }

    // Extract username and password for verification

    if(empty($_POST['uid']) || empty($_POST['pwd'])) {
        header("Location: $website_root/new_recipe.php?err=no_creds");
        exit;
    }

    $uid = $_POST['uid'];
    $pwd = $_POST['pwd'];

    if(!checkCredentials($uid,$pwd)) {
        header("Location: $website_root/new_recipe.php?err=bad_creds");
        exit;
    }

    // Should check that required paramaters are set

    // Creating array of data we wish to enter into the new entry

    $data = $_POST;

    // Parseing Markdown into HTML and adding current datetime

    $Parsedown = new Parsedown();
    $data['intro_html']=$Parsedown->text($_POST['intro_md']);
    $data['ingredients_html']=$Parsedown->text($_POST['ingredients_md']);
    $data['method_html']=$Parsedown->text($_POST['method_md']);

    $newURL_enc = rawurlencode($website_root."/recipe_view?id=".$data['nextID']);
    //$data['social_twtr']="http://twitter.com/home?status=" . rawurlencode("Check out this ".$data['title']." on the Ugly Croissant! ") . $newURL_enc;
    $data['social_twtr']="https://twitter.com/intent/tweet?text=". rawurlencode("Check out this ".$data['title']." on the Ugly Croissant")."&url=" .$newURL_enc;
    $data['social_pnt']="http://pinterest.com/pin/create/link/?url=" . $newURL_enc;
    $data['social_snoo']="http://www.reddit.com/submit?url=" . $newURL_enc ."&title=" . rawurlencode("Check out this ".$data['title']);

    unset($data['uid']);
    unset($data['pwd']);
    unset($data['submit']);
    $nextID=$data['nextID'];
    unset($data['nextID']);

    // Getting list of keys for which we have data

    $keys = array_keys($data);

    // Get list of acceptable keys

    $sql = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema=:dbName AND table_name='recipes'";
    $stmt = $pdo_conn->prepare($sql);
    $stmt->bindValue(':dbName',$dbName);
    $stmt->execute();
    $acceptableKeys = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Make sure all keys are acceptable

    foreach ($keys as $key) {
        if(!in_array($key, $acceptableKeys)) {
            header("Location: $website_root/new_recipe.php?err=bad_key&key=$key");
            exit;
            //exit('Key "'.$key.'" was not a recognised column. Potential SQL injection attack.');
        }
    }

    // At this point we know all the columns are safe
    // Probably worth doing some data validation here

    //Now we can prepare an SQL statement to input the recipe into the database

    $sql = "INSERT into recipes (";
    foreach($keys as $key) {
        $sql = $sql.$key.", ";
    }
    $sql = substr($sql,0,-2).") VALUES (";
    foreach($keys as $key) {
        $sql = $sql.":". $key .", ";
    }
    $sql = substr($sql,0,-2).")";


    if ( !($stmt = $pdo_conn->prepare($sql)) ) {
        header("Location: $website_root/new_recipe.php?err=sql_fail");
        exit;
    } else {
        // Bind Paramaters
        foreach($keys as $key) {
            $stmt->bindValue(":".$key,$data[$key]);
        }
        // Submit new recipe
        $stmt->execute();
        // Return to original page for success message and link to new recipe
        header("Location: $website_root/new_recipe.php?id=".$nextID);
    }
