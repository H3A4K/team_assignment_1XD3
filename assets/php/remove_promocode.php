<?php
/**
 * Clears the currently-applied promo code from the session.
 * No DB work needed.
 */

session_start();
header("Content-Type: application/json");

unset($_SESSION["appliedPromoID"]);
unset($_SESSION["appliedPromoCode"]);

echo json_encode(["success" => true]);
