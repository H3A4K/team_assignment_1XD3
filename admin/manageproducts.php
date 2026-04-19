<?php
/**
 * admin/manageproducts.php
 *
 * Admin page for adding, editing, and removing menu products. The
 * product list is rendered client-side by manage_products.js; the popup
 * at the top of the page is the add/edit form (name, description, price,
 * class, and image upload) that the same script shows when the admin
 * clicks "Add Product" or the per-row "Edit" button.
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
    <title>Manage Products | Clarence's Kitchen</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- <script src="../assets/js/admin.js" defer></script> -->
    <script src="../assets/js/manage_products.js" defer></script>
    <script src="../assets/js/global.js" defer></script>
</head>

<body>
    <div id="popup" class="popup" >
        <h3>Add/Edit Menu Item</h3>
        <div id="add-product-form" class="form">
            <input class="form-control" style="display: none;" type="text" id="productID" placeholder="ID" >
            <input type="hidden" id="currentProductImg" value="">
            <label for="productName">Product Name:</label>
            <input class="form-control" type="text" id="productName" placeholder="Name" required>
            <label for="productDesc">Product Description:</label>
            <textarea class="form-control" type="text" rows="3" id="productDesc" placeholder="Description" required></textarea>
            <label for="price">Price:</label>
            <input class="form-control" type="number" step="1.0" id="price" placeholder="Price" required>
            <label for="productClassesSelect">Product Class:</label>
            <select class="form-control" id="productClassesSelect" name="productClass">
            </select>
            <label for="productImgFile">Product Image:</label>
            <div class="image-upload-row">
                <img id="productImgPreview" class="product-img-preview" src="../assets/images/menu/placeholder.jpg" alt="Product image preview">
                <input class="form-control" type="file" id="productImgFile" accept="image/png,image/jpeg">
            </div>
        </div>
        <div>
            <button class="primary-button" type="submit" id="saveProductBtn">Save</button>
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
            <li><a class="current" href="manageproducts.php">Products</a></li>
            <li><a href="manageorders.php">Orders</a></li>
            <li><a href="managepromo.php">Promo Codes</a></li>
            <li><a href="managepromotions.php">Promotions</a></li>
            <?php } ?>
        </ul>
    </nav>

        
    <main>
        <?php if ($user_is_admin) { ?>
        <div class="toolbar">
            <p>Products</p>
            <button id="addProductBtn" class="primary-button last-item">Add Product</button>
        </div>
        <div id="product-table"></div>
        <div class="toolbar">
            <button id="bottomAddProductBtn" class="primary-button last-item">Add Product</button>
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
