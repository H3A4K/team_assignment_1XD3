<?php
/**
 * admin.php
 *
 * The single backend endpoint powering the admin dashboard. Handles
 * every create/read/update/delete operation on products, orders, promo
 * codes, and visual promotions. The operation is selected with a GET
 * query-string flag (e.g. ?saveProduct, ?removePromoCode). Every request
 * is gated behind an isAdmin() check so non-admins cannot reach the data.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 03, 2026
 */

session_start();

include "connect.php";
include "promo_requirements.php";
include "security.php";

$is_user_admin = isAdmin();

if (!$is_user_admin) {
    http_response_code(404);
    exit();
}

/**
 * Fetches every row from the products table.
 *
 * @returns an array of product rows (each an associative array)
 */
function getAllProducts() {
    global $dbh;
    $cmd = "SELECT * FROM products";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $products = $stmt->fetchAll();
    return $products;
}

/**
 * Fetches every row from the orders table.
 *
 * @returns an array of order rows (each an associative array)
 */
function getAllOrders() {
    global $dbh;
    $cmd = "SELECT * FROM orders";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $orders = $stmt->fetchAll();
    return $orders;
}

/**
 * Fetches every promo code (both active and inactive) from the
 * promocodes table.
 *
 * @returns an array of promo code rows (each an associative array)
 */
function GetAllPromoCodes() {
    global $dbh;
    $cmd = "SELECT * FROM promocodes";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $promocodes = $stmt->fetchAll();
    return $promocodes;
}

/**
 * Fetches every product class (kitchen station) so the admin UI can
 * populate its class-selector dropdown.
 *
 * @returns an array of product class rows (each an associative array)
 */
function GetProductClasses() {
    global $dbh;
    $cmd = "SELECT * from productClasses";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $classes = $stmt->fetchAll();
    return $classes;
}

// PRODUCTS
/**
 * Inserts a brand new product row.
 *
 * @param {String} $productName the display name of the product
 * @param {String} $productDesc the description shown on the menu
 * @param {float} $price the price in dollars
 * @param {String} $productImg the filename (not full path) of the product image
 * @param {String} $productClass the kitchen class/station this product belongs to
 */
function insertProduct($productName, $productDesc, $price, $productImg, $productClass) {
    global $dbh;
    $cmd = "INSERT INTO products (productName, productDesc, price, productImg, productClass) VALUES (?, ?, ?, ?, ?)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productName, $productDesc, $price, $productImg, $productClass]);
}

/**
 * Overwrites the fields of an existing product.
 *
 * @param {int} $productID the ID of the product to update
 * @param {String} $productName the new display name
 * @param {String} $productDesc the new description
 * @param {float} $price the new price in dollars
 * @param {String} $productImg the new image filename
 * @param {String} $productClass the new kitchen class
 */
function updateProduct($productID, $productName, $productDesc, $price, $productImg, $productClass) {
    global $dbh;
    $cmd = "UPDATE products SET productName = ?,  productDesc = ?, price = ?, productImg = ?, productClass = ? WHERE productID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productName, $productDesc, $price, $productImg, $productClass, $productID]);
}

/**
 * Processes the product image upload that came with a save-product
 * request. Validates the file is a real JPEG or PNG under 5 MB, writes
 * it under assets/images/menu/ with a generated filename, and returns
 * the filename to store in the database. Falls back to the current
 * image (or placeholder.jpg) when the admin didn't upload a new file
 * or something goes wrong.
 *
 * @param {String} $currentProductImg the filename already stored in the DB (so we can keep it when no new file is uploaded)
 * @returns the filename that should be saved to the products.productImg column
 */
