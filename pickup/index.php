<?php
    // include "..\assets\php\pickup.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickup | Clarence's Kitchen</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/home.css">

    <script src="../assets/js/pickup.js"></script>
</head>
<body>
    <header class="site-header">
        <div class="site-title">Clarence's Kitchen</div>
        <div class="header-actions">
            <a class="nav-cta" href="../catering/index.php">Book Catering</a>
            <div class="account-menu">
                <a class="nav-cta account-trigger" href="../login/index.php" aria-label="Account menu">
                    <img src="../assets/images/user.png" alt="">
                    <span>Login/Signup</span>
                </a>
                <div class="account-dropdown">
                    <a href="../login/index.php">Log In</a>
                    <a href="../signup/index.php">Sign Up</a>
                </div>
            </div>
        </div>
    </header>

    <nav class="main-nav" aria-label="Main navigation">
        <ul>
            <li><a class="current" href="../index.php">Home</a></li>
            <li><a href="../menu/index.php">Menu</a></li>
            <li><a href="../catering/index.php">Catering</a></li>
            <li><a href="../shop/index.php">Shop</a></li>
        </ul>
    </nav>

    <main>
        <?= $display ?>
    </main>
</body>
</html>
