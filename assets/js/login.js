/**
 * login.js
 *
 * Client-side script for the login page. Validates the email and
 * password fields as the user types (colouring them green/red),
 * wires the "Sign up" button to navigate to the signup page, and
 * submits the login form to assets/php/login.php. On success the
 * user is redirected to the home page.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: March 30, 2026
 */

window.addEventListener("load", function () {
    const emailinput = this.document.getElementById("emailinput")
    const passwordinput = this.document.getElementById("passwordinput")
    const errorElem = document.getElementById("errormessage");

    // REGISTER BUTTON
    let registerButton = this.document.getElementById("gotoregister");
    registerButton.addEventListener("click", function () {
        window.location.href = "../signup/";
    })

    // REGISTER 
    let loginButton = this.document.getElementById("submitbtn");
    loginButton.addEventListener("click", function () {
        if (!isValidEmail(emailinput.value)) {
            errorElem.innerHTML = "Please enter a valid email";
            errorElem.style.visibility = "visible";
            return
        }
        if (passwordinput.value == "") {
            errorElem.innerHTML = "Please enter a password";
            errorElem.style.visibility = "visible";
            return
        }

        // Register user
        let params = "email=" + emailinput.value + "&password=" + passwordinput.value
        let config = {
            method: 'POST',
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params
        };
        loginButton.disabled = true;

        fetch("../assets/php/login.php", config)
            .then(response => response.text())
            .then(doneLoggingInUser);
    });

    // EMAIL VALIDITIY
    emailinput.addEventListener("input", function () {
        if (isValidEmail(emailinput.value)) {
            errorElem.style.visibility = "hidden";
            emailinput.style.backgroundColor = "rgb(200, 255, 200)"
        }
        else if (emailinput.value === "") {
            emailinput.style.backgroundColor = "white"
        }
        else {
            emailinput.style.backgroundColor = "rgb(255, 200, 200)"
        }
    })

    //PASSWORD VALIDITY
    passwordinput.addEventListener("input", function () {

        if (passwordinput.value != "") {
            errorElem.style.visibility = "hidden";
            passwordinput.style.backgroundColor = "rgb(200, 255, 200)"
        }
        else if (passwordinput.value === "") {
            passwordinput.style.backgroundColor = "white"
            errorElem.style.visibility = "hidden";
        }
    })
})



/**
 * Performs a quick structural check on an email address: must be
 * non-empty, contain exactly one @, have at least one dot after the @,
 * and not end with a dot. Good enough for front-end validation before
 * hitting the server.
 *
 * @param {String} email the email address to validate
 * @returns {Boolean} true if the string looks like a plausible email address
 */
function isValidEmail(email) {
    if (email.length <= 0) return false;
    if (!email.includes(".")) return false;
    if ((email.split("@").length - 1) != 1) return false;
    if (!email.split("@")[1].includes(".")) return false;
    if (email.endsWith(".")) return false;

    return true;
}

/**
 * Callback that runs after the login POST completes. Re-enables the
 * submit button, shows the server's plain-text message if login failed,
 * and redirects to the home page if it succeeded.
 *
 * @param {String} code the plain-text response from login.php (e.g. "Logged in" or an error message)
 */
function doneLoggingInUser(code) {
    let loginButton = this.document.getElementById("submitbtn");
    loginButton.disabled = false;
    const errorElem = document.getElementById("errormessage");
    if (code != "Logged in") {
        errorElem.innerHTML = code;
        errorElem.style.visibility = "visible";
    }
    else {
        window.location.href = "../";
    }
}
