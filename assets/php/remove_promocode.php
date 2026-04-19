<?php
/**
 * remove_promocode.php
 *
 * Removes any promo code that the user has applied to their current cart
 * session. Returns a JSON success payload for the menu page's cart UI.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 18, 2026
 */

session_start();
header("Content-Type: application/json");

unset($_SESSION["appliedPromoID"]);
unset($_SESSION["appliedPromoCode"]);

echo json_encode(["success" => true]);
