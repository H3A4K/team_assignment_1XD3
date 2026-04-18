<?php

include "connect.php";

session_start();

header("Content-Type: application/json");

function roundUpToAny(int|float $n, int $x): int {
    return (int) (ceil($n / $x) * $x);
}

function unassign_array(array $arr, string $key): array {
    $out = [];
    foreach ($arr as $sub) {
        $out[] = $sub[$key];
    }
    return $out;
}

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

function ordersHasFulfillmentMethodColumn(PDO $dbh): bool {
    static $hasColumn = null;
    if ($hasColumn !== null) {
        return $hasColumn;
    }

    $stmt = $dbh->query("SHOW COLUMNS FROM orders LIKE 'fulfillmentMethod'");
    $hasColumn = $stmt !== false && $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    return $hasColumn;
}

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

function getRequestedOrder(int $userID, ?int $orderID): array {
    global $dbh;

    $selectFields = ordersHasFulfillmentMethodColumn($dbh)
        ? "orderID, orderDate, address, fulfillmentMethod"
        : "orderID, orderDate, address";

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
            $order["fulfillmentMethod"] ?? "pickup",
            $order["address"] ?? "",
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
        $order["fulfillmentMethod"] ?? "pickup",
        $order["address"] ?? "",
    ];
}

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

function main(): string {
    if (!isset($_SESSION["userID"])) {
        http_response_code(401);
        return json_encode(["error" => "Please log in to view your order wait time."]);
    }

    $userID = (int) $_SESSION["userID"];
    $requestedOrderID = filter_input(INPUT_GET, "order_id", FILTER_VALIDATE_INT);

    [$orderItems, $orderTime, $fulfillmentMethod, $address] = getRequestedOrder($userID, $requestedOrderID ?: null);

    if (!$orderItems || $orderTime === -1) {
        http_response_code(404);
        return json_encode(["error" => "No checked-out order was found for this account."]);
    }

    $allOrders = getAllOrdersSince($orderTime);
    $overallTime = calculateTime($allOrders, 5);

    return json_encode([
        "time" => $overallTime,
        "order" => $orderItems,
        "fulfillmentMethod" => $fulfillmentMethod,
        "address" => $address,
    ]);
}

echo main();
