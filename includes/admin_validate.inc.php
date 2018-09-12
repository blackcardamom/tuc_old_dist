<?php
    include_once 'pdo.inc.php';
    include_once 'conn.inc.php';

    // Returns true if user exists otherwise returns false
    // If the statement fails to prepare then we return -1
    function userExists($uid) {
        $pdo_conn = $GLOBALS['pdo_conn'];
        $sql = "SELECT * FROM users WHERE uid=:uid";
        if ( !($stmt = $pdo_conn->prepare($sql)) ) {
            echo "SQL statement failed to prepare";
            return -1;
        } else {
            $stmt->bindValue(':uid',$uid);
            $stmt->execute();
            if(!($result = $stmt->fetch())) {
                return false;
            } else {
                return true;
            }
        }
    }

    // Checks username and password against the database
    // Returns 1 if credentials agree with an entry in the database
    // Otherwise returns 0
    function checkCredentials($uid, $pwd) {
        $pdo_conn = $GLOBALS['pdo_conn'];
        $uidExists = userExists($uid);
        if ($uidExists === -1) {
            return -1;
        } elseif ($uidExists) {
            $sql = "SELECT pwd FROM users WHERE uid=:uid";
            if ( !($stmt = $pdo_conn->prepare($sql)) ) {
                echo "SQL statement failed to prepare";
                return -1;
            } else {
                $stmt->bindValue(':uid',$uid);
                $stmt->execute();
                $server_pwd_response = $stmt->fetch(PDO::FETCH_ASSOC)['pwd'];
                return password_verify($pwd, $server_pwd_response);
            }
        } else {
            return 0;
        }
    }
