<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clarence's Kitchen</title>
    <link rel="stylesheet" href="./assets/css/global.css">
    <link rel="stylesheet" href="./assets/css/home.css">
    <script src="./assets/js/global.js"></script>
</head>

<body>
    <header class="site-header">
        <div class="site-title">Clarence's Kitchen</div>
        <div class="header-actions">
            <a class="nav-cta" href="/team_assignment_1XD3/menu/">Order Now</a>
            <?php
            if (!isset($_SESSION["email"])) {
            ?>
                <div class="account-menu">
                    <a class="nav-cta account-trigger" href="/team_assignment_1XD3/login/" aria-label="Account menu">
                        <img src="./assets/images/user.png" alt="">
                        <span>Login/Signup</span>
                    </a>
                    <div class="account-dropdown">
                        <a href="/team_assignment_1XD3/login/">Log In</a>
                        <a href="/team_assignment_1XD3/signup/">Sign Up</a>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <span class="login-status">Logged in as <?php echo htmlspecialchars($_SESSION["email"]); ?></span>
                <div class="account-menu" id="logoutbtn">
                    <a class="nav-cta account-trigger" href ="./signup" aria-label="Account menu">
                        <img src="./assets/images/user.png" alt="">
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
            <li><a class="current" href="/team_assignment_1XD3/">Home</a></li>
            <li><a href="/team_assignment_1XD3/menu/">Menu</a></li>
            <li><a href="/team_assignment_1XD3/catering/">Catering</a></li>
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
                    <a class="primary-button" href="/team_assignment_1XD3/menu/">Browse Menu</a>
                    <a class="secondary-button" href="/team_assignment_1XD3/catering/">Plan Catering</a>
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

    <a href="/team_assignment_1XD3/pickup/">Pickup</a>
</body>

</html>
