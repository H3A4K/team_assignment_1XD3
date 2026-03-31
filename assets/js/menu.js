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

window.addEventListener("load", function() {

    const menu = document.getElementById("menu");

    function handleMenu(data) {

        menu.innerHTML = "";

        for (const product of data) {
            const item = document.createElement("div");
            item.className = "menuitem";

            const img = document.createElement("img");
            img.src = "../assets/images/menu/" + product.productImg;

            const br = document.createElement("br");

            const desc = document.createElement("div");
            desc.className = "desc";

            const name = document.createElement("h2");
            name.textContent = product.productName;

            const price = document.createElement("h3");
            price.textContent = "$" + product.price;

            const itemdesc = document.createElement("p");
            itemdesc.textContent = product.productDesc;

            desc.append(name, price, itemdesc);
            item.append(img, br, desc);
            menu.appendChild(item);
        }

    }

    fetch("../assets/php/menu.php")
        .then(function (response) {
            if (!response.ok) {
                throw new Error("Server returned " + response.status);
            }
            return response.json()
        })
        .then(handleMenu);

})
