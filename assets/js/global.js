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

    function doLogout(e) {
        if (e) e.preventDefault();
        var path = window.location.pathname;
        var isSubdir =
            path.includes("catering") ||
            path.includes("menu") ||
            path.includes("login") ||
            path.includes("signup") ||
            path.includes("pickup") ||
            path.includes("admin");
        var prefix = isSubdir ? "../" : "./";

        fetch(prefix + "assets/php/logout.php")
            .then(function (response) {
                return response.text();
            })
            .then(function () {
                window.location.href = prefix;
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
