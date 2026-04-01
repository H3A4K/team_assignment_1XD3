<?php session_start(); ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup</title>
  <link rel="stylesheet" href="../assets/css/global.css" />
  <link rel="stylesheet" href="../assets/css/login.css" />
  <script src="../assets/js/login.js"></script>
  <script src="../assets/js/global.js"></script>
</head>

<body>
  <header class="site-header">
    <div class="site-title">Clarence's Kitchen</div>
    <div class="header-actions">
      <a class="nav-cta" href="../catering/index.html">Book Catering</a>
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
      <li><a href="../catering">Catering</a></li>
      <li><a href="../shop">Shop</a></li>
    </ul>
  </nav>
  <div id="content">
    <img src="../assets/images/logo.png" id="logo" />
    <div id="form">
      <div class="inputcontainer">
        <label for="emailinput">Email</label>
        <input
          type="email"
          id="emailinput"
          placeholder="email@example.com" />
      </div>
      <div class="inputcontainer">
        <label for="passwordinput">Password</label>
        <input type="password" id="passwordinput" />
      </div>
      <p id="errormessage">Error</p>
      <div id="btns">
        <button id="submitbtn" class="button">Login</button>
        <button id="gotoregister" class="button">Don't have an account?<br>
          Click to register</button>
      </div>
    </div>
  </div>
</body>

</html>