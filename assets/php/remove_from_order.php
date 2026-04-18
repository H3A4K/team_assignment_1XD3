<?php
/**
 * Removes a single line item from the logged-in user's open order.
 * Accepts JSON body: { "orderDetailID": 123 }
 *
 * Security: verifies that the target orderDetail belongs to an order whose
 * accountID matches the current session userID AND whose order is still open
 * (fullfilled = 0). A user cannot delete another user's items or finalized ones.
 */

session_start();
include "connect.php";

header("Content-Type: application/json");

if (!isset($_SESSION["userID"])) {
    http_response_code(401);
    echo json_encode(["error" => "You must be logged in to modify your cart"]);
    return;
}

$input = json_decode(file_get_contents("php://input"), true);
$orderDetailID = filter_var($input["orderDetailID"] ?? null, FILTER_VALIDATE_INT);

if (!$orderDetailID) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid cart item"]);
    return;
}

$userID = $_SESSION["userID"];

try {
    $dbh->beginTransaction();

    // Ownership check: make sure this orderDetail belongs to the user's open order
    $checkStmt = $dbh->prepare("
        SELECT od.orderDetailID
        FROM orderdetails od
        INNER JOIN orders o ON o.orderID = od.orderID
        WHERE od.orderDetailID = ? AND o.accountID = ? AND o.fullfilled = 0
        LIMIT 1
    ");
    $checkStmt->execute([$orderDetailID, $userID]);
    $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $dbh->rollBack();
        http_response_code(404);
        echo json_encode(["error" => "Cart item not found"]);
        return;
    }

    $deleteStmt = $dbh->prepare("DELETE FROM orderdetails WHERE orderDetailID = ?");
    $deleteStmt->execute([$orderDetailID]);

    $dbh->commit();

    echo json_encode([
        "success" => true,
        "orderDetailID" => $orderDetailID,
    ]);
} catch (Exception $e) {
    if ($dbh->inTransaction()) {
        $dbh->rollBack();
    }
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
