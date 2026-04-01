<?php

include "./connect.php";

session_start();

/**
 * Gets the user's id from their email
 * @param string $email
 * @return int the User Id
 */
function get_user_id(string $email) {
    global $dbh;

    $cmd = "SELECT `userID` FROM `users` WHERE `email`=?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$email]);

    return $stmt->fetch()["userID"];
}

/**
 * Gets all of the orderIDs that are currently
 * @param int $user
 * @return array
 */
function get_user_orders(int $user) {
    global $dbh;

    $cmd = "SELECT `orderID` FROM `orders` WHERE `userID`=? AND `fullfilled`=0";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$user]);

    return $stmt->fetchAll();
}


$display = "";

if (!isset($_SESSION["email"])) {
    $display =  "<p>Please login to see and make orders</p>";
    die();
}





