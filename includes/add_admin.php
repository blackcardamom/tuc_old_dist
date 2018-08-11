<?php
    include_once 'conn.inc.php';

    /*

    //
    // Remove block quotes, fill out variables and navigate this page in order to add an admin account
    //

    $uid = '';
    $pwd = '';
    $first_name = '';
    $last_name = '';

    $pwd_hash = password_hash($pwd, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (uid, pwd, first_name, last_name) VALUES ('$uid', '$pwd_hash', '$first_name', '$last_name')";
    mysqli_query($conn, $sql);


    $sql = "SELECT pwd FROM users WHERE uid='$uid'";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($result);
    $server_pwd_response = $row['pwd'];
    if (password_verify($pwd, $server_pwd_response)) {
        echo "User $uid succesfuly created";
    } else {
        echo "Couldn't verify password";
    }
    
    */
