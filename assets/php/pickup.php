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

    $cmd = "SELECT `userID` FROM `users` WHERE `email`=? LIMIT 1";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$email]);

    return $stmt->fetchColumn();
}

/**
 * Gets all of the orders associated with the user that are not currently fullfilled
 * @param int $user
 * @return array
 */
function get_user_orders(int $user) {
    global $dbh;

    $cmd = "SELECT `orderID` FROM `orders` WHERE `accountID`=? AND `fullfilled`=0";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$user]);

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $orderIDs= [];

    // convert to arr of ints, instead of array of array of ints. 
    // e.g. [[1], [2], [3]] -> [1, 2, 3]
    foreach ($orders as $_ => $orderID) {
        array_push($orderIDs, $orderID["orderID"]);
    }

    $placeholder = implode(" ", array_fill(0, count($orderIDs), '?'));

    $cmd = "SELECT `productID`, `quantity` FROM `orderdetails` WHERE `orderID` IN ($placeholder)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute($orderIDs);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_all_orders() {
    global $dbh;

    $cmd = "SELECT `productID` `quantity` FROM `orderdetails`";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();

    $arr = $stmt->fetchAll();

    return $arr;
}

function calculate_time() {
    
}


// $display = "";

// if (!isset($_SESSION["email"])) {
//     $display =  "<p>Please login to see and make orders</p>";
//     die();
// }

$u = (int)get_user_id("alice.nguyen@example.com");

$a = get_user_orders($u);

print_r($a);