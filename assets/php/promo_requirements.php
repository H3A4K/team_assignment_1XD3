<?php
/**
 * promo_requirements.php
 *
 * Helper functions for promo-code product-requirement logic. Admins can
 * tie a promo code to a list of required product IDs, and these helpers
 * handle both cleaning admin input and verifying a user's cart meets the
 * requirements at apply/checkout time.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 18, 2026
 */

/**
 * Cleans up the raw "required product IDs" string coming from the admin
 * form. Accepts comma- or whitespace-separated IDs, drops non-positive or
 * duplicate entries, and returns a canonical comma-separated string.
 *
 * @param {String} $raw the raw text typed into the admin form (e.g. "101, 103 105")
 * @returns a comma-separated string of unique positive integer IDs, or NULL if there are none
 */
function sanitizeRequiredProductIDs($raw) {
    if ($raw === NULL || $raw === "") return NULL;
    $parts = preg_split('/[,\s]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
    $ids = [];
    foreach ($parts as $p) {
        $n = (int) $p;
        if ($n > 0 && !in_array($n, $ids, true)) {
            $ids[] = $n;
        }
    }
    if (empty($ids)) return NULL;
    return implode(",", $ids);
}

/**
 * Checks whether a user's open order contains every product required by a
 * promo code. Missing products are resolved to human-readable names so the
 * UI can tell the user exactly what's missing from their cart.
 *
 * @param {PDO} $dbh the shared database handle
 * @param {int} $userID the accountID of the logged-in user
 * @param {String} $requiredProductIDsString comma-separated list of required product IDs (may be NULL/empty)
 * @returns an associative array with keys "ok" (bool) and "missing" (array of product names the cart is missing)
 */
function checkPromoRequirements($dbh, $userID, $requiredProductIDsString) {
    if ($requiredProductIDsString === NULL || trim((string) $requiredProductIDsString) === "") {
        return ["ok" => true, "missing" => []];
    }

    $requiredIDs = array_values(array_filter(array_map(
        'intval',
        preg_split('/[,\s]+/', $requiredProductIDsString, -1, PREG_SPLIT_NO_EMPTY)
    ), function ($n) { return $n > 0; }));

    if (empty($requiredIDs)) {
        return ["ok" => true, "missing" => []];
    }

    // Load current cart product IDs from the user's open order
    $stmt = $dbh->prepare("
        SELECT DISTINCT od.productID
        FROM orderdetails od
        INNER JOIN orders o ON o.orderID = od.orderID
        WHERE o.accountID = ? AND o.fullfilled = 0
    ");
    $stmt->execute([$userID]);
    $cartRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cartIDs = array_map(function ($r) { return (int) $r["productID"]; }, $cartRows);

    $missingIDs = array_values(array_diff($requiredIDs, $cartIDs));
    if (empty($missingIDs)) {
        return ["ok" => true, "missing" => []];
    }

    // Resolve missing IDs to human-readable product names for UI feedback
    $placeholders = implode(",", array_fill(0, count($missingIDs), "?"));
    $nameStmt = $dbh->prepare("SELECT productID, productName FROM products WHERE productID IN ($placeholders)");
    $nameStmt->execute($missingIDs);
    $rows = $nameStmt->fetchAll(PDO::FETCH_ASSOC);
    $nameMap = [];
    foreach ($rows as $r) {
        $nameMap[(int) $r["productID"]] = $r["productName"];
    }
    $missingNames = [];
    foreach ($missingIDs as $mid) {
        $missingNames[] = $nameMap[$mid] ?? ("Product #" . $mid);
    }

    return ["ok" => false, "missing" => $missingNames];
}