function handleProductImageUpload($currentProductImg) {
    // Sanitize currentProductImg: allow only simple filename chars (no slashes, no ..)
    if ($currentProductImg === NULL || !preg_match('/^[a-zA-Z0-9_.\-]+$/', $currentProductImg)) {
        $currentProductImg = "";
    }
    $fallback = $currentProductImg !== "" ? $currentProductImg : "placeholder.jpg";

    // No file uploaded (or the field wasn't in the POST) -> keep existing
    if (!isset($_FILES["productImgFile"])) {
        return $fallback;
    }
    $file = $_FILES["productImgFile"];
    if ($file["error"] === UPLOAD_ERR_NO_FILE) {
        return $fallback;
    }
    if ($file["error"] !== UPLOAD_ERR_OK) {
        error_log("Product image upload failed with error code: " . $file["error"]);
        return $fallback;
    }

    // Size limit: 5 MB
    if ($file["size"] > 5 * 1024 * 1024) {
        error_log("Product image upload rejected: size " . $file["size"] . " exceeds 5MB");
        return $fallback;
    }

    // Validate the uploaded file is actually an image and pick extension from its real mime
    $imgInfo = @getimagesize($file["tmp_name"]);
    if ($imgInfo === false) {
        error_log("Product image upload rejected: not a valid image");
        return $fallback;
    }
    $mimeToExt = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
    ];
    $mime = $imgInfo["mime"];
    if (!isset($mimeToExt[$mime])) {
        error_log("Product image upload rejected: unsupported mime type " . $mime);
        return $fallback;
    }
    $ext = $mimeToExt[$mime];

    // Generate a safe server-side filename; never trust client filename
    $newFilename = "product_" . uniqid("", true) . "." . $ext;
    // Replace dots in the uniqid portion so we end up with exactly one extension
    $newFilename = preg_replace('/\.(?=.*\.)/', "_", $newFilename);

    $destDir = __DIR__ . "/../images/menu/";
    $destination = $destDir . $newFilename;

    if (!is_dir($destDir)) {
        error_log("Product image upload failed: destination directory does not exist: " . $destDir);
        return $fallback;
    }
    if (!move_uploaded_file($file["tmp_name"], $destination)) {
        error_log("Product image upload failed: move_uploaded_file could not write to " . $destination);
        return $fallback;
    }

    return $newFilename;
}

/**
 * Deletes a product row by its ID.
 *
 * @param {int} $productID the ID of the product to remove
 */
function removeProduct($productID) {
    global $dbh;
    $cmd = "DELETE FROM products WHERE productID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productID]);
}

// ORDERS
/**
 * Updates the fulfillment status of a customer order.
 *
 * @param {int} $orderID the ID of the order to update
 * @param {int} $fullfilled 1 if the order is completed, 0 if still open
 */
function updateOrderStatus($orderID, $fullfilled) {
    global $dbh;
    $cmd = "UPDATE orders SET fullfilled = ? WHERE orderID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$fullfilled, $orderID]);
}

// PROMO CODES
/**
 * Inserts a new discount promo code.
 *
 * @param {String} $promoCode the human-readable code the customer types in (e.g. "WELCOME10")
 * @param {String} $discountType either "percentage" or "fixed"
 * @param {float} $discountValue the percentage (0-100) or fixed-dollar amount of the discount
 * @param {int} $active 1 if usable right away, 0 if temporarily disabled
 * @param {String} $expiryDate a YYYY-MM-DD date after which the code should stop working (may be empty)
 * @param {String} $requiredProductIDs canonical comma-separated list of product IDs the cart must contain (or NULL)
 */
function insertPromoCode($promoCode, $discountType, $discountValue, $active, $expiryDate, $requiredProductIDs) {
    global $dbh;
    $cmd = "INSERT INTO promocodes (promoCode, discountType, discountValue, active, expiryDate, requiredProductIDs) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$promoCode, $discountType, $discountValue, $active, $expiryDate, $requiredProductIDs]);
}

