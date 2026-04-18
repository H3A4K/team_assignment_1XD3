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
