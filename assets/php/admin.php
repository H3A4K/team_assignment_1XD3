<?php 
session_start();

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

// PRODUCTS
function insertProduct($productName, $productDesc, $price, $productClass) {
    global $dbh;
    $cmd = "INSERT INTO products (productName, productDesc, price, productImg, productClass) VALUES (?, ?, ?, ?, ?)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productName, $productDesc, $price, "placeholder.jpg", $productClass]);
}

function updateProduct($productID, $productName, $productDesc, $price, $productClass) {
    global $dbh;
    $cmd = "UPDATE products SET productName = ?,  productDesc = ?, price = ?, productImg = ?, productClass = ? WHERE productID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productName, $productDesc, $price, "placeholder.jpg", $productClass, $productID]);
}

function removeProduct($productID) {
    global $dbh;
    $cmd = "DELETE FROM products WHERE productID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productID]);
}

// ORDERS
function updateOrderStatus($orderID, $fullfilled) {
    global $dbh;
    $cmd = "UPDATE orders SET fullfilled = ? WHERE orderID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$fullfilled, $orderID]);
}

// PROMO CODES
function insertPromoCode($promoCode, $discountType, $discountValue, $active, $expiryDate) {
    global $dbh;
    $cmd = "INSERT INTO promocodes (promoCode, discountType, discountValue, active, expiryDate) VALUES (?, ?, ?, ?, ?)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$promoCode, $discountType, $discountValue, $active, $expiryDate]);
}

function updatePromoCode($promoID, $promoCode, $discountType, $discountValue, $active, $expiryDate) {
    global $dbh;
    $cmd = "UPDATE promocodes SET promoCode = ?,  discountType = ?, discountValue = ?, active = ?, expiryDate = ? WHERE promoID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$promoCode, $discountType, $discountValue, $active, $expiryDate, $promoID]);
}

function removePromoCode($promoID) {
    global $dbh;
    $cmd = "DELETE FROM promocodes WHERE promoID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$promoID]);
}

$getAllProducts = filter_input(INPUT_GET, "getAllProducts", FILTER_SANITIZE_SPECIAL_CHARS);
$getAllOrders = filter_input(INPUT_GET, "getAllOrders", FILTER_SANITIZE_SPECIAL_CHARS);
$getAllPromoCodes = filter_input(INPUT_GET, "getAllPromoCodes", FILTER_SANITIZE_SPECIAL_CHARS);
$getProductClasses = filter_input(INPUT_GET, "getProductClasses", FILTER_SANITIZE_SPECIAL_CHARS);
$saveProduct = filter_input(INPUT_GET, "saveProduct", FILTER_SANITIZE_SPECIAL_CHARS);
$removeProduct = filter_input(INPUT_GET, "removeProduct", FILTER_SANITIZE_SPECIAL_CHARS);
$editOrderStatus = filter_input(INPUT_GET, "editOrderStatus", FILTER_SANITIZE_SPECIAL_CHARS);
$savePromoCode = filter_input(INPUT_GET, "savePromoCode", FILTER_SANITIZE_SPECIAL_CHARS);
$removePromoCode = filter_input(INPUT_GET, "removePromoCode", FILTER_SANITIZE_SPECIAL_CHARS);

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
    $productID = filter_input(INPUT_POST, "productID", FILTER_SANITIZE_SPECIAL_CHARS);
    $productName = filter_input(INPUT_POST, "productName", FILTER_SANITIZE_SPECIAL_CHARS);
    $productDesc = filter_input(INPUT_POST, "productDesc", FILTER_SANITIZE_SPECIAL_CHARS);
    $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
    $productClass = filter_input(INPUT_POST, "productClass", FILTER_SANITIZE_SPECIAL_CHARS);
    if ($productID !== NULL && $productID !== "") {
        updateProduct($productID, $productName, $productDesc, $price, $productClass);
    }
    else {
        insertProduct($productName, $productDesc, $price, $productClass);
    }    
    $products = getAllProducts();
    echo json_encode($products);
}
else if ($removeProduct !== NULL) {
    $productID = filter_input(INPUT_POST, "productID", FILTER_VALIDATE_INT);
    removeProduct($productID);
    $products = getAllProducts();
    echo json_encode($products);
} 
else if ($editOrderStatus !== NULL) {
    $orderID = filter_input(INPUT_POST, "orderID", FILTER_VALIDATE_INT);
    $fullfilled = filter_input(INPUT_POST, "fullfilled", FILTER_VALIDATE_INT);
    if ($orderID !== NULL && $orderID !== "") {
        updateOrderStatus($orderID, $fullfilled);
    }
    $orders = getAllOrders();
    echo json_encode($orders);
}
else if ($savePromoCode !== NULL) {
    $promoID = filter_input(INPUT_POST, "promoID", FILTER_SANITIZE_SPECIAL_CHARS);
    $promoCode = filter_input(INPUT_POST, "promoCode", FILTER_SANITIZE_SPECIAL_CHARS);
    $discountType = filter_input(INPUT_POST, "discountType", FILTER_SANITIZE_SPECIAL_CHARS);
    $discountValue = filter_input(INPUT_POST, "discountValue", FILTER_VALIDATE_FLOAT);
    $active = filter_input(INPUT_POST, "active", FILTER_SANITIZE_SPECIAL_CHARS);
    $expiryDate = filter_input(INPUT_POST, "expiryDate", FILTER_SANITIZE_SPECIAL_CHARS);
    if ($promoID !== NULL && $promoID !== "") {
        updatePromoCode($promoID, $promoCode, $discountType, $discountValue, $active, $expiryDate);
    }
    else {
        insertPromoCode($promoCode, $discountType, $discountValue, $active, $expiryDate);
    }    
    $promoCodes = GetAllPromoCodes();
    echo json_encode($promoCodes);
}  
else if ($removePromoCode !== NULL) {
    $promoID = filter_input(INPUT_POST, "promoID", FILTER_VALIDATE_INT);
    removePromoCode($promoID);
    $promocode = GetAllPromoCodes();
    echo json_encode($promocode);
}  
?>