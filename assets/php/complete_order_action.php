<?php
/**
 * complete_order_action.php
 *
 * Traditional form-post variant of complete_order.php. Used by older
 * checkout links that simply submit a form rather than calling the JSON
 * endpoint. Marks the user's open order as completed and then redirects
 * them back to the menu page with a success or error query string.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 06, 2026
 */

session_start();
include "connect.php";
include "promo_requirements.php";

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

    // Final server-side guard: if a promo code is applied, re-verify its
    // product requirements against the actual order contents. Blocks the
    // edge case where the user bypassed the client-side check.
    if (isset($_SESSION["appliedPromoID"])) {
        $promoStmt = $dbh->prepare("
            SELECT promoCode, requiredProductIDs, active
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
                $missing = $reqCheck["missing"];
                $msg = count($missing) === 1
                    ? "Promo code " . $appliedPromo["promoCode"] . " requires " . $missing[0] . " in your cart."
                    : "Promo code " . $appliedPromo["promoCode"] . " requires these items in your cart: " . implode(", ", $missing) . ".";
                header("Location: /team_assignment_1XD3/menu/?order_error=" . urlencode($msg));
                exit;
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

    header("Location: /team_assignment_1XD3/menu/?order_completed=1");
    exit;
} catch (Exception $e) {
    if ($dbh->inTransaction()) {
        $dbh->rollBack();
    }

    header("Location: /team_assignment_1XD3/menu/?order_error=" . urlencode("Server error while completing the order"));
    exit;
}
