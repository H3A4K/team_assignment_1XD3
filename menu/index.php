<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | Clarence's Kitchen</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/menu.css">
    <script src="../assets/js/menu.js"></script>
    <script src="../assets/js/global.js"></script>
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
                    <a class="nav-cta account-trigger" href="../login" aria-label="Account menu">
                        <img src="../assets/images/user.png" alt="">
                        <span>Login/Signup</span>
                    </a>
                    <div class="account-dropdown">
                        <a href="../login">Log In</a>
                        <a href="../signup">Sign Up</a>
                    </div>
                </div>
            <?php
            } else {
            ?>
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
            <li><a class="current" href="./">Menu</a></li>
            <li><a href="../catering">Catering</a></li>
            <li><a href="../shop">Shop</a></li>
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
            <ul class="cd-cart-items">
                <li>
                    <div>
                        <span class="cd-qty">1x</span> Product Name
                    </div>
                    <div class="cd-price">$9.99</div>
                    <a href="#0" class="cd-item-remove">Remove</a>
                </li>

                <li>
                    <div>
                        <span class="cd-qty">2x</span> Product Name
                    </div>
                    <div class="cd-price">$19.98</div>
                    <a href="#0" class="cd-item-remove">Remove</a>
                </li>

                <li>
                    <div>
                        <span class="cd-qty">1x</span> Product Name
                    </div>
                    <div class="cd-price">$9.99</div>
                    <a href="#0" class="cd-item-remove">Remove</a>
                </li>
            </ul>

            <div class="cd-cart-total">
                <p>Subtotal <span>$33.37</span></p>
                <p>Total <span>$39.96</span></p>
            </div>

            <a href="#0" class="checkout-btn">Checkout</a>
            <p class="cd-go-to-cart"><a href="#0">Go to cart page</a></p>
        </section>
    </main>
</body>

</html>