<?php

include "connect.php";

$user_is_admin = false;
$security_error = "";

function isAdmin() {
    global $dbh;
    global $user_is_admin;
    global $security_error;
    if (isset($_SESSION["userID"])) {
        try {
            $userStmt = $dbh->prepare("
            SELECT admin
            FROM users
            WHERE userID = ?
            LIMIT 1
        ");
            $userStmt->execute([$_SESSION["userID"]]);
            $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);

            if ($currentUser["admin"] == 1) {
                $user_is_admin = true;
            }
        } catch (Exception $e) {
            $server_error = "Server error";
        }
    }
    
    if (!$user_is_admin){
        $security_error = "Security constraints prevent you from viewing this page.";
    }
    return $user_is_admin;
}
?>