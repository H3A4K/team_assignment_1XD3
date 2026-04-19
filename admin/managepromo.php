<?php
/**
 * admin/managepromo.php
 *
 * Admin page for managing discount promo codes (the codes customers
 * type at checkout). The table of codes is rendered client-side by
 * manage_promo_codes.js; the popup is the add/edit form where admins
 * set the code text, discount type and value, status, expiry date, and
 * any required product IDs that must be in the cart for the code to
 * apply.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 03, 2026
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
    <title>Manage Promos | Clarence's Kitchen</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- <script src="../assets/js/admin.js" defer></script> -->
    <script src="../assets/js/manage_promo_codes.js" defer></script>
    <script src="../assets/js/global.js" defer></script>
</head>

<body>
    <div id="popup" class="popup" >
        <h3>Add/Edit Promo Code</h3>
        <div id="add-promo-code-form" class="form">
            <div class="layout-column">
                <input class="form-control" style="display: none;" type="text" id="promoID" placeholder="ID">
                <label for="promoCode">Promo Code Name:</label>
                <input class="form-control" type="text" id="promoCode" placeholder="Promo Code Name" required>
                <label for="discountType">Discount Type:</label>
                <select class="form-control" id="discountType">
                    <option value="percentage">Percentage</option>
                    <option value="fixed">Fixed</option>
                </select>
            </div>
            
            <div class="layout-column">
                <!-- <input class="form-control" type="text" rows="3" id="discountType" placeholder="Discount Type" required></input> -->
                <label for="discountValue">Discount Value:</label>
                <input class="form-control" type="number" step="1.0" id="discountValue" placeholder="Discount Value" required>
                <label for="active">Status:</label>
                <select class="form-control" id="active">
                    <option value="0">Inoperative</option>
                    <option value="1">Active</option>
                </select>
                <label for="expiryDate">Expiry Date:</label>
                <input type="date" id="expiryDate" min="2026-01-01">
                <label for="requiredProductIDs">Required Product IDs (optional):</label>
                <input class="form-control" type="text" id="requiredProductIDs" placeholder="e.g. 101,103">
                <p class="form-hint">Comma-separated product IDs that must all be in the cart for this code to apply. Leave blank for a code with no conditions.</p>
            </div>
            
        </div>
        <div>
            <button class="primary-button" type="submit" id="savePromoCodeBtn">Save</button>
            <button class="secondary-button" id="cancelBtn">Cancel</button>
        </div>
    </div>

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
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="manageproducts.php">Products</a></li>
            <li><a href="manageorders.php">Orders</a></li>
            <li><a class="current" href="managepromo.php">Promo Codes</a></li>
            <li><a href="managepromotions.php">Promotions</a></li>
            <?php } ?>
        </ul>
    </nav>

    <main>
        <?php if ($user_is_admin) { ?>
        <div class="toolbar">
            <p>Promo Codes</p>
            <button id="addPromoCodeBtn" class="primary-button last-item">Add Promo Code</button>
        </div>
        <div id="promocodes-table"></div>
        <div class="toolbar">
            <button id="bottomAddPromoCodeBtn" class="primary-button last-item">Add Promo Code</button>
        </div>
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
