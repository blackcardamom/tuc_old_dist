<?php
    include_once 'conn.inc.php';

    // Returns 1 if user exists otherwise returns 0
    function userExists($uid) {
        $sql = "SELECT * FROM users WHERE uid=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt,$sql)) {
            echo "SQL statement failed to prepare";
        } else {
            mysqli_stmt_bind_param($stmt,'s',$uid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($conn, $sql);
            return mysqli_num_rows($result);
        }
    }

    // Checks username and password against the database
    // Returns 1 if credentials agree with an entry in the database
    // Otherwise returns 0
    function checkCredentials($uid, $pwd) {
        if (userExists($uid)) {
            $sql = "SELECT pwd FROM users WHERE uid=?";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt,$sql)) {
                echo "SQL statement failed to prepare";
            } else {
                mysqli_stmt_bind_param($stmt,'s',$uid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $server_pwd_response = $row['pwd'];
                return password_verify($pwd, $server_pwd_response);
            }
        } else {
            return 0;
        }
    }

}
