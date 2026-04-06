window.addEventListener("load", function () {
    let logoutButton = document.getElementById("logoutbtn");
    if (!logoutButton) return;
    logoutButton.addEventListener("click", function () {
        if (window.location.pathname.includes("catering") || window.location.pathname.includes("menu") || window.location.pathname.includes("login") || window.location.pathname.includes("signup")) {
            fetch("../assets/php/logout.php").then(response => response.text()).then(loggedOut);
        }   
        else {
            fetch("./assets/php/logout.php").then(response => response.text()).then(loggedOut);
        }
    });
})

function loggedOut(text) {
    window.location.href = "./";
    window.location.reload();
}
