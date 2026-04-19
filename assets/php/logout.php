<?php
/**
 * logout.php
 *
 * Ends the current user's session and destroys any stored session data.
 * Called from the global logout button via a fetch() request.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 01, 2026
 */

session_start();
if (isset($_SESSION["email"])) {
    session_destroy();
}

echo "logged out";

?>