/**
 * Date Created  : 01 04 26
 * Date Modified : 01 04 26
 * 
 * 
 */

window.addEventListener("load", function() {

    function success(json) {
        if (!json) {
            const main = document.querySelector("main");
            main.innerHTML = "";
            return;
        }
        const display = document.getElementById("time");
        // JSON.parse(json);
        display.innerHTML = json;
    }

    fetch("../assets/php/pickup.php")
        .then(response => response.text())
        .then(success);
});