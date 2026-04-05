<?php

include "connect.php";


session_start();

/**
 * Rounds up to the nearest factor of $x
 * e.g. $x = 5 rounds to the next factor of 5
 *      $n = 12, $x = 5 -> 15
 * @param int $n the number being rounded
 * @param int $x the base
 * @return int
 */
function roundUpToAny(int $n, int $x) {
    return ceil($n / $x) * $x;
}

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
 * @param array $arr
 * @param string $name
 * @param bool $single
 * @return array
 */
function reorder(array $arr, string $name, bool $single = false) {
    $out = [];

    foreach($arr as $in_arr) {
        $class = $in_arr[$name];
        if ($single) {
            $out[$class] = array_filter($in_arr, fn($key) => $key !== $name, ARRAY_FILTER_USE_KEY);
        } else if (!array_key_exists($class, $out)) {
            $out[$class] = [array_filter($in_arr, fn($key) => $key !== $name, ARRAY_FILTER_USE_KEY)];
        } else {
            array_push($out[$class], array_filter($in_arr, fn($key) => $key !== $name, ARRAY_FILTER_USE_KEY));
        }
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

    if (!$data) {
        return [[], -1];
    }

    $orders = unassign_array($data, "orderID");

    $placeholder = implode(" ,", array_fill(0, count($orders), '?'));

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

    $placeholder = implode(" ,", array_fill(0, count($classes), '?'));
    $cmd = "SELECT `time`, `quantity`,`name` FROM `productClasses` WHERE `name` IN ($placeholder)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute($classes);


    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function calculate_time($orders, $concurrent = 5) {
    $orders = reorder($orders, "productClass");
    // print_r (get_classes(array_keys($orders)));
    $classes = reorder(get_classes(array_keys($orders)), "name", true);
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

        $t = [];
        foreach ($productIDs as $_ => $quantity) {
            array_push($t, $class["time"] * (ceil($quantity / $class["quantity"])));
            if (count($t) >= $concurrent) {
                $time += min($t);
                $t = [];
            }
            // print_r($t);

        }
        if (!empty($t)) {
            $time += min($t);
        }
    }   
    return roundUpToAny($time, 5);
}

function main() {
    if (!isset($_SESSION["email"])) {
        return "<p>Please login to see and make orders</p>";
    }

    $user = (int)get_user_id($_SESSION["email"]);
    // $user = (int)get_user_id("alice.nguyen@example.com");

    [$userOrders, $lowest_time] = get_user_orders($user);

    if (!$userOrders || $lowest_time == -1) {
        return "<p>No order has been made.</p>";
    }

    // Note that $userOrders will also be represented in $orders
    $orders = get_all_orders($lowest_time);

    // $user_time = calculate_time($userOrders);
    $overall_time = calculate_time($orders, 5);

    return $overall_time;
}

echo main();
