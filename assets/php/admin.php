<?php 
session_start();

include "connect.php";

function getAllProducts() {
    global $dbh;
    $cmd = "SELECT * FROM products";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $products = $stmt->fetchAll();
    return $products;
}

function getAllOrders() {
    global $dbh;
    $cmd = "SELECT * FROM orders";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $orders = $stmt->fetchAll();
    return $orders;
}

function GetAllPromoCodes() {
    global $dbh;
    $cmd = "SELECT * FROM promocodes";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $promocodes = $stmt->fetchAll();
    return $promocodes;
}

function GetProductClasses() {
    global $dbh;
    $cmd = "SELECT * from productClasses";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute();
    $classes = $stmt->fetchAll();
    return $classes;
}

// PRODUCTS
function insertProduct($productName, $productDesc, $price, $productImg, $productClass) {
    global $dbh;
    $cmd = "INSERT INTO products (productName, productDesc, price, productImg, productClass) VALUES (?, ?, ?, ?, ?)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productName, $productDesc, $price, $productImg, $productClass]);
}

function updateProduct($productID, $productName, $productDesc, $price, $productImg, $productClass) {
    global $dbh;
    $cmd = "UPDATE products SET productName = ?,  productDesc = ?, price = ?, productImg = ?, productClass = ? WHERE productID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productName, $productDesc, $price, $productImg, $productClass, $productID]);
}

/**
 * Handles an optional product-image file upload from the admin "Add/Edit product" form.
 *
 * Returns the filename to store in products.productImg:
 *   - If a valid image was uploaded, moves it into assets/images/menu/ and returns the new filename.
 *   - If no new file was uploaded, returns the provided $currentProductImg (for edits).
 *   - If $currentProductImg is empty/invalid (new product) and no file uploaded, returns "placeholder.jpg".
 *   - On any validation/move failure, falls back to $currentProductImg (or placeholder.jpg).
 *
 * Security:
 *   - $currentProductImg is constrained to safe filename chars to prevent path traversal.
 *   - Uploaded files are validated via getimagesize() (rejects non-images).
 *   - Filename is regenerated server-side (uniqid + mime-derived extension), never trusted from client.
 *   - Size capped at 5 MB.
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
        "image/gif"  => "gif",
        "image/webp" => "webp",
    ];
    $mime = $imgInfo["mime"];
    if (!isset($mimeToExt[$mime])) {
        error_log("Product image upload rejected: unsupported mime type " . $mime);
        return $fallback;
    }
    $ext = $mimeToExt[$mime];

    // Generate a collision-safe server-side filename; never trust client filename
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

function removeProduct($productID) {
    global $dbh;
    $cmd = "DELETE FROM products WHERE productID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$productID]);
}

// ORDERS
function updateOrderStatus($orderID, $fullfilled) {
    global $dbh;
    $cmd = "UPDATE orders SET fullfilled = ? WHERE orderID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$fullfilled, $orderID]);
}

// PROMO CODES
function insertPromoCode($promoCode, $discountType, $discountValue, $active, $expiryDate) {
    global $dbh;
    $cmd = "INSERT INTO promocodes (promoCode, discountType, discountValue, active, expiryDate) VALUES (?, ?, ?, ?, ?)";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$promoCode, $discountType, $discountValue, $active, $expiryDate]);
}

function updatePromoCode($promoID, $promoCode, $discountType, $discountValue, $active, $expiryDate) {
    global $dbh;
    $cmd = "UPDATE promocodes SET promoCode = ?,  discountType = ?, discountValue = ?, active = ?, expiryDate = ? WHERE promoID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$promoCode, $discountType, $discountValue, $active, $expiryDate, $promoID]);
}

function removePromoCode($promoID) {
    global $dbh;
    $cmd = "DELETE FROM promocodes WHERE promoID = ?";
    $stmt = $dbh->prepare($cmd);
    $stmt->execute([$promoID]);
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
    if ($promoID !== NULL && $promoID !== "") {
        updatePromoCode($promoID, $promoCode, $discountType, $discountValue, $active, $expiryDate);
    }
    else {
        insertPromoCode($promoCode, $discountType, $discountValue, $active, $expiryDate);
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
?>