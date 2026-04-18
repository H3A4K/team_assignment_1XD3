<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catering | Clarence's Kitchen</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/catering.css">
    <script src="../assets/js/global.js" defer></script>
</head>

<body>
    <header class="site-header">
        <a href="../" class="site-title"><img src="../assets/images/logo.png" alt="" class="site-logo">Clarence's Kitchen</a>
        <button class="hamburger" id="hamburger-toggle" aria-label="Toggle navigation" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <div class="header-actions">
            <a class="nav-cta" href="../menu/">Order Now</a>
            <?php if (!isset($_SESSION["email"])) { ?>
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
            <?php } else { ?>
                <a href="../account/" class="login-status">Account</a>
                <div class="account-menu" id="logoutbtn">
                    <a class="nav-cta account-trigger" aria-label="Account menu">
                        <img src="../assets/images/user.png" alt="">
                        <span>Logout</span>
                    </a>
                </div>
            <?php } ?>
        </div>
    </header>

    <nav class="main-nav" id="main-nav" aria-label="Main navigation">
        <ul>
            <li><a href="../">Home</a></li>
            <li><a href="../menu/">Menu</a></li>
            <li><a class="current" href="../catering/">Catering</a></li>
            <?php if (!isset($_SESSION["email"])) { ?>
                <li class="nav-mobile-only"><a href="../login/">Login</a></li>
                <li class="nav-mobile-only"><a href="../signup/">Sign Up</a></li>
            <?php } else { ?>
                <li class="nav-mobile-only nav-mobile-status"><a href="../account/">Account</a></li>
                <li class="nav-mobile-only"><a href="#" id="mobile-logout">Logout</a></li>
            <?php } ?>
        </ul>
    </nav>

    <main>
        <section class="hero">
            <p class="eyebrow">Catering</p>
            <h1>Let us cater your next event!</h1>
            <p class="hero-copy">
                From family dinners to large corporate events, we handle the food
                so you can focus on your guests. Flexible menus, generous portions, and
                the same bold flavours you love from our kitchen, delivered on time.
            </p>
        </section>

        <section class="content-grid">
            <article class="panel">
                <h2>CK Catering Made simple and delicious</h2>
                <p>
                    Bring the taste of Clarence's Kitchen to your next event! Whether it's a birthday, office party, family gathering, or any special occasion, our catering menu offers a variety of crowd-pleasing options. We take care of the cooking, so you can focus on enjoying the moment. Our dishes are made fresh, packed with flavour, and served in portions perfect for sharing. Customizable options and dietary accommodations are available to ensure every guest leaves satisfied. Let us make your event unforgettable with delicious, fresh, and convenient catering that everyone will love.
                </p>
                <div class="catering-details">
                    <div class="catering-section">
                        <h3>Packages</h3>
                        <ul>
                            <li><strong>Small Gathering</strong> &mdash; 10 to 20 guests</li>
                            <li><strong>Medium Event</strong> &mdash; 20 to 50 guests</li>
                            <li><strong>Large Celebration</strong> &mdash; 50+ guests, custom menu available</li>
                        </ul>
                    </div>
                    <div class="catering-section">
                        <h3>What's Included</h3>
                        <ul>
                            <li>Choice of mains from our CK Favourites menu</li>
                            <li>Sides, salads, and assorted beverages</li>
                            <li>Disposable serving ware and utensils</li>
                            <li>Dietary accommodations available on request</li>
                        </ul>
                    </div>
                    <div class="catering-section">
                        <h3>Order Lead Times</h3>
                        <ul>
                            <li>Small orders: at least 48 hours notice</li>
                            <li>Medium events: at least 3 days notice</li>
                            <li>Large events: 5+ days notice</li>
                        </ul>
                    </div>
                </div>
            </article>

            <aside class="panel contact-panel" id="contact-info">
                <h2>Contact Us</h2>
                <ul class="contact-list">
                    <li><strong>Phone:</strong> (647)-438-8589</li>
                    <li><strong>Email:</strong> clarenceskitchen@gmail.com</li>
                    <li><strong>Address:</strong> 8 Glen Watford Drive, Scarborough, ON</li>
                    <li><strong>Hours:</strong> Mon-Sat, 11:00 AM to 9:00 PM</li>
                </ul>
                <p class="contact-note">
                    For large orders, corporate lunches, or private events, contact the restaurant
                    directly and include your event date, guest count, and pickup or delivery needs.
                </p>

                <div class="socials-section">
                    <h3>Socials</h3>
                    <div class="social-links" aria-label="Restaurant social media">
                        <a href="https://www.instagram.com/clarenceskitchen/" target="_blank" rel="noopener" class="social-icon" aria-label="Instagram">
                            <img src="../assets/images/instagram.png" alt="Instagram">
                        </a>
                        <a href="https://www.facebook.com/ClarencesKitchen/" target="_blank" rel="noopener" class="social-icon" aria-label="Facebook">
                            <img src="../assets/images/facebook.png" alt="Facebook">
                        </a>
                    </div>
                </div>
            </aside>
        </section>
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
