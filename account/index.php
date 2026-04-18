<?php session_start(); 
include "../assets/php/connect.php";
if (!isset($_SESSION["email"])) {
    header("Location: ../");
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account | Clarence's Kitchen</title>
    <link rel="stylesheet" href="../assets/css/global.css" />
    <link rel="stylesheet" href="../assets/css/account.css" />
    <script src="../assets/js/account.js" defer></script>
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
                <span class="login-status">Logged in as <?php echo htmlspecialchars($_SESSION["email"]); ?></span>
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
            <li><a href="../catering/">Catering</a></li>
            <?php if (!isset($_SESSION["email"])) { ?>
                <li class="nav-mobile-only"><a href="../login/">Login</a></li>
                <li class="nav-mobile-only"><a href="../signup/">Sign Up</a></li>
            <?php } else { ?>
                <li class="nav-mobile-only nav-mobile-status"><span>Logged in as <?php echo htmlspecialchars($_SESSION["email"]); ?></span></li>
                <li class="nav-mobile-only"><a href="#" id="mobile-logout">Logout</a></li>
            <?php } ?>
        </ul>
    </nav>

    <div id="content">
        <div id ="adminandlogo">
            <img src="../assets/images/logo.png" id="logo" />
            <?php
            // Check if they're an admin
            if (!isset($_SESSION["email"])) {
                return;
            }

            $email = $_SESSION["email"];
            $cmd = "SELECT admin FROM users where email=?";
            $stmt = $dbh->prepare($cmd);
            $success = $stmt->execute([$email]);
            if (!$success) {
                return;
            }
            $row = $stmt->fetch();
            if ($row["admin"] == 1) {
            ?>
            <a class="button" href="../admin" id="adminpanel">Admin Panel</a>
            <?php
            }
            ?>
        </div>
        <div id="changeForms">
            <div class="inputcontainer">
                <label for="emailinput">Email</label>
                <input type="email" id="emailinput" placeholder="email@example.com" />
                <button id="changeemail" class="button">Change Email</button>
            </div>

            <div class="inputcontainer">
                <label for="passinput">Password</label>
                <input type="password" id="passinput" placeholder="Password123!" />
                <button id="changepass" class="button">Change Password</button>
            </div>

            <div class="inputcontainer">
                <label for="phoneinput">Phone Number</label>
                <input type="text" id="phoneinput" placeholder="123-456-7890" />
                <button id="changephone" class="button">Change Phone Number</button>
            </div>

            <div class="inputcontainer">
                <label for="addressinput">Address</label>
                <input type="text" id="addressinput" placeholder="15 Example Drive" />
                <button id="changeaddress" class="button">Change Address</button>
            </div>
            <p id="errormessage">Error</p>
        </div>


    </div>


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