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
            <a class="nav-cta" href="../menu/">Order Now</a>
            <?php if (isset($_SESSION["email"])) { ?>
                <span class="login-status">Logged in as <?php echo htmlspecialchars($_SESSION["email"]); ?></span>
            <?php } ?>
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
        </div>
    </header>

    <nav class="main-nav" aria-label="Main navigation">
        <ul>
            <li><a class="current" href="../">Home</a></li>
            <li><a href="../menu/">Menu</a></li>
            <li><a href="../catering/">Catering</a></li>
        </ul>
    </nav>

    <main>
        <h1>Estimated Wait Time: <span id="time"></span> minutes</h1>
    </main>
</body>
</html>
