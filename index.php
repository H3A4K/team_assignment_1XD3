<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clarence's Kitchen</title>
    <link rel="stylesheet" href="./assets/css/global.css">
    <link rel="stylesheet" href="./assets/css/home.css">
    <script src="./assets/js/global.js" defer></script>
</head>

<body>
    <header class="site-header">
        <a href="./" class="site-title"><img src="./assets/images/logo.png" alt="" class="site-logo">Clarence's Kitchen</a>
        <button class="hamburger" id="hamburger-toggle" aria-label="Toggle navigation" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <div class="header-actions">
            <a class="nav-cta" href="./menu/">Order Now</a>
            <?php if (!isset($_SESSION["email"])) { ?>
                <div class="account-menu">
                    <a class="nav-cta account-trigger" href="./login/" aria-label="Account menu">
                        <img src="./assets/images/user.png" alt="">
                        <span>Login/Signup</span>
                    </a>
                    <div class="account-dropdown">
                        <a href="./login/">Log In</a>
                        <a href="./signup/">Sign Up</a>
                    </div>
                </div>
            <?php } else { ?>
                <a class="login-status" href="./account">Account</a>
                <div class="account-menu" id="logoutbtn">
                    <a class="nav-cta account-trigger" aria-label="Account menu">
                        <img src="./assets/images/user.png" alt="">
                        <span>Logout</span>
                    </a>
                </div>
            <?php } ?>
        </div>
    </header>

    <nav class="main-nav" id="main-nav" aria-label="Main navigation">
        <ul>
            <li><a class="current" href="./">Home</a></li>
            <li><a href="./menu/">Menu</a></li>
            <li><a href="./catering/">Catering</a></li>
            <?php if (!isset($_SESSION["email"])) { ?>
                <li class="nav-mobile-only"><a href="./login/">Login</a></li>
                <li class="nav-mobile-only"><a href="./signup/">Sign Up</a></li>
            <?php } else { ?>
                <li class="nav-mobile-only nav-mobile-status"><a href="./account">Account</a></li>
                <li class="nav-mobile-only"><a href="#" id="mobile-logout">Logout</a></li>
            <?php } ?>
        </ul>
    </nav>

    <main>
        <section class="hero">
            <div class="hero-copy">
                <span class="eyebrow">Clarence's Kitchen</span>
                <h1>Bold flavour, warm service, and comfort food that shows up.</h1>
                <p>
                    Clarence's Kitchen is your neighbourhood stop for satisfying plates,
                    quick pickup, and catering built for family events, office lunches,
                    and community gatherings.
                </p>
                <div class="hero-actions">
                    <a class="primary-button" href="./menu/">Browse Menu</a>
                    <a class="secondary-button" href="./catering/">Plan Catering</a>
                </div>
            </div>

            <div class="hero-brand">
                <img src="./assets/images/logo.png" alt="Clarence's Kitchen logo">
            </div>
        </section>

        <section class="feature-grid">
            <article class="feature-card">
                <h2>Fresh Daily</h2>
                <p>
                    Rotating comfort-food favourites, quick lunch picks, and portions designed
                    to feel generous without overcomplicating the experience.
                </p>
            </article>
            <article class="feature-card">
                <h2>Catering Ready</h2>
                <p>
                    From trays for office events to family-style meal packages, the catering page
                    gives customers a direct route to plan larger orders.
                </p>
            </article>
            <article class="feature-card">
                <h2>Easy Ordering</h2>
                <p>
                    The site now has a clear entry point for the menu, shop, and catering pages
                    so visitors can move through the experience without dead ends.
                </p>
            </article>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <img src="./assets/images/logo.png" alt="Clarence's Kitchen" class="footer-logo">
                <span class="footer-title">Clarence's Kitchen</span>
                <p>Bold flavour, warm service, and comfort food that shows up.</p>
                <div class="footer-socials">
                    <a href="https://www.instagram.com/clarenceskitchen/" target="_blank" rel="noopener" aria-label="Instagram"><img src="./assets/images/instagram.png" alt="Instagram"></a>
                    <a href="https://www.facebook.com/ClarencesKitchen/" target="_blank" rel="noopener" aria-label="Facebook"><img src="./assets/images/facebook.png" alt="Facebook"></a>
                </div>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="./">Home</a></li>
                    <li><a href="./menu/">Menu</a></li>
                    <li><a href="./catering/">Catering</a></li>
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
