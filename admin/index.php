<?php
/**
 * admin/index.php
 *
 * The admin dashboard landing page. Shows a welcome message and the
 * admin navigation links (Products, Orders, Promo Codes, Promotions) to
 * admin users, or a security-error message to anyone who manages to
 * reach the page without admin rights.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: March 25, 2026
 */
session_start();

include "../assets/php/security.php";

$is_user_admin = isAdmin();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="../assets/js/global.js" defer></script>
    <title>Admin Dashboard | Clarence's Kitchen</title>
</head>

<body>
    <header class="site-header">
        <a href="../" class="site-title"><img src="../assets/images/logo.png" alt="" class="site-logo">Clarence's Kitchen</a>
        <button class="hamburger" id="hamburger-toggle" aria-label="Toggle navigation" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <div class="header-actions">
            <div class="account-menu">
                <a class="nav-cta account-trigger" href="../login/" aria-label="Logout">
                    <img src="../assets/images/user.png" alt="">
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </header>

    <nav class="main-nav" id="main-nav" aria-label="Main navigation">
        <ul>
            <li><a href="../">Home</a></li>
            <li><a href="../menu">Menu</a></li>
            <li><a href="../catering/">Catering</a></li>
            <?php if ($user_is_admin) { ?>
            <li><a class="current" href="index.php">Dashboard</a></li>
            <li><a href="manageproducts.php">Products</a></li>
            <li><a href="manageorders.php">Orders</a></li>
            <li><a href="managepromo.php">Promo Codes</a></li>
            <li><a href="managepromotions.php">Promotions</a></li>
            <?php } ?>
        </ul>
    </nav>

    <main>
        <?php if ($user_is_admin) { ?>
        <h3>Welcome to the Admin Dashboard</h3>
        <p>Select a section from the navigation above to manage products, orders, or promo codes.</p>
        <?php } else { 
            echo "<h3>";
            echo $security_error;
            echo "</h3>";
            } ?>
        
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <img src="../assets/images/logo.png" alt="Clarence's Kitchen" class="footer-logo">
                <span class="footer-title">Clarence's Kitchen</span>
                <p>Bold flavour, warm service, and comfort food that shows up.</p>
                <div class="footer-socials">
                    <a href="https://www.instagram.com/clarenceskitchen/" target="_blank" rel="noopener" aria-label="Instagram"><img src="../assets/images/instagram.png" alt="Instagram"></a>
                    <a href="https://www.facebook.com/ClarencesKitchen/" target="_blank" rel="noopener" aria-label="Facebook"><img src="../assets/images/facebook.png" alt="Facebook"></a>
                </div>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="../">Home</a></li>
                    <li><a href="../menu/">Menu</a></li>
                    <li><a href="../catering/">Catering</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>Contact</h4>
                <ul>
                    <li>(647)-438-8589</li>
                    <li>clarenceskitchen@gmail.com</li>
                    <li>8 Glen Watford Drive, Scarborough, ON</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Clarence's Kitchen. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>
