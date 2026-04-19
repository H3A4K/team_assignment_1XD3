<?php
/**
 * apply_promocode.php
 *
 * JSON endpoint that validates a promo code submitted by the user and, if
 * it's active, not expired, and the cart satisfies all of its product
 * requirements, stores it in the session so get_cart.php will apply the
 * discount on every subsequent cart fetch.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 18, 2026
 */

session_start();
include "connect.php";
include "promo_requirements.php";

header("Content-Type: application/json");

if (!isset($_SESSION["userID"])) {
    http_response_code(401);
    echo json_encode(["error" => "You must be logged in to apply a promo code"]);
    return;
}

$input = json_decode(file_get_contents("php://input"), true);
$promoCode = isset($input["promoCode"]) ? trim((string) $input["promoCode"]) : "";

if ($promoCode === "") {
    http_response_code(400);
    echo json_encode(["error" => "Please enter a promo code"]);
    return;
}

try {
    $stmt = $dbh->prepare("
        SELECT promoID, promoCode, discountType, discountValue, active, expiryDate, requiredProductIDs
        FROM promocodes
        WHERE promoCode = ?
        LIMIT 1
    ");
    $stmt->execute([$promoCode]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(["error" => "Invalid promo code"]);
        return;
    }

    if ((int) $row["active"] !== 1) {
        http_response_code(400);
        echo json_encode(["error" => "This promo code is no longer active"]);
        return;
    }

    // Reject expired codes
    if (!empty($row["expiryDate"])) {
        $expiry = strtotime($row["expiryDate"]);
        if ($expiry !== false && $expiry < time()) {
            http_response_code(400);
            echo json_encode(["error" => "This promo code has expired"]);
            return;
        }
    }

    // Enforce product-requirement conditions: code is only valid when the
    // customer's open order contains every required product.
    $reqCheck = checkPromoRequirements($dbh, $_SESSION["userID"], $row["requiredProductIDs"]);
    if (!$reqCheck["ok"]) {
        $missing = $reqCheck["missing"];
        $msg = count($missing) === 1
            ? "This code requires " . $missing[0] . " in your cart."
            : "This code requires these items in your cart: " . implode(", ", $missing) . ".";
        http_response_code(400);
        echo json_encode(["error" => $msg, "missing" => $missing]);
        return;
    }

    // Store in session; get_cart.php will apply it on every cart fetch
    $_SESSION["appliedPromoID"] = (int) $row["promoID"];
    $_SESSION["appliedPromoCode"] = $row["promoCode"];

    echo json_encode([
        "success" => true,
        "promoCode" => $row["promoCode"],
        "discountType" => $row["discountType"],
        "discountValue" => (float) $row["discountValue"],
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
