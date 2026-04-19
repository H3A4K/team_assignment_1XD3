/**
 * account.js
 *
 * Client-side script for the account page. Validates the email,
 * password, and phone inputs as the user types, and wires up the four
 * "Change ..." buttons so each one posts its specific credential
 * update to assets/php/changecredential.php. Reloads the page on a
 * successful update so the new value shows in the form.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: March 30, 2026
 */

window.addEventListener("load", function () {
    const emailinput = this.document.getElementById("emailinput")
    const passwordinput = this.document.getElementById("passinput")
    const phoneinput = this.document.getElementById("phoneinput")
    const addressinput = this.document.getElementById("addressinput")
    const errorElem = document.getElementById("errormessage");

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
        let code = isValidPassword(passwordinput.value)
        if (code == 100) {
            errorElem.style.visibility = "hidden";
            passwordinput.style.backgroundColor = "rgb(200, 255, 200)"
        }
        else if (passwordinput.value === "") {
            passwordinput.style.backgroundColor = "white"
            errorElem.style.visibility = "hidden";
        }
        else {
            errorElem.style.visibility = "visible";
            if (code == 1) {
                errorElem.innerHTML = "Password too short";
            }
            if (code == 2) {
                errorElem.innerHTML = "Password must contain a digit";
            }
            if (code == 3) {
                errorElem.innerHTML = "Password must contain a lowercase character";
            }
            if (code == 4) {
                errorElem.innerHTML = "Password must contain a uppercase character";
            }

            passwordinput.style.backgroundColor = "rgb(255, 200, 200)"
        }
    })

    // PHONE NUMBER VALIDITY
    phoneinput.addEventListener("input", function () {
        if (isValidPhone(phoneinput.value)) {
            errorElem.style.visibility = "hidden";
            phoneinput.style.backgroundColor = "rgb(200, 255, 200)"
        }
        else if (phoneinput.value === "") {
            phoneinput.style.backgroundColor = "white"
        }
        else {
            phoneinput.style.backgroundColor = "rgb(255, 200, 200)"
        }
    })

    // Change Email
    const emailbtn = this.document.getElementById("changeemail");
    const addressbtn = this.document.getElementById("changeaddress");
    const passbtn = this.document.getElementById("changepass");
    const phonebtn = this.document.getElementById("changephone");
    emailbtn.addEventListener("click", function () {
        if (!isValidEmail(emailinput.value)) {
            errorElem.innerHTML = "Invalid Email";
            errorElem.style.visibility = "visible";
            return
        }

        let params = "type=email&email=" + emailinput.value;
        let config = {
            method: 'POST',
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params
        };
        phonebtn.disabled = true;
        addressbtn.disabled = true;
        passbtn.disabled = true;
        emailbtn.disabled = true;

        fetch("../assets/php/changecredential.php", config)
            .then(response => response.text())
            .then(doneChange);

    })

    // Change Pass
    passbtn.addEventListener("click", function () {
        if (isValidPassword(passwordinput.value) != 100) {
            errorElem.innerHTML = "Invalid Password";
            errorElem.style.visibility = "visible";
            return;
        }

        let params = "type=password&password=" + passwordinput.value;
        let config = {
            method: 'POST',
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params
        };
        phonebtn.disabled = true;
        addressbtn.disabled = true;
        passbtn.disabled = true;
        emailbtn.disabled = true;

        fetch("../assets/php/changecredential.php", config)
            .then(response => response.text())
            .then(doneChange);

    })

    // Change Phone
    phonebtn.addEventListener("click", function () {
        if (!isValidPhone(phoneinput.value)) {
            errorElem.innerHTML = "Invalid Phone Number";
            errorElem.style.visibility = "visible";
            return
        }

        let params = "type=phone&phone=" + phoneinput.value;
        let config = {
            method: 'POST',
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params
        };
        phonebtn.disabled = true;
        addressbtn.disabled = true;
        passbtn.disabled = true;
        emailbtn.disabled = true;

        fetch("../assets/php/changecredential.php", config)
            .then(response => response.text())
            .then(doneChange);

    })

    // Change Address
    addressbtn.addEventListener("click", function () {
        let params = "type=address&address=" + addressinput.value;
        let config = {
            method: 'POST',
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params
        };
        phonebtn.disabled = true;
        addressbtn.disabled = true;
        passbtn.disabled = true;
        emailbtn.disabled = true;

        fetch("../assets/php/changecredential.php", config)
            .then(response => response.text())
            .then(doneChange);

    })
})

/**
 * Callback that runs after any credential-change POST completes.
 * Re-enables all four action buttons, shows the server's plain-text
 * message, and reloads the page 5 seconds later when the update
 * succeeded so the user sees their new value in the form.
 *
 * @param {String} text the plain-text response from changecredential.php (e.g. "Success" or an error)
 */
function doneChange(text) {
    const emailinput = this.document.getElementById("changeemail")
    const passwordinput = this.document.getElementById("changepass")
    const phoneinput = this.document.getElementById("changephone")
    const addressinput = this.document.getElementById("changeaddress")
    const errorElem = document.getElementById("errormessage");

    phoneinput.disabled = false;
    addressinput.disabled = false;
    passwordinput.disabled = false;
    emailinput.disalbed = false;

    errorElem.innerHTML = text;
    errorElem.style.visibility = "visible";
    setTimeout(() => {
        if (text == "Success") {
            window.location.reload();
        }
    }, 5000);

}


/**
 * Performs a quick structural check on an email address: must be
 * non-empty, contain exactly one @, have at least one dot after the @,
 * and not end with a dot.
 *
 * @param {String} email the email address to validate
 * @returns {Boolean} true if the string looks like a plausible email
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
 * Checks a password against four rules and returns a status code so the
 * UI can show a specific error message. Rules: at least 6 characters,
 * at least one digit, at least one lowercase letter, at least one
 * uppercase letter.
 *
 * @param {String} pwd the password to check
 * @returns {Number} 100 if all rules pass, 1 if too short, 2 if missing a digit, 3 if missing a lowercase letter, 4 if missing an uppercase letter
 */
function isValidPassword(pwd) {
    if (pwd.length < 6) return 1;
    if (/\d/.test(pwd) == false) return 2;
    if (pwd.toUpperCase() == pwd) return 3;
    if (pwd.toLowerCase() == pwd) return 4;

    return 100;
}

/**
 * Checks that a phone number matches a common North-American format.
 * Accepts optional country code, parentheses around the area code, and
 * dashes or spaces as separators.
 *
 * @param {String} phone the phone number string to test
 * @returns {Boolean} true if the string matches the accepted pattern
 */
function isValidPhone(phone) {
    const pattern = /^(1\s|1)?(\(\d{3}\)|\d{3})(-|\s)?\d{3}(-|\s)?\d{4}$/;
    return pattern.test(phone);
}
