<?php

session_start();
include "connect.php";
include "promo_requirements.php";

header("Content-Type: application/json");

/**
 * Resolves the currently-applied promo code against the DB and returns
 * [discountAmount, promoCode, discountType, discountValue] given a subtotal.
 * If the stored promo is no longer active / has expired, the session is cleared
 * and a zero discount is returned.
 */
function resolveAppliedPromo($dbh, $subtotal) {
    if (!isset($_SESSION["appliedPromoID"])) {
        return ["amount" => 0.0, "code" => null, "type" => null, "value" => 0.0];
    }

    $stmt = $dbh->prepare("
        SELECT promoID, promoCode, discountType, discountValue, active, expiryDate, requiredProductIDs
        FROM promocodes
        WHERE promoID = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION["appliedPromoID"]]);
    $promo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Clear invalid / inactive / expired codes from the session
    if (!$promo || (int) $promo["active"] !== 1) {
        unset($_SESSION["appliedPromoID"], $_SESSION["appliedPromoCode"]);
        return ["amount" => 0.0, "code" => null, "type" => null, "value" => 0.0];
    }
    if (!empty($promo["expiryDate"])) {
        $expiry = strtotime($promo["expiryDate"]);
        if ($expiry !== false && $expiry < time()) {
            unset($_SESSION["appliedPromoID"], $_SESSION["appliedPromoCode"]);
            return ["amount" => 0.0, "code" => null, "type" => null, "value" => 0.0];
        }
    }

    // Drop the code silently if the cart no longer satisfies its requirements
    // (e.g., the user removed a required item after applying the code).
    if (isset($_SESSION["userID"])) {
        $reqCheck = checkPromoRequirements($dbh, $_SESSION["userID"], $promo["requiredProductIDs"]);
        if (!$reqCheck["ok"]) {
            unset($_SESSION["appliedPromoID"], $_SESSION["appliedPromoCode"]);
            return ["amount" => 0.0, "code" => null, "type" => null, "value" => 0.0];
        }
    }

    $discountValue = (float) $promo["discountValue"];
    if ($promo["discountType"] === "percentage") {
        $amount = $subtotal * ($discountValue / 100);
    } else {
        // fixed-dollar discount; never exceed the subtotal
        $amount = min($discountValue, $subtotal);
    }

    return [
        "amount" => round($amount, 2),
        "code" => $promo["promoCode"],
        "type" => $promo["discountType"],
        "value" => $discountValue,
    ];
}

if (!isset($_SESSION["userID"])) {
    echo json_encode([
        "loggedIn" => false,
        "items" => [],
        "subtotal" => 0,
        "discount" => 0,
        "tax" => 0,
        "total" => 0,
        "hasOpenOrder" => false,
        "appliedPromoCode" => null
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
        // Still re-validate the applied promo so a stale/expired/deleted code
        // gets cleared from the session and the UI chip disappears cleanly.
        $promoResult = resolveAppliedPromo($dbh, 0);
        echo json_encode([
            "loggedIn" => true,
            "items" => [],
            "subtotal" => 0,
            "discount" => 0,
            "tax" => 0,
            "total" => 0,
            "hasOpenOrder" => false,
            "appliedPromoCode" => $promoResult["code"],
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
        $item["orderDetailID"] = (int) $item["orderDetailID"];
        $item["lineTotal"] = $item["price"] * $item["quantity"];
        $subtotal += $item["lineTotal"];
    }
    unset($item);

    $promoResult = resolveAppliedPromo($dbh, $subtotal);
    $discount = $promoResult["amount"];
    $taxable = max(0, $subtotal - $discount);
    $tax = round($taxable * 0.13, 2);
    $total = $taxable + $tax;

    echo json_encode([
        "loggedIn" => true,
        "hasOpenOrder" => true,
        "orderID" => (int) $order["orderID"],
        "items" => $items,
        "subtotal" => round($subtotal, 2),
        "discount" => round($discount, 2),
        "tax" => $tax,
        "total" => round($total, 2),
        "appliedPromoCode" => $promoResult["code"],
        "appliedPromoType" => $promoResult["type"],
        "appliedPromoValue" => $promoResult["value"],
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
