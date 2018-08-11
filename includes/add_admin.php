<?php
    include_once 'conn.inc.php';

    $uid = 'beansprout';
    $pwd = 'buttons_tucadmin#18';
    $first_name = 'Nikki';
    $last_name = 'Easton';

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
