<?php
/**
 * pickup.php
 *
 * JSON endpoint used by the pickup confirmation page. Calculates an
 * estimated wait time for the user's most recent (or specifically
 * requested) order, based on how long each product class takes to make
 * and how busy the kitchen has been since the order was placed.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 01, 2026
 */

include "connect.php";

session_start();

header("Content-Type: application/json");

/**
 * Rounds a number up to the nearest multiple of x. Used to round wait
 * times up to the nearest 5 minutes so the UI shows tidy numbers.
 *
 * @param {int|float} $n the number to round (e.g. 17.3)
 * @param {int} $x the multiple to round up to (e.g. 5)
 * @returns the smallest multiple of $x that is >= $n (e.g. 20)
 */
function roundUpToAny(int|float $n, int $x): int {
    return (int) (ceil($n / $x) * $x);
}

/**
 * Pulls a single column out of every row of a result-set style array and
 * returns just those values as a flat array.
 *
 * @param {array} $arr the input array of associative arrays (rows)
 * @param {String} $key the key whose value should be pulled from each row
 * @returns a flat array of the values
 */
function get_column(array $arr, string $key): array {
    $out = [];
    foreach ($arr as $sub) {
        $out[] = $sub[$key];
    }
    return $out;
}

/**
 * Groups rows by the value of one of their fields.
 *
 * @param {array} $arr the input array of associative-array rows
 * @param {String} $name the key to group by (e.g. "productClass")
 * @param {bool} $single when true, assumes there is only one row per group and stores it directly; when false, each group is itself an array of rows
 * @returns an associative array keyed by the grouping value
 */
function reorder(array $arr, string $name, bool $single = false): array {
    $out = [];

    foreach ($arr as $in_arr) {
        $class = $in_arr[$name];
        if ($single) {
            $out[$class] = array_filter($in_arr, fn($key) => $key !== $name, ARRAY_FILTER_USE_KEY);
        } elseif (!array_key_exists($class, $out)) {
            $out[$class] = [array_filter($in_arr, fn($key) => $key !== $name, ARRAY_FILTER_USE_KEY)];
        } else {
            $out[$class][] = array_filter($in_arr, fn($key) => $key !== $name, ARRAY_FILTER_USE_KEY);
        }
    }

    return $out;
}

/**
 * Checks once (and caches the result) whether the orders table has the
 * optional `fulfillmentMethod` column. Allows the wait-time code to keep
 * working against older databases where the column hasn't been added yet.
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

/**
 * Normalizes the fulfillment method for an order. Older databases may
 * not have the explicit fulfillmentMethod column yet, so fall back to
 * the stored address to distinguish pickup from delivery.
 *
 * @param {array} $order a row from the orders table
 * @returns "pickup" or "delivery"
 */
function resolveFulfillmentMethod(array $order): string {
    $method = strtolower(trim((string) ($order["fulfillmentMethod"] ?? "")));
    if ($method === "delivery" || $method === "pickup") {
        return $method;
    }

    $address = trim((string) ($order["address"] ?? ""));
    if ($address !== "" && strcasecmp($address, "Pickup at Clarence's Kitchen") !== 0) {
        return "delivery";
    }

    return "pickup";
}

/**
 * Loads the line items for a specific order, joined with their product
 * class so the timing logic can group items by kitchen station.
 *
 * @param {int} $orderID the ID of the order whose items should be fetched
 * @returns an array of rows with keys `quantity`, `productID`, `productClass`
 */
function getOrderItems(int $orderID): array {
    global $dbh;

    $cmd = "SELECT `quantity`, `orderdetails`.`productID`, `products`.`productClass`
        FROM `orderdetails`
        JOIN `products` ON `orderdetails`.`productID` = `products`.`productID`
        WHERE `orderID` = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$orderID]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetches the order whose wait time we want to show. If the caller
 * passes a specific orderID, that order is used (after verifying it
 * belongs to the user); otherwise falls back to the user's oldest open
 * (unfulfilled) order.
 *
 * @param {int} $userID the accountID of the logged-in user
 * @param {int|null} $orderID a specific orderID to look up, or null to use the current open order
 * @returns a four-element array [items, orderDate, fulfillmentMethod, address]; returns empty items and -1 orderDate when no order is found
 */
