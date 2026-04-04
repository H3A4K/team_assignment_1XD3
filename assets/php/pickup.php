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
 * Takes an array of associative arrays and returns an array of the values responding to the keys.
 * @param array $arr
 * @param string $key
 * @return array
 */
function unassign_array(array $arr, string $key) {
    $out = [];
    foreach ($arr as $_ => $sub) {
        array_push($out, $sub[$key]);
    }
    return $out;
}

/**
 * Takes an array of associative arrays and returns those arrays as associated to their value at $name
 * @param array $orders
 * @return array
 */
function reorder(array $orders, $name) {
    $out = [];

    foreach($orders as $order) {
        $class = $order[$name];
        unset($orders[$name]);
        if (!array_key_exists($class, $out)) {
            $out[$class] = [];
        } 
        array_push($out[$class], array_filter($order, fn($key) => $key !== $name));
    }

    return $out;
}

/**
 * Gets all of the orders associated with the user that are not currently fullfilled
 * @param int $user
 * @return array > productID and Quantity Pairs, and the orders' lowest unfullfilled date/time
 */
function get_user_orders(int $user) {
    global $dbh;

    $cmd = "SELECT `orderID`, `orderDate` FROM `orders` WHERE `accountID`=? AND `fullfilled`=0";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$user]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $orders = unassign_array($data, "orderID");

    $placeholder = implode(" ", array_fill(0, count($orders), '?'));

    $cmd = "SELECT `quantity`, `orderdetails`.`productID`, `products`.`productClass` FROM `orderdetails`
        JOIN `products` ON `orderdetails`.`productID` = `products`.`productID`
        WHERE `orderID` IN ($placeholder)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute($orders);

    return [$stmt->fetchAll(PDO::FETCH_ASSOC), min(unassign_array($data, "orderDate"))];
}

function get_all_orders($time) {
    global $dbh;

    $cmd = "SELECT `quantity`, `orderdetails`.`productID`, `products`.`productClass` FROM `orderdetails` 
        JOIN `orders` ON `orderdetails`.`orderID` = `orders`.`orderID` 
        JOIN `products` ON `orderdetails`.`productID` = `products`.`productID`
        WHERE `orders`.`orderDate`>=?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$time]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_classes($classes) {
    global $dbh;

    $classes = array_unique($classes);

    $placeholder = implode(" ", array_fill(0, count($classes), '?'));

    $cmd = "SELECT `time`, `quantity`,`name` FROM `productClasses` WHERE `name` IN ($placeholder)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute($classes);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function calculate_time($orders) {
    $orders = reorder($orders, "productClass");
    $classes = reorder(get_classes(array_keys($orders)), "name");
    $time = 0;

    foreach ($orders as $c => $arr) {
        $class = $classes[$c];
        $productIDs = [];

        foreach ($arr as $_ => $order) {
            $prodID = $order["productID"];
            if (!in_array($prodID, $productIDs)) {
                $productIDs[$prodID] = $order["quantity"];
            } else {
                $productIDs[$prodID] += $order["quantity"];
            }
        }

        foreach ($productIDs as $_ => $quantity) {
            $t = $quantity / $class["quantity"] + ($quantity % $class["quantity"] != 0);
            $t *= $class["time"];
            $time += $t;
        }

    }
    
    return $time;
}


// $display = "";

// if (!isset($_SESSION["email"])) {
//     $display =  "<p>Please login to see and make orders</p>";
//     die();
// }

$user = (int)get_user_id("alice.nguyen@example.com");



[$userOrders, $lowest_time] = get_user_orders($user);

// Note that $userOrders will also be represented in $orders
$orders = get_all_orders($lowest_time);

$user_time = calculate_time($userOrders);
$overall_time = calculate_time($orders);

echo $overall_time;