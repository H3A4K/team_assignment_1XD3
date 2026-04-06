<?php
session_start();

include "../assets/php/connect.php";

$cartItems = [];
$cartSubtotal = 0;
$hasOpenOrder = false;
$cartStatusMessage = "Log in to view and complete your order.";
$cartFeedbackMessage = "";
$cartFeedbackIsError = false;

if (isset($_GET["order_completed"])) {
    $cartFeedbackMessage = "Order completed successfully.";
} elseif (isset($_GET["order_error"])) {
    $cartFeedbackMessage = $_GET["order_error"];
    $cartFeedbackIsError = true;
}

if (isset($_SESSION["userID"])) {
    $cartStatusMessage = "Your cart is empty.";

    try {
        $orderStmt = $dbh->prepare("
            SELECT orderID
            FROM orders
            WHERE accountID = ? AND fullfilled = 0
            ORDER BY orderID DESC
            LIMIT 1
        ");
        $orderStmt->execute([$_SESSION["userID"]]);
        $openOrder = $orderStmt->fetch(PDO::FETCH_ASSOC);

        if ($openOrder) {
            $itemsStmt = $dbh->prepare("
                SELECT
                    od.productID,
                    od.quantity,
                    p.productName,
                    p.price
                FROM orderdetails od
                INNER JOIN products p ON p.productID = od.productID
                WHERE od.orderID = ?
                ORDER BY od.orderDetailID ASC
            ");
            $itemsStmt->execute([$openOrder["orderID"]]);
            $cartItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cartItems as &$cartItem) {
                $cartItem["quantity"] = (int) $cartItem["quantity"];
                $cartItem["price"] = (float) $cartItem["price"];
                $cartItem["lineTotal"] = $cartItem["quantity"] * $cartItem["price"];
                $cartSubtotal += $cartItem["lineTotal"];
            }
            unset($cartItem);

            $hasOpenOrder = count($cartItems) > 0;
            if ($hasOpenOrder) {
                $cartStatusMessage = "";
            }
        }
    } catch (Exception $e) {
        $cartStatusMessage = "Unable to load cart right now.";
        $cartFeedbackMessage = "Server error while loading the cart.";
        $cartFeedbackIsError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | Clarence's Kitchen</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/menu.css">
    <script src="../assets/js/menu.js?v=20260406b" defer></script>
    <script src="../assets/js/global.js?v=20260406b" defer></script>
</head>

<body>
    <header class="site-header">
        <div class="site-title">Clarence's Kitchen</div>
        <div class="header-actions">
            <div id="cd-cart-trigger"><a class="nav-cta" href="#cd-cart">Cart</a></div>
            <?php
            if (!isset($_SESSION["email"])) {
            ?>
                <div class="account-menu">
                    <a class="nav-cta account-trigger" href="../login/" aria-label="Account menu">
                        <img src="../assets/images/user.png" alt="">
                        <span>Login/Signup</span>
                    </a>
                    <div class="account-dropdown">
                        <a href="../login/">Log In</a>
                        <a href="../signup/">Sign Up</a>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <span class="login-status">Logged in as <?php echo htmlspecialchars($_SESSION["email"]); ?></span>
                <div class="account-menu" id="logoutbtn">
                    <a class="nav-cta account-trigger" aria-label="Account menu">
                        <img src="../assets/images/user.png" alt="">
                        <span>Logout</span>
                    </a>
                    </form>
                </div>

            <?php
            }
            ?>
        </div>
    </header>

    <nav class="main-nav" aria-label="Main navigation">
        <ul>
            <li><a href="../">Home</a></li>
            <li><a class="current" href="../menu/">Menu</a></li>
            <li><a href="../catering/">Catering</a></li>
        </ul>
    </nav>

    <main>
        <div id="menu-controls">
            <input type="text" id="menu-search" placeholder="Search items...">
            <select id="menu-sort">
                <option value="default">Sort: Default</option>
                <option value="price-asc">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
                <option value="alpha">Alphabetical</option>
            </select>
            <div id="class-filter-wrapper">
                <button id="class-filter-btn" type="button">Filter by Class <span id="class-filter-arrow">&#9660;</span></button>
                <div id="class-filter-dropdown" class="hidden">
                    <!-- class options injected by JS -->
                </div>
            </div>
        </div>
        <div id="menu"></div>

        <div id="cd-shadow-layer"></div>

        <section id="cd-cart">
            <img id="cd-shoppingcart-icon" src="../assets/images/shoppingcart.png" alt="Open cart">
            <button id="cd-close-icon" type="button"><img src="../assets/images/close.png" alt="Close cart"></button>
            <h2 class="cd-cart-heading">Your Cart</h2>
            <p id="cd-cart-status" class="cd-cart-status" <?php if ($cartStatusMessage === "") { ?>hidden<?php } ?>><?php echo htmlspecialchars($cartStatusMessage); ?></p>
            <p id="cd-cart-feedback" class="cd-cart-feedback<?php if ($cartFeedbackIsError) { ?> is-error<?php } ?>" <?php if ($cartFeedbackMessage === "") { ?>hidden<?php } ?>><?php echo htmlspecialchars($cartFeedbackMessage); ?></p>
            <ul class="cd-cart-items">
                <?php foreach ($cartItems as $cartItem) { ?>
                    <li>
                        <div>
                            <span class="cd-qty"><?php echo (int) $cartItem["quantity"]; ?>x</span>
                            <?php echo htmlspecialchars($cartItem["productName"]); ?>
                        </div>
                        <div class="cd-price">$<?php echo number_format($cartItem["lineTotal"], 2); ?></div>
                    </li>
                <?php } ?>
            </ul>

            <div class="cd-cart-total">
                <p>Subtotal <span id="cd-cart-subtotal">$<?php echo number_format($cartSubtotal, 2); ?></span></p>
                <p>Total <span id="cd-cart-total">$<?php echo number_format($cartSubtotal, 2); ?></span></p>
            </div>

            <form id="cd-cart-checkout-form" action="../assets/php/complete_order_action.php" method="post">
                <button id="cd-cart-checkout" class="checkout-btn" type="submit" <?php if (!$hasOpenOrder) { ?>disabled<?php } ?>>Complete Order</button>
            </form>
            <p class="cd-go-to-cart">Your open order stays here until you complete it.</p>
        </section>
    </main>
</body>

</html>
