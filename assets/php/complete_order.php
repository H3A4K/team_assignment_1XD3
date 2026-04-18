<?php

session_start();
include "connect.php";
include "promo_requirements.php";

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

    // Final server-side guard: if a promo code is applied, re-verify its
    // product requirements against the actual order contents. Blocks the
    // edge case where the user bypassed the client-side check.
    if (isset($_SESSION["appliedPromoID"])) {
        $promoStmt = $dbh->prepare("
            SELECT promoCode, requiredProductIDs, active, expiryDate
            FROM promocodes
            WHERE promoID = ?
            LIMIT 1
        ");
        $promoStmt->execute([$_SESSION["appliedPromoID"]]);
        $appliedPromo = $promoStmt->fetch(PDO::FETCH_ASSOC);
        if ($appliedPromo && (int) $appliedPromo["active"] === 1) {
            $reqCheck = checkPromoRequirements($dbh, $userID, $appliedPromo["requiredProductIDs"]);
            if (!$reqCheck["ok"]) {
                $dbh->rollBack();
                http_response_code(400);
                $missing = $reqCheck["missing"];
                $msg = count($missing) === 1
                    ? "Promo code " . $appliedPromo["promoCode"] . " requires " . $missing[0] . " in your cart."
                    : "Promo code " . $appliedPromo["promoCode"] . " requires these items in your cart: " . implode(", ", $missing) . ".";
                echo json_encode(["error" => $msg, "missing" => $missing]);
                return;
            }
        }
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

    // Clear any applied promo code so next order starts fresh
    unset($_SESSION["appliedPromoID"], $_SESSION["appliedPromoCode"]);

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
