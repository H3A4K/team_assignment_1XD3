/**
 * Date Created  : 01 04 26
 * Date Modified : 01 04 26
 * 
 * 
 */

window.addEventListener("load", function() {
    const display = document.querySelector("main");

    function success(text) {
        display.innerHTML = text;
    }

    fetch("../assets/php/pickup.php")
        .then(response => response.text())
        .then(success);
});