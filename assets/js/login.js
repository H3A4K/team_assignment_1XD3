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



function isValidEmail(email) {
    if (email.length <= 0) return false;
    if (!email.includes(".")) return false;
    if ((email.split("@").length - 1) != 1) return false;
    if (!email.split("@")[1].includes(".")) return false;
    if (email.endsWith(".")) return false;

    return true;
}

function doneLoggingInUser(code) {
    console.log(code);
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