function getRequestedOrder(int $userID, ?int $orderID): array {
    global $dbh;

    $selectFields = ordersHasFulfillmentMethodColumn($dbh)
        ? "orderID, orderDate, address, discountTotal, fulfillmentMethod"
        : "orderID, orderDate, address, discountTotal";

    if ($orderID) {
        $cmd = "SELECT $selectFields
            FROM `orders`
            WHERE `orderID` = ? AND `accountID` = ?
            LIMIT 1";
        $stmt = $dbh->prepare($cmd);
        $stmt->execute([$orderID, $userID]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return [[], -1, "pickup", ""];
        }

        return [
            getOrderItems((int) $order["orderID"]),
            $order["orderDate"],
            resolveFulfillmentMethod($order),
            $order["address"] ?? "",
            $order["discountTotal"],
        ];
    }

    $cmd = "SELECT $selectFields
        FROM `orders`
        WHERE `accountID` = ? AND `fullfilled` = 0
        ORDER BY `orderDate` ASC
        LIMIT 1";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$userID]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        return [[], -1, "pickup", ""];
    }

    return [
        getOrderItems((int) $order["orderID"]),
        $order["orderDate"],
        resolveFulfillmentMethod($order),
        $order["address"] ?? "",
        $order["discountTotal"],
    ];
}

/**
 * Loads every order item that was placed at or after the given time.
 * Used to figure out how busy the kitchen has been since the user's
 * order arrived so we can estimate a realistic wait time.
 *
 * @param {String} $time a MySQL datetime string (e.g. "2026-04-19 14:30:00")
 * @returns an array of rows with keys `quantity`, `productID`, `productClass`
 */
function getAllOrdersSince(string $time): array {
    global $dbh;

    $cmd = "SELECT `quantity`, `orderdetails`.`productID`, `products`.`productClass`
        FROM `orderdetails`
        JOIN `orders` ON `orderdetails`.`orderID` = `orders`.`orderID`
        JOIN `products` ON `orderdetails`.`productID` = `products`.`productID`
        WHERE `orders`.`orderDate` >= ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$time]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetches the timing configuration for a list of product classes (how
 * long a batch takes and how many items fit in one batch).
 *
 * @param {array} $classes an array of product class names (e.g. ["Drinks", "Sides"])
 * @returns an array of rows with keys `time`, `quantity`, and `name`
 */
