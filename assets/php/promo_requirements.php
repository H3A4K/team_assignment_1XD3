<?php
/**
 * Helper utilities for promo-code product-requirement validation.
 *
 * A promo code MAY define `requiredProductIDs` as a comma-separated list of
 * product IDs. When it does, the code is only valid while the user's open
 * order contains at least one of EACH listed product. This is validated in:
 *   - apply_promocode.php  (user clicks Apply)
 *   - get_cart.php         (every cart refresh; silently drops stale codes)
 *   - complete_order*.php  (final checkpoint when placing the order)
 */

/**
 * Canonicalize a comma-separated product-ID list into "1,2,3" form with
 * only unique positive integers. Returns NULL if nothing valid remains.
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
 * Checks whether the user's open order satisfies the given requirement list.
 *
 * Returns an associative array:
 *   [
 *     "ok"      => bool,
 *     "missing" => array of product NAMES (empty when ok=true)
 *   ]
 *
 * When $requiredProductIDsString is NULL/empty, returns ["ok" => true, "missing" => []].
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
