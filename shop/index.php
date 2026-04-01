<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop | Clarence's Kitchen</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/shop.css">
    <script src="../assets/js/global.js"></script>
</head>

<body>
    <header class="site-header">
        <div class="site-title">Clarence's Kitchen</div>
        <div class="header-actions">
            <a class="nav-cta" href="../catering/index.html">Book Catering</a>
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
            <li><a href="../menu">Menu</a></li>
            <li><a href="../catering">Catering</a></li>
            <li><a class="current" href="./">Shop</a></li>
        </ul>
    </nav>

    <main>
        <section class="shop-hero">
            <h1>Shop Clarence's Kitchen</h1>
            <p>
                This page gives the shop section a consistent entry point and a simple layout
                for featured products, pantry items, and branded merchandise.
            </p>
        </section>

        <section class="shop-grid">
            <article class="shop-card">
                <h3>Featured Product</h3>
                <p>Use this area for a signature sauce, meal kit, or limited weekly special.</p>
            </article>
            <article class="shop-card">
                <h3>Merchandise</h3>
                <p>Placeholder space for branded shirts, gift cards, or small restaurant merch.</p>
            </article>
            <article class="shop-card">
                <h3>Pickup Details</h3>
                <p>Add ordering notes, pickup timing, and any local delivery information here.</p>
            </article>
        </section>
    </main>
</body>

</html>