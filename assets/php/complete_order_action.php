<?php

session_start();
include "connect.php";

if (!isset($_SESSION["userID"])) {
    header("Location: /team_assignment_1XD3/menu/?order_error=" . urlencode("You must be logged in to complete an order"));
    exit;
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
        header("Location: /team_assignment_1XD3/menu/?order_error=" . urlencode("There is no open order to complete"));
        exit;
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
    header("Location: /team_assignment_1XD3/menu/?order_completed=1");
    exit;
} catch (Exception $e) {
    if ($dbh->inTransaction()) {
        $dbh->rollBack();
    }

    header("Location: /team_assignment_1XD3/menu/?order_error=" . urlencode("Server error while completing the order"));
    exit;
}
