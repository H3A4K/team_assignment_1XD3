<?php
/**
 * add_to_order.php
 *
 * JSON endpoint that adds a product to the logged-in user's open (not yet
 * checked out) order, creating a fresh order row if they don't already
 * have one. If the product is already in the order, its quantity is
 * incremented instead of inserting a duplicate row.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 06, 2026
 */

session_start();
include "connect.php";

header("Content-Type: application/json");

if (!isset($_SESSION["userID"])) {
    http_response_code(401);
    echo json_encode(["error" => "You must be logged in to add items to an order"]);
    return;
}

$input = json_decode(file_get_contents("php://input"), true);

$productID = filter_var($input["productID"] ?? null, FILTER_VALIDATE_INT);
$quantity = filter_var($input["quantity"] ?? 1, FILTER_VALIDATE_INT);
$userID = $_SESSION["userID"];

if (!$productID || !$quantity || $quantity < 1) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid product or quantity"]);
    return;
}

try {
    $dbh->beginTransaction();

    $userStmt = $dbh->prepare("SELECT address FROM users WHERE userID = ?");
    $userStmt->execute([$userID]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $dbh->rollBack();
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
        return;
    }

    $orderStmt = $dbh->prepare("
        SELECT orderID
        FROM orders
        WHERE accountID = ? AND fullfilled = 0
        ORDER BY orderID DESC
        LIMIT 1
    ");
    $orderStmt->execute([$userID]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $orderID = $order["orderID"];
    } else {
        $createOrderStmt = $dbh->prepare("
            INSERT INTO orders (accountID, orderDate, address, fullfilled)
            VALUES (?, NOW(), ?, 0)
        ");
        $createOrderStmt->execute([$userID, $user["address"]]);
        $orderID = $dbh->lastInsertId();
    }

    $detailStmt = $dbh->prepare("
        SELECT orderDetailID, quantity
        FROM orderdetails
        WHERE orderID = ? AND productID = ?
        LIMIT 1
    ");
    $detailStmt->execute([$orderID, $productID]);
    $detail = $detailStmt->fetch(PDO::FETCH_ASSOC);

    if ($detail) {
        $updateStmt = $dbh->prepare("
            UPDATE orderdetails
            SET quantity = quantity + ?
            WHERE orderDetailID = ?
        ");
        $updateStmt->execute([$quantity, $detail["orderDetailID"]]);
    } else {
        $insertStmt = $dbh->prepare("
            INSERT INTO orderdetails (orderID, productID, quantity)
            VALUES (?, ?, ?)
        ");
        $insertStmt->execute([$orderID, $productID, $quantity]);
    }

    $dbh->commit();

    echo json_encode([
        "success" => true,
        "message" => "Item added to order",
        "orderID" => $orderID,
        "productID" => $productID,
        "quantityAdded" => $quantity
    ]);
} catch (Exception $e) {
    if ($dbh->inTransaction()) {
        $dbh->rollBack();
    }

    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