/**
 * Overwrites the fields of an existing promo code.
 *
 * @param {int} $promoID the ID of the promo code to update
 * @param {String} $promoCode the new customer-facing code text
 * @param {String} $discountType "percentage" or "fixed"
 * @param {float} $discountValue the new percentage or fixed-dollar amount
 * @param {int} $active 1 or 0
 * @param {String} $expiryDate YYYY-MM-DD date (may be empty)
 * @param {String} $requiredProductIDs canonical comma-separated list of required product IDs (or NULL)
 */
function updatePromoCode($promoID, $promoCode, $discountType, $discountValue, $active, $expiryDate, $requiredProductIDs) {
    global $dbh;
    $cmd = "UPDATE promocodes SET promoCode = ?, discountType = ?, discountValue = ?, active = ?, expiryDate = ?, requiredProductIDs = ? WHERE promoID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$promoCode, $discountType, $discountValue, $active, $expiryDate, $requiredProductIDs, $promoID]);
}

/**
 * Deletes a promo code by its ID.
 *
 * @param {int} $promoID the ID of the promo code to remove
 */
function removePromoCode($promoID) {
    global $dbh;
    $cmd = "DELETE FROM promocodes WHERE promoID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$promoID]);
}

// PROMOTIONS (visual menu banners, distinct from discount promoCodes)
/**
 * Fetches every promotional banner (both active and inactive), ordered
 * the way they should appear on the menu page.
 *
 * @returns an array of promotion rows (each an associative array)
 */
