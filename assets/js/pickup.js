/**
 * Date Created  : 01 04 26
 * Date Modified : 01 04 26
 * 
 * 
 */

window.addEventListener("load", function() {
    const display = document.getElementById("time");

    function success(json) {
        // JSON.parse(json);
        display.innerHTML = json;
    }

    fetch("../assets/php/pickup.php")
        .then(response => response.json())
        .then(success);
});