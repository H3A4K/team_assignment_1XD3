<?php

session_start();
include "connect.php";

header("Content-Type: application/json");

if (!isset($_SESSION["userID"])) {
    echo json_encode([
        "loggedIn" => false,
        "items" => [],
        "subtotal" => 0,
        "total" => 0,
        "hasOpenOrder" => false
    ]);
    return;
}

$userID = $_SESSION["userID"];

try {
    $orderStmt = $dbh->prepare("
        SELECT orderID
        FROM orders
        WHERE accountID = ? AND fullfilled = 0
        ORDER BY orderID DESC
        LIMIT 1
    ");
    $orderStmt->execute([$userID]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode([
            "loggedIn" => true,
            "items" => [],
            "subtotal" => 0,
            "total" => 0,
            "hasOpenOrder" => false
        ]);
        return;
    }

    $itemsStmt = $dbh->prepare("
        SELECT
            od.orderDetailID,
            od.productID,
            od.quantity,
            p.productName,
            p.price
        FROM orderdetails od
        INNER JOIN products p ON p.productID = od.productID
        WHERE od.orderID = ?
        ORDER BY od.orderDetailID ASC
    ");
    $itemsStmt->execute([$order["orderID"]]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    $subtotal = 0;
    foreach ($items as &$item) {
        $item["price"] = (float) $item["price"];
        $item["quantity"] = (int) $item["quantity"];
        $item["lineTotal"] = $item["price"] * $item["quantity"];
        $subtotal += $item["lineTotal"];
    }

    echo json_encode([
        "loggedIn" => true,
        "hasOpenOrder" => true,
        "orderID" => (int) $order["orderID"],
        "items" => $items,
        "subtotal" => $subtotal,
        "total" => $subtotal
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
