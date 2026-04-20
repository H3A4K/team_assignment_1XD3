<?php
/**
 * security.php
 *
 * Admin authorization helper. Exposes isAdmin() so admin pages can decide
 * whether to render their admin UI or show a "not allowed" message. Also
 * publishes the globals $user_is_admin and $security_error used by the
 * admin page templates.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 18, 2026
 */

include "connect.php";

$user_is_admin = false;
$security_error = "";

/**
 * Checks whether the currently logged-in user has admin privileges.
 *
 * Reads the userID from $_SESSION, looks up the matching row in the accounts
 * table, and sets the global $user_is_admin flag. Also writes a
 * human-readable message to the global $security_error when the check
 * fails so pages can display it to the user.
 *
 * @returns {Boolean} true if the current session belongs to an admin user, false otherwise
 */
function isAdmin() {
    global $dbh;
    global $user_is_admin;
    global $security_error;
    if (isset($_SESSION["userID"])) {
        try {
            $userStmt = $dbh->prepare("
            SELECT admin
            FROM accounts
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