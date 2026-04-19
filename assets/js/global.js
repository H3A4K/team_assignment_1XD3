/**
 * global.js
 *
 * Site-wide client-side script included on every page. Handles the
 * mobile hamburger menu (open/close, aria state, outside-click close)
 * and wires up the logout buttons in the header and mobile navigation.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 01, 2026
 */

document.addEventListener("DOMContentLoaded", function () {
    var hamburger = document.getElementById("hamburger-toggle");
    var nav = document.getElementById("main-nav");

    if (hamburger && nav) {
        hamburger.addEventListener("click", function () {
            hamburger.classList.toggle("is-active");
            nav.classList.toggle("nav-open");
            hamburger.setAttribute(
                "aria-expanded",
                hamburger.classList.contains("is-active").toString()
            );
        });

        var navLinks = nav.querySelectorAll("a");
        for (var i = 0; i < navLinks.length; i++) {
            navLinks[i].addEventListener("click", function () {
                hamburger.classList.remove("is-active");
                nav.classList.remove("nav-open");
                hamburger.setAttribute("aria-expanded", "false");
            });
        }

        document.addEventListener("click", function (e) {
            if (!nav.contains(e.target) && !hamburger.contains(e.target)) {
                hamburger.classList.remove("is-active");
                nav.classList.remove("nav-open");
                hamburger.setAttribute("aria-expanded", "false");
            }
        });
    }

    /**
     * Logs the current user out by calling the logout endpoint, then
     * redirects to the home page. Handles both the desktop logout
     * button and the mobile-menu logout link. Works from any depth in
     * the folder tree by detecting which subdirectory we're in.
     *
     * @param {Event} e optional click event; preventDefault is called on it so anchor navigation doesn't fire before the fetch
     */
    function doLogout(e) {
        if (e) e.preventDefault();
        var path = window.location.pathname;
        var isSubdir =
            path.includes("catering") ||
            path.includes("menu") ||
            path.includes("login") ||
            path.includes("signup") ||
            path.includes("pickup") ||
            path.includes("admin") ||
            path.includes("account");
        var prefix = isSubdir ? "../" : "./";

        fetch(prefix + "assets/php/logout.php")
            .then(function (response) {
                return response.text();
            })
            .then(function () {
                if (!path.includes("account")) {
                    window.location.href = prefix;
                } else {
                    window.location.href = "../";
                }
            });
    }

    var logoutButton = document.getElementById("logoutbtn");
    if (logoutButton) {
        logoutButton.addEventListener("click", doLogout);
    }

    var mobileLogout = document.getElementById("mobile-logout");
    if (mobileLogout) {
        mobileLogout.addEventListener("click", doLogout);
    }
});
