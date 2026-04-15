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

function GetProductClasses() {
    global $dbh;
    $cmd = "SELECT * from productClasses";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $classes = $stmt->fetchAll();
    return $classes;
}

function insertProduct($productName, $productDesc, $price, $productClass) {
    global $dbh;
    $cmd = "INSERT INTO products (productName, productDesc, price, productImg, productClass) VALUES (?, ?, ?, ?, ?)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productName, $productDesc, $price, "placeholder.jpg", $productClass]);
}

function removeProduct($productID) {
    global $dbh;
    $cmd = "DELETE FROM products WHERE productID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productID]);
}

$getAllProducts = filter_input(INPUT_GET, "getAllProducts", FILTER_SANITIZE_SPECIAL_CHARS);
$getAllOrders = filter_input(INPUT_GET, "getAllOrders", FILTER_SANITIZE_SPECIAL_CHARS);
$getAllPromoCodes = filter_input(INPUT_GET, "getAllPromoCodes", FILTER_SANITIZE_SPECIAL_CHARS);
$getProductClasses = filter_input(INPUT_GET, "getProductClasses", FILTER_SANITIZE_SPECIAL_CHARS);
$saveProduct = filter_input(INPUT_GET, "saveProduct", FILTER_SANITIZE_SPECIAL_CHARS);
$removeProduct = filter_input(INPUT_GET, "removeProduct", FILTER_SANITIZE_SPECIAL_CHARS);

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
else if ($getProductClasses !== NULL) {
    $classes = GetProductClasses();
    echo json_encode($classes);
}
else if ($saveProduct !== NULL) {
    $productName = filter_input(INPUT_POST, "productName", FILTER_SANITIZE_SPECIAL_CHARS);
    $productDesc = filter_input(INPUT_POST, "productDesc", FILTER_SANITIZE_SPECIAL_CHARS);
    $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
    $productClass = filter_input(INPUT_POST, "productClass", FILTER_SANITIZE_SPECIAL_CHARS);
    insertProduct($productName, $productDesc, $price, $productClass);
    $products = getAllProducts();
    echo json_encode($products);
}
else if ($removeProduct !== NULL) {
    $productID = filter_input(INPUT_POST, "productID", FILTER_VALIDATE_INT);
    removeProduct($productID);
    $products = getAllProducts();
    echo json_encode($products);
}    
?>