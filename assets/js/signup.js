window.addEventListener("load", function () {
    const emailinput = this.document.getElementById("emailinput")
    const passwordinput = this.document.getElementById("passwordinput")
    const phoneinput = this.document.getElementById("phoneinput")
    const addressinput = this.document.getElementById("addressinput")
    const errorElem = document.getElementById("errormessage");

    // LOGIN BUTTON
    let loginButton = this.document.getElementById("gotologin");
    loginButton.addEventListener("click", function () {
        window.location.href = "../login/";
    })

    // REGISTER 
    let createButton = this.document.getElementById("submitbtn");
    createButton.addEventListener("click", function () {
        if (!isValidEmail(emailinput.value)) {
            errorElem.innerHTML = "Invalid Email";
            errorElem.style.visibility = "visible";
            return
        }
        if (!isValidPhone(phoneinput.value)) {
            errorElem.innerHTML = "Invalid Phone Number";
            errorElem.style.visibility = "visible";
            return
        }
        if (isValidPassword(passwordinput.value) != 100) {
            errorElem.innerHTML = "Invalid Password";
            errorElem.style.visibility = "visible";
            return;
        }

        // Register user
        let params = "email=" + emailinput.value + "&password=" + passwordinput.value + "&phone=" + phoneinput.value + "&address=" + addressinput.value
        let config = {
            method: 'POST',
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: params
        };
        createButton.disabled = true;

        fetch("../assets/php/signup.php", config)
            .then(response => response.text())
            .then(doneRegisteringUser);
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
        let pwd = passwordinput.value;
        
        // Update requirements list
        document.getElementById("req-length").classList.toggle("met", pwd.length >= 6);
        document.getElementById("req-digit").classList.toggle("met", /\d/.test(pwd));
        document.getElementById("req-lower").classList.toggle("met", pwd !== "" && pwd.toUpperCase() !== pwd);
        document.getElementById("req-upper").classList.toggle("met", pwd !== "" && pwd.toLowerCase() !== pwd);

        let code = isValidPassword(pwd)
        if (code == 100) {
            errorElem.style.visibility = "hidden";
            passwordinput.style.backgroundColor = "rgb(200, 255, 200)"
        }
        else if (pwd === "") {
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
})



function isValidEmail(email) {
    if (email.length <= 0) return false;
    if (!email.includes(".")) return false;
    if ((email.split("@").length - 1) != 1) return false;
    if (!email.split("@")[1].includes(".")) return false;
    if (email.endsWith(".")) return false;

    return true;
}

function isValidPassword(pwd) {
    if (pwd.length < 6) return 1;
    if (/\d/.test(pwd) == false) return 2;
    if (pwd.toUpperCase() == pwd) return 3;
    if (pwd.toLowerCase() == pwd) return 4;

    return 100;
}

function isValidPhone(phone) {
    const pattern = /^(1\s|1)?(\(\d{3}\)|\d{3})(-|\s)?\d{3}(-|\s)?\d{4}$/;
    return pattern.test(phone);
}

function doneRegisteringUser(code) {
    console.log(code);
    let createButton = this.document.getElementById("submitbtn");
    createButton.disabled = false;
    const errorElem = document.getElementById("errormessage");
    if (code != "Your account was created successfully") {
        errorElem.innerHTML = code;
        errorElem.style.visibility = "visible";
    }
    else {
        // Redirect to login page
        window.location.href = "../login/";
    }
}
