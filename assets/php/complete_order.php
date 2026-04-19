<?php
/**
 * complete_order.php
 *
 * JSON checkout endpoint. Marks the user's open order as completed,
 * stamps it with the chosen fulfillment method (pickup or delivery) and
 * delivery address, re-verifies any applied promo code one last time, and
 * returns the URL to redirect to next (the pickup confirmation page).
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 06, 2026
 */

session_start();
include "connect.php";
include "promo_requirements.php";

header("Content-Type: application/json");

/**
 * Reads the checkout payload from the request. Prefers a JSON body (sent
 * by the menu page's fetch call) and falls back to standard $_POST data
 * for form submissions.
 *
 * @returns an associative array of checkout fields (e.g. fulfillmentMethod, deliveryAddress)
 */
function getCheckoutPayload(): array {
    $raw = file_get_contents("php://input");
    if (!$raw) {
        return $_POST;
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : $_POST;
}

/**
 * Checks once (and caches the result) whether the orders table actually
 * has the optional `fulfillmentMethod` column. Lets the app keep running
 * against older databases that haven't been migrated yet.
 *
 * @param {PDO} $dbh the shared database handle
 * @returns true if the column exists, false otherwise
 */
function ordersHasFulfillmentMethodColumn(PDO $dbh): bool {
    static $hasColumn = null;
    if ($hasColumn !== null) {
        return $hasColumn;
    }

    $stmt = $dbh->query("SHOW COLUMNS FROM orders LIKE 'fulfillmentMethod'");
    $hasColumn = $stmt !== false && $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    return $hasColumn;
}

if (!isset($_SESSION["userID"])) {
    http_response_code(401);
    echo json_encode(["error" => "You must be logged in to complete an order"]);
    return;
}

$userID = $_SESSION["userID"];
$payload = getCheckoutPayload();
$fulfillmentMethod = ($payload["fulfillmentMethod"] ?? "pickup") === "delivery" ? "delivery" : "pickup";
$deliveryAddress = trim((string) ($payload["deliveryAddress"] ?? ""));

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

    if ($fulfillmentMethod === "delivery" && $deliveryAddress === "") {
        $addressStmt = $dbh->prepare("
            SELECT address
            FROM users
            WHERE userID = ?
            LIMIT 1
        ");
        $addressStmt->execute([$userID]);
        $deliveryAddress = trim((string) $addressStmt->fetchColumn());
    }

    if ($fulfillmentMethod === "delivery" && $deliveryAddress === "") {
        $dbh->rollBack();
        http_response_code(400);
        echo json_encode(["error" => "Delivery orders need a delivery address"]);
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

    $finalAddress = $fulfillmentMethod === "delivery"
        ? $deliveryAddress
        : "Pickup at Clarence's Kitchen";

    if (ordersHasFulfillmentMethodColumn($dbh)) {
        $updateOrderStmt = $dbh->prepare("
            UPDATE orders
            SET address = ?, fulfillmentMethod = ?, fullfilled = 1
            WHERE orderID = ?
        ");
        $updateOrderStmt->execute([$finalAddress, $fulfillmentMethod, $order["orderID"]]);
    } else {
        $updateOrderStmt = $dbh->prepare("
            UPDATE orders
            SET address = ?, fullfilled = 1
            WHERE orderID = ?
        ");
        $updateOrderStmt->execute([$finalAddress, $order["orderID"]]);
    }

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
        "completedOrders" => $completedOrders,
        "fulfillmentMethod" => $fulfillmentMethod,
        "redirectUrl" => "../pickup/?order_id=" . (int) $order["orderID"],
    ]);
} catch (Exception $e) {
    if ($dbh->inTransaction()) {
        $dbh->rollBack();
    }

    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
