<?php session_start(); ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup</title>
  <link rel="stylesheet" href="../assets/css/global.css" />
  <link rel="stylesheet" href="../assets/css/signup.css" />
  <script src="../assets/js/signup.js"></script>
  <script src="../assets/js/global.js"></script>
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
            <img src="../assets/images/user.png" alt="">
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
      <li><a href="/team_assignment_1XD3/">Home</a></li>
      <li><a href="/team_assignment_1XD3/menu/">Menu</a></li>
      <li><a href="/team_assignment_1XD3/catering/">Catering</a></li>
    </ul>
  </nav>
  <div id="content">
    <img src="../assets/images/logo.png" id="logo" />
    <div id="form">
      <!-- <div id="topinputs"> -->
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
      <!-- </div> -->
      <!-- <div id="bottominputs"> -->
      <div class="inputcontainer">
        <label for="phoneinput">Phone Number</label>
        <input type="tel" id="phoneinput" placeholder="123-456-7890" />
      </div>
      <div class="inputcontainer">
        <label for="addressinput">Address</label>
        <input
          type="text"
          id="addressinput"
          placeholder="15 Example Drive" />
      </div>
      <!-- </div> -->
      <p id="errormessage">Error</p>
      <div id="btns">
        <button id="submitbtn" class="button">Create Account</button>
        <h3>Already have an account?</h3>
        <button id="gotologin" class="button">Log in</button>
      </div>
    </div>
  </div>
</body>

</html>