function getAllPromotions() {
    global $dbh;
    $cmd = "SELECT * FROM promotions ORDER BY sortOrder ASC, promotionID ASC";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Fetches only the promotional banners currently marked as active. These
 * are the ones actually displayed to customers on the menu page.
 *
 * @returns an array of active promotion rows (each an associative array)
 */
function getActivePromotions() {
    global $dbh;
    $cmd = "SELECT * FROM promotions WHERE active = 1 ORDER BY sortOrder ASC, promotionID ASC";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Inserts a new promotional banner row.
 *
 * @param {String} $title the main headline on the banner
 * @param {String} $eyebrow small text shown above the title
 * @param {String} $price display price text (e.g. "$22.99"); purely cosmetic
 * @param {String} $badge badge text shown in the corner (e.g. "Limited Time")
 * @param {String} $description body copy shown below the banner image
 * @param {String} $finePrint rules/fine print, one per line
 * @param {String} $image filename of the banner image (without the images/menu/ prefix)
 * @param {String} $theme colour theme ("orange", "brown", "green", or "blue")
 * @param {String} $ctaLabel button label (e.g. "Order Now")
 * @param {int} $active 1 if the banner should show on the menu page, 0 if hidden
 * @param {int} $sortOrder lower numbers show first
 * @param {String} $promoCode matching promo code for display (may be NULL)
 */
function insertPromotion($title, $eyebrow, $price, $badge, $description, $finePrint, $image, $theme, $ctaLabel, $active, $sortOrder, $promoCode) {
    global $dbh;
    $cmd = "INSERT INTO promotions (title, eyebrow, price, badge, description, finePrint, image, theme, ctaLabel, active, sortOrder, promoCode)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$title, $eyebrow, $price, $badge, $description, $finePrint, $image, $theme, $ctaLabel, $active, $sortOrder, $promoCode]);
}

/**
 * Overwrites the fields of an existing promotional banner.
 *
 * @param {int} $promotionID the ID of the promotion to update
 * @param {String} $title the new headline
 * @param {String} $eyebrow the new eyebrow text
 * @param {String} $price the new display price text
 * @param {String} $badge the new badge text
 * @param {String} $description the new body copy
 * @param {String} $finePrint the new fine-print rules (one per line)
 * @param {String} $image the new image filename
 * @param {String} $theme the new colour theme
 * @param {String} $ctaLabel the new button label
 * @param {int} $active 1 for visible, 0 for hidden
 * @param {int} $sortOrder the new display order
 * @param {String} $promoCode matching promo code for display (may be NULL)
 */
function updatePromotion($promotionID, $title, $eyebrow, $price, $badge, $description, $finePrint, $image, $theme, $ctaLabel, $active, $sortOrder, $promoCode) {
    global $dbh;
    $cmd = "UPDATE promotions SET title=?, eyebrow=?, price=?, badge=?, description=?, finePrint=?, image=?, theme=?, ctaLabel=?, active=?, sortOrder=?, promoCode=? WHERE promotionID=?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$title, $eyebrow, $price, $badge, $description, $finePrint, $image, $theme, $ctaLabel, $active, $sortOrder, $promoCode, $promotionID]);
}

/**
 * Deletes a promotional banner by its ID.
 *
 * @param {int} $promotionID the ID of the promotion to remove
 */
function removePromotion($promotionID) {
    global $dbh;
    $cmd = "DELETE FROM promotions WHERE promotionID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$promotionID]);
}

/**
 * Processes the banner image upload that came with a save-promotion
 * request. Performs the same validation and safe filename logic as
 * handleProductImageUpload() but writes to a different filename prefix
 * so product and promotion uploads never collide.
 *
 * @param {String} $currentPromotionImg the filename currently stored in the DB
 * @returns the filename that should be saved to promotions.image
 */
function handlePromotionImageUpload($currentPromotionImg) {
    if ($currentPromotionImg === NULL || !preg_match('/^[a-zA-Z0-9_.\-]+$/', $currentPromotionImg)) {
        $currentPromotionImg = "";
    }
    $fallback = $currentPromotionImg !== "" ? $currentPromotionImg : "placeholder.jpg";

    if (!isset($_FILES["promotionImgFile"])) return $fallback;
    $file = $_FILES["promotionImgFile"];
    if ($file["error"] === UPLOAD_ERR_NO_FILE) return $fallback;
    if ($file["error"] !== UPLOAD_ERR_OK) {
        error_log("Promotion image upload failed with error code: " . $file["error"]);
        return $fallback;
    }
    if ($file["size"] > 5 * 1024 * 1024) {
        error_log("Promotion image upload rejected: exceeds 5MB");
        return $fallback;
    }
    $imgInfo = @getimagesize($file["tmp_name"]);
    if ($imgInfo === false) {
        error_log("Promotion image upload rejected: not a valid image");
        return $fallback;
    }
    $mimeToExt = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
    ];
    $mime = $imgInfo["mime"];
    if (!isset($mimeToExt[$mime])) {
        error_log("Promotion image upload rejected: unsupported mime " . $mime);
        return $fallback;
    }
    $ext = $mimeToExt[$mime];
    $newFilename = "promo_" . uniqid("", true) . "." . $ext;
    $newFilename = preg_replace('/\.(?=.*\.)/', "_", $newFilename);

    $destDir = __DIR__ . "/../images/menu/";
    $destination = $destDir . $newFilename;
    if (!is_dir($destDir)) {
        error_log("Promotion image upload failed: destination dir missing: " . $destDir);
        return $fallback;
    }
    if (!move_uploaded_file($file["tmp_name"], $destination)) {
        error_log("Promotion image upload failed: move_uploaded_file failed");
        return $fallback;
    }
    return $newFilename;
}

$getAllProducts = filter_input(INPUT_GET, "getAllProducts", FILTER_SANITIZE_SPECIAL_CHARS);
$getAllOrders = filter_input(INPUT_GET, "getAllOrders", FILTER_SANITIZE_SPECIAL_CHARS);
$getAllPromoCodes = filter_input(INPUT_GET, "getAllPromoCodes", FILTER_SANITIZE_SPECIAL_CHARS);
$getProductClasses = filter_input(INPUT_GET, "getProductClasses", FILTER_SANITIZE_SPECIAL_CHARS);
$saveProduct = filter_input(INPUT_GET, "saveProduct", FILTER_SANITIZE_SPECIAL_CHARS);
$removeProduct = filter_input(INPUT_GET, "removeProduct", FILTER_SANITIZE_SPECIAL_CHARS);
$editOrderStatus = filter_input(INPUT_GET, "editOrderStatus", FILTER_SANITIZE_SPECIAL_CHARS);
$savePromoCode = filter_input(INPUT_GET, "savePromoCode", FILTER_SANITIZE_SPECIAL_CHARS);
$removePromoCode = filter_input(INPUT_GET, "removePromoCode", FILTER_SANITIZE_SPECIAL_CHARS);
$getAllPromotions = filter_input(INPUT_GET, "getAllPromotions", FILTER_SANITIZE_SPECIAL_CHARS);
$savePromotion = filter_input(INPUT_GET, "savePromotion", FILTER_SANITIZE_SPECIAL_CHARS);
$removePromotion = filter_input(INPUT_GET, "removePromotion", FILTER_SANITIZE_SPECIAL_CHARS);

if ($getAllProducts !== NULL) {
    $products = getAllProducts();
    echo json_encode($products);
}
else if ($getAllOrders !== NULL) {
    $orders = getAllOrders();
    echo json_encode($orders);
}
else if ($getAllPromoCodes !== NULL) {
    $promocodes = GetAllPromoCodes();
    echo json_encode($promocodes);
}
else if ($getProductClasses !== NULL) {
    $classes = GetProductClasses();
    echo json_encode($classes);
}
else if ($saveProduct !== NULL) {
    $productID = filter_input(INPUT_POST, "productID", FILTER_SANITIZE_SPECIAL_CHARS);
    $productName = filter_input(INPUT_POST, "productName", FILTER_SANITIZE_SPECIAL_CHARS);
    $productDesc = filter_input(INPUT_POST, "productDesc", FILTER_SANITIZE_SPECIAL_CHARS);
    $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
    $productClass = filter_input(INPUT_POST, "productClass", FILTER_SANITIZE_SPECIAL_CHARS);
    $currentProductImg = filter_input(INPUT_POST, "currentProductImg", FILTER_SANITIZE_SPECIAL_CHARS);
    $productImg = handleProductImageUpload($currentProductImg);
    if ($productID !== NULL && $productID !== "") {
        updateProduct($productID, $productName, $productDesc, $price, $productImg, $productClass);
    }
    else {
        insertProduct($productName, $productDesc, $price, $productImg, $productClass);
    }    
    $products = getAllProducts();
    echo json_encode($products);
}
else if ($removeProduct !== NULL) {
    $productID = filter_input(INPUT_POST, "productID", FILTER_VALIDATE_INT);
    removeProduct($productID);
    $products = getAllProducts();
    echo json_encode($products);
} 
else if ($editOrderStatus !== NULL) {
    $orderID = filter_input(INPUT_POST, "orderID", FILTER_VALIDATE_INT);
    $fullfilled = filter_input(INPUT_POST, "fullfilled", FILTER_VALIDATE_INT);
    if ($orderID !== NULL && $orderID !== "") {
        updateOrderStatus($orderID, $fullfilled);
    }
    $orders = getAllOrders();
    echo json_encode($orders);
}
else if ($savePromoCode !== NULL) {
    $promoID = filter_input(INPUT_POST, "promoID", FILTER_SANITIZE_SPECIAL_CHARS);
    $promoCode = filter_input(INPUT_POST, "promoCode", FILTER_SANITIZE_SPECIAL_CHARS);
    $discountType = filter_input(INPUT_POST, "discountType", FILTER_SANITIZE_SPECIAL_CHARS);
    $discountValue = filter_input(INPUT_POST, "discountValue", FILTER_VALIDATE_FLOAT);
    $active = filter_input(INPUT_POST, "active", FILTER_SANITIZE_SPECIAL_CHARS);
    $expiryDate = filter_input(INPUT_POST, "expiryDate", FILTER_SANITIZE_SPECIAL_CHARS);
    $requiredProductIDsRaw = filter_input(INPUT_POST, "requiredProductIDs", FILTER_SANITIZE_SPECIAL_CHARS);
    $requiredProductIDs = sanitizeRequiredProductIDs($requiredProductIDsRaw);

    if ($promoID !== NULL && $promoID !== "") {
        updatePromoCode($promoID, $promoCode, $discountType, $discountValue, $active, $expiryDate, $requiredProductIDs);
    }
    else {
        insertPromoCode($promoCode, $discountType, $discountValue, $active, $expiryDate, $requiredProductIDs);
    }    
    $promoCodes = GetAllPromoCodes();
    echo json_encode($promoCodes);
}  
else if ($removePromoCode !== NULL) {
    $promoID = filter_input(INPUT_POST, "promoID", FILTER_VALIDATE_INT);
    removePromoCode($promoID);
    $promocode = GetAllPromoCodes();
    echo json_encode($promocode);
}
else if ($getAllPromotions !== NULL) {
    echo json_encode(getAllPromotions());
}
else if ($savePromotion !== NULL) {
    $promotionID = filter_input(INPUT_POST, "promotionID", FILTER_SANITIZE_SPECIAL_CHARS);
    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_SPECIAL_CHARS);
    $eyebrow = filter_input(INPUT_POST, "eyebrow", FILTER_SANITIZE_SPECIAL_CHARS);
    $price = filter_input(INPUT_POST, "price", FILTER_SANITIZE_SPECIAL_CHARS);
    $badge = filter_input(INPUT_POST, "badge", FILTER_SANITIZE_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_SPECIAL_CHARS);
    $finePrint = filter_input(INPUT_POST, "finePrint", FILTER_SANITIZE_SPECIAL_CHARS);
    $theme = filter_input(INPUT_POST, "theme", FILTER_SANITIZE_SPECIAL_CHARS);
    $ctaLabel = filter_input(INPUT_POST, "ctaLabel", FILTER_SANITIZE_SPECIAL_CHARS);
    $active = filter_input(INPUT_POST, "active", FILTER_VALIDATE_INT);
    $sortOrder = filter_input(INPUT_POST, "sortOrder", FILTER_VALIDATE_INT);
    $currentPromotionImg = filter_input(INPUT_POST, "currentPromotionImg", FILTER_SANITIZE_SPECIAL_CHARS);
    $promoCodeRaw = filter_input(INPUT_POST, "promoCode", FILTER_SANITIZE_SPECIAL_CHARS);

    // Whitelist theme to one of the CSS-backed values; default to orange
    $allowedThemes = ["orange", "brown", "green", "blue"];
    if (!in_array($theme, $allowedThemes, true)) {
        $theme = "orange";
    }
    if ($active === NULL) $active = 0;
    if ($sortOrder === NULL) $sortOrder = 0;
    if ($ctaLabel === NULL || $ctaLabel === "") $ctaLabel = "Order Now";

    // Promo code is purely for display on the promo card — the existing
    // promocodes table is what actually validates it at checkout time.
    $promoCode = $promoCodeRaw !== NULL ? trim((string) $promoCodeRaw) : "";
    if ($promoCode === "") $promoCode = NULL;

    $image = handlePromotionImageUpload($currentPromotionImg);

    if ($promotionID !== NULL && $promotionID !== "") {
        updatePromotion($promotionID, $title, $eyebrow, $price, $badge, $description, $finePrint, $image, $theme, $ctaLabel, $active, $sortOrder, $promoCode);
    } else {
        insertPromotion($title, $eyebrow, $price, $badge, $description, $finePrint, $image, $theme, $ctaLabel, $active, $sortOrder, $promoCode);
    }
    echo json_encode(getAllPromotions());
}
else if ($removePromotion !== NULL) {
    $promotionID = filter_input(INPUT_POST, "promotionID", FILTER_VALIDATE_INT);
    removePromotion($promotionID);
    echo json_encode(getAllPromotions());
}
?>