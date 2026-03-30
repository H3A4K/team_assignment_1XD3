const cart = document.getElementById("cd-cart");
const closeIcon = document.getElementById("cd-close-icon");
const shoppingCartIcon = document.getElementById("cd-shoppingcart-icon");

if (cart && closeIcon && shoppingCartIcon) {
    closeIcon.addEventListener("click", () => {
        cart.classList.add("cart-collapsed");
    });

    shoppingCartIcon.addEventListener("click", () => {
        cart.classList.remove("cart-collapsed");
    });
}
