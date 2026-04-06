<?php

session_start();
include "connect.php";

header("Content-Type: application/json");

if (!isset($_SESSION["userID"])) {
    http_response_code(401);
    echo json_encode(["error" => "You must be logged in to complete an order"]);
    return;
}

$userID = $_SESSION["userID"];

try {
    $dbh->beginTransaction();

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
        $dbh->rollBack();
        http_response_code(400);
        echo json_encode(["error" => "There is no open order to complete"]);
        return;
    }

    $updateOrderStmt = $dbh->prepare("
        UPDATE orders
        SET fullfilled = 1
        WHERE accountID = ? AND fullfilled = 0
    ");
    $updateOrderStmt->execute([$userID]);
    $completedOrders = $updateOrderStmt->rowCount();

    $updateUserStmt = $dbh->prepare("
        UPDATE users
        SET ordersdone = ordersdone + ?
        WHERE userID = ?
    ");
    $updateUserStmt->execute([$completedOrders, $userID]);

    $dbh->commit();

    echo json_encode([
        "success" => true,
        "message" => "Order completed successfully",
        "orderID" => (int) $order["orderID"],
        "completedOrders" => $completedOrders
    ]);
} catch (Exception $e) {
    if ($dbh->inTransaction()) {
        $dbh->rollBack();
    }

    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