function getClasses(array $classes): array {
    global $dbh;

    $classes = array_unique($classes);
    if (empty($classes)) {
        return [];
    }

    $placeholder = implode(" ,", array_fill(0, count($classes), "?"));
    $cmd = "SELECT `time`, `quantity`, `name` FROM `productClasses` WHERE `name` IN ($placeholder)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute($classes);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Estimates the total wait time (in minutes) to cook every item in the
 * supplied list of orders. Groups items by product class, converts
 * quantities into the class's batch time, and assumes the kitchen can
 * work on up to `$concurrent` batches of the same class at once.
 * Rounds the final value up to the nearest 5 minutes.
 *
 * @param {array} $orders an array of order-item rows (quantity, productID, productClass)
 * @param {int} $concurrent how many batches of the same class the kitchen can cook in parallel (default 5)
 * @returns the estimated wait time in minutes, rounded up to the nearest 5
 */
function calculateTime(array $orders, int $concurrent = 5): int {
    if (empty($orders)) {
        return 0;
    }

    $orders = reorder($orders, "productClass");
    $classes = reorder(getClasses(array_keys($orders)), "name", true);
    $time = 0;

    foreach ($orders as $className => $arr) {
        if (!isset($classes[$className])) {
            continue;
        }

        $class = $classes[$className];
        $productIDs = [];

        foreach ($arr as $order) {
            $prodID = $order["productID"];
            if (!isset($productIDs[$prodID])) {
                $productIDs[$prodID] = (int) $order["quantity"];
            } else {
                $productIDs[$prodID] += (int) $order["quantity"];
            }
        }

        $batchTimes = [];
        foreach ($productIDs as $quantity) {
            $batchTimes[] = (float) $class["time"] * ceil($quantity / $class["quantity"]);
            if (count($batchTimes) >= $concurrent) {
                $time += min($batchTimes);
                $batchTimes = [];
            }
        }

        if (!empty($batchTimes)) {
            $time += min($batchTimes);
        }
    }

    return roundUpToAny($time, 5);
}

/**
 * Formats the user's order into an html table, including the prices of the items and total spent
 *
 * @param {string} $productName the name of the product that was purchased
 * @param {string} $quantity the quantity of the product that was purchased
 * @param {string} $priceperunit the price per unit of the product that was purchased
 * @param {string} $price the total price of the product that was purchased
 * @param {string} $class the html class of the list element
 * @returns the html formated row
 */
function table_row(?string $productName, ?string $quantity, ?string $priceperunit, ?string $price, ?string $class): string {
    return "<li class=$class><span class='product-name'>$productName</span>
        <span class='product-quantity'>$quantity</span>
        <span class='product-priceperunit'>$priceperunit</span>
        <span class='product-price'>$price</span></li>";
}

/**
 * Formats the user's order into an html table, including the prices of the items and total spent
 *
 * @param {array} $items an array of order-item rows (quantity, productID, productClass)
 * @param {float} $discount the dollar amount off of the subtotal by using promocodes
 * @returns the html formated list of ordered items
 */
function formatOrder(array $items, float $discount): string {
    global $dbh;

    $placeholder = implode(",", array_fill(0, count($items), "?"));
    $cmd = "SELECT `price`, `productName`, `productID` FROM `products` WHERE `productID` IN ($placeholder)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute(get_column($items, "productID"));
    $products = reorder($stmt->fetchAll(PDO::FETCH_ASSOC), "productID", true);

    $total = 0;
    $out = table_row("Product Name", "Quantity", "Price Per Unit", "Price", null);
    foreach ($items as $item) {
        $quantity = $item['quantity'];
        $product = $products[$item['productID']];
        $price = $product["price"] * $quantity;
        $out .= table_row($product["productName"], $quantity, $product["price"], $price, null);
        $total += $price;
    }
    $out .= table_row("Subtotal", null, null, $total, "line-break");
    
    if ($discount !== (float) 0 || $discount === null) {
        $out .= table_row("Discount", null, null, "−" . $discount, null);
        $total -= $discount;
    }

    $tax = round(0.13 * $total, 2);
    $out .= table_row("Tax (13%)", null, null, $tax, null);
    $total += $tax;

    $out .= table_row("Total", null, null, $total, "line-break");

    return $out;
}

/**
 * Entry point for this endpoint. Reads the optional order_id query
 * parameter, looks up the relevant order, factors in the backlog of all
 * orders placed since, and returns a JSON string with the estimated
 * wait time, line items, fulfillment method, and delivery address.
 * Sends a 401 or 404 status code for unauthenticated users and users
 * with no open order.
 *
 * @returns a JSON-encoded response body string
 */
function main(): string {
    if (!isset($_SESSION["userID"])) {
        http_response_code(401);
        return json_encode(["error" => "Please log in to view your order wait time."]);
    }

    $userID = (int) $_SESSION["userID"];
    $requestedOrderID = filter_input(INPUT_GET, "order_id", FILTER_VALIDATE_INT);

    [$orderItems, $orderTime, $fulfillmentMethod, $address, $discountTotal] = getRequestedOrder($userID, $requestedOrderID ?: null);

    if (!$orderItems || $orderTime === -1) {
        http_response_code(404);
        return json_encode(["error" => "No checked-out order was found for this account."]);
    }

    $order_table = formatOrder($orderItems, $discountTotal);

    $allOrders = getAllOrdersSince($orderTime);
    $overallTime = calculateTime($allOrders, 5);

    return json_encode([
        "time" => $overallTime,
        "order" => $orderItems,
        "orderTable" => $order_table,
        "fulfillmentMethod" => $fulfillmentMethod,
        "address" => $address,
    ]);
}

echo main();
