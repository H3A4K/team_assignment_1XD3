<?php 
include "connect.php";

function getAllProducts() {
    global $dbh;
    $cmd = "SELECT * FROM products";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $products = $stmt->fetchAll();
    return $products;
}

function getAllOrders() {
    global $dbh;
    $cmd = "SELECT * FROM orders";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $orders = $stmt->fetchAll();
    return $orders;
}

function GetAllPromoCodes() {
    global $dbh;
    $cmd = "SELECT * FROM promocodes";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $promocodes = $stmt->fetchAll();
    return $promocodes;
}

$getAllProducts = filter_input(INPUT_GET, "getAllProducts", FILTER_SANITIZE_SPECIAL_CHARS);
$getAllOrders = filter_input(INPUT_GET, "getAllOrders", FILTER_SANITIZE_SPECIAL_CHARS);
$getAllPromoCodes = filter_input(INPUT_GET, "getAllPromoCodes", FILTER_SANITIZE_SPECIAL_CHARS);

if ($getAllProducts !== NULL) {
    $products = getAllProducts();
    echo json_encode($products);
}
else if ($getAllOrders !== NULL) {
    $orders = getAllOrders();
    echo json_encode($orders);
}
else if ($getAllPromoCodes !== NULL) {
    $promocodes = GetAllPromoCodes();
    echo json_encode($promocodes);
}





?>