<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catering | CK's</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/catering.css">
    <script src="../assets/js/global.js"></script>
</head>

<body>
    <header class="site-header">
        <div class="site-title">Clarence’s Kitchen</div>
        <div class="header-actions">
            <a class="nav-cta" href="../menu">Order Now</a>
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
            <li><a class="current" href="./">Catering</a></li>
        </ul>
    </nav>

    <main>
        <section class="hero">
            <p class="eyebrow">Catering Wireframe</p>
            <h1>Let us cater your next event!</h1>
            <p class="hero-copy">
                This placeholder layout gives the catering page a dedicated landing area,
                a short booking summary, and clear restaurant contact information.
            </p>
        </section>

        <section class="content-grid">
            <article class="panel">
                <h2>CK Catering Made simple and delicious</h2>
                <p>
                    Bring the taste of Clarence’s Kitchen to your next event! Whether it’s a birthday, office party, family gathering, or any special occasion, our catering menu offers a variety of crowd-pleasing options. We take care of the cooking, so you can focus on enjoying the moment. Our dishes are made fresh, packed with flavour, and served in portions perfect for sharing. Customizable options and dietary accommodations are available to ensure every guest leaves satisfied. Let us make your event unforgettable with delicious, fresh, and convenient catering that everyone will love.
                </p>
                <div class="placeholder-box">Package options / event details / order lead times</div>
            </article>

            <aside class="panel contact-panel" id="contact-info">
                <h2>Contact The Restaurant</h2>
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
                        <a href="#0" class="social-icon" aria-label="Instagram">
                            <img src="../assets/images/instagram.png" alt="Instagram">
                        </a>
                        <a href="#0" class="social-icon" aria-label="Facebook">
                            <img src="../assets/images/facebook.png" alt="Facebook">
                        </a>
                    </div>
                </div>
            </aside>
        </section>
    </main>
</body>

</html>