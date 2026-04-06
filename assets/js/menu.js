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

document.addEventListener("DOMContentLoaded", function () {
    const menu = document.getElementById("menu");
    const searchInput = document.getElementById("menu-search");
    const sortSelect = document.getElementById("menu-sort");
    const classFilterBtn = document.getElementById("class-filter-btn");
    const classFilterDropdown = document.getElementById("class-filter-dropdown");
    const cartItems = document.querySelector(".cd-cart-items");
    const cartStatus = document.getElementById("cd-cart-status");
    const cartSubtotal = document.getElementById("cd-cart-subtotal");
    const cartTotal = document.getElementById("cd-cart-total");
    const checkoutBtn = document.getElementById("cd-cart-checkout");
    const checkoutForm = document.getElementById("cd-cart-checkout-form");
    const cartFeedback = document.getElementById("cd-cart-feedback");

    if (!menu || !searchInput || !sortSelect || !classFilterBtn || !classFilterDropdown || !cartItems || !cartStatus || !cartSubtotal || !cartTotal || !checkoutBtn || !checkoutForm || !cartFeedback) {
        console.error("Menu page is missing expected cart or filter elements.");
        return;
    }

    let allProducts = [];
    let selectedClasses = new Set();

    function formatCurrency(value) {
        return "$" + Number(value).toFixed(2);
    }

    function setCartStatus(message) {
        cartStatus.textContent = message;
        cartStatus.hidden = false;
    }

    function setCartFeedback(message, isError = false) {
        if (!message) {
            cartFeedback.hidden = true;
            cartFeedback.textContent = "";
            cartFeedback.classList.remove("is-error");
            return;
        }

        cartFeedback.hidden = false;
        cartFeedback.textContent = message;
        cartFeedback.classList.toggle("is-error", isError);
    }

    function renderCart(cartData) {
        cartItems.innerHTML = "";
        cartSubtotal.textContent = formatCurrency(cartData.subtotal || 0);
        cartTotal.textContent = formatCurrency(cartData.total || 0);

        if (!cartData.loggedIn) {
            setCartStatus("Log in to view and complete your order.");
            checkoutBtn.disabled = true;
            return;
        }

        if (!cartData.hasOpenOrder || cartData.items.length === 0) {
            setCartStatus("Your cart is empty.");
            checkoutBtn.disabled = true;
            return;
        }

        cartStatus.hidden = true;
        checkoutBtn.disabled = false;

        for (const item of cartData.items) {
            const li = document.createElement("li");

            const itemInfo = document.createElement("div");
            const qty = document.createElement("span");
            qty.className = "cd-qty";
            qty.textContent = item.quantity + "x";
            itemInfo.append(qty, " " + item.productName);

            const price = document.createElement("div");
            price.className = "cd-price";
            price.textContent = formatCurrency(item.lineTotal);

            li.append(itemInfo, price);
            cartItems.appendChild(li);
        }
    }

    async function refreshCart() {
        try {
            const response = await fetch("../assets/php/get_cart.php");
            const responseText = await response.text();
            const cartData = JSON.parse(responseText);

            if (!response.ok) {
                throw new Error(cartData.error || "Failed to load cart");
            }

            renderCart(cartData);
        } catch (error) {
            console.error(error);
            setCartStatus("Unable to load cart right now.");
            setCartFeedback("Cart request failed. Check the browser console for details.", true);
            cartSubtotal.textContent = formatCurrency(0);
            cartTotal.textContent = formatCurrency(0);
            checkoutBtn.disabled = true;
        }
    }

    function renderMenu(data) {
        menu.innerHTML = "";

        if (data.length === 0) {
            const empty = document.createElement("p");
            empty.id = "menu-empty";
            empty.textContent = "No items found.";
            menu.appendChild(empty);
            return;
        }

        for (const product of data) {
            const item = document.createElement("div");
            item.className = "menuitem";

            const img = document.createElement("img");
            img.src = "../assets/images/menu/" + product.productImg;

            const br = document.createElement("br");

            const desc = document.createElement("div");
            desc.className = "desc";

            const classTag = document.createElement("span");
            classTag.className = "menu-class-tag";
            classTag.textContent = product.productClass;

            const name = document.createElement("h2");
            name.textContent = product.productName;

            const price = document.createElement("h3");
            price.textContent = formatCurrency(product.price);

            const itemdesc = document.createElement("p");
            itemdesc.textContent = product.productDesc;

            const addBtn = document.createElement("button");
            addBtn.textContent = "Add to cart";

            addBtn.addEventListener("click", async function () {
                try {
                    const response = await fetch("../assets/php/add_to_order.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            productID: product.productID,
                            quantity: 1
                        })
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        throw new Error(result.error || "Failed to add item to order");
                    }

                    setCartFeedback(product.productName + " added to cart.");
                    await refreshCart();
                } catch (error) {
                    console.error(error);
                    setCartFeedback(error.message, true);
                    alert(error.message);
                }
            });

            desc.append(classTag, name, price, itemdesc);
            item.append(img, br, desc, addBtn);
            menu.appendChild(item);
        }
    }

    function buildClassCheckboxes(data) {
        classFilterDropdown.innerHTML = "";
        const classes = [...new Set(data.map((p) => p.productClass))].sort();
        classes.forEach((cls) => {
            const label = document.createElement("label");
            const cb = document.createElement("input");
            cb.type = "checkbox";
            cb.value = cls;
            cb.checked = false;
            cb.addEventListener("change", function () {
                if (this.checked) {
                    selectedClasses.add(cls);
                } else {
                    selectedClasses.delete(cls);
                }
                applyFiltersAndSort();
            });
            label.append(cb, " " + cls);
            classFilterDropdown.appendChild(label);
        });
    }

    function applyFiltersAndSort() {
        const query = searchInput.value.trim().toLowerCase();
        const sort = sortSelect.value;

        const filtered = allProducts.filter((p) =>
            (selectedClasses.size === 0 || selectedClasses.has(p.productClass)) &&
            (
                p.productName.toLowerCase().includes(query) ||
                p.productDesc.toLowerCase().includes(query) ||
                p.productClass.toLowerCase().includes(query)
            )
        );

        if (sort === "price-asc") {
            filtered.sort((a, b) => a.price - b.price);
        } else if (sort === "price-desc") {
            filtered.sort((a, b) => b.price - a.price);
        } else if (sort === "alpha") {
            filtered.sort((a, b) => a.productName.localeCompare(b.productName));
        }

        renderMenu(filtered);
    }

    classFilterBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        classFilterDropdown.classList.toggle("hidden");
    });

    document.addEventListener("click", function (e) {
        if (!classFilterDropdown.contains(e.target) && e.target !== classFilterBtn) {
            classFilterDropdown.classList.add("hidden");
        }
    });

    searchInput.addEventListener("input", applyFiltersAndSort);
    sortSelect.addEventListener("change", applyFiltersAndSort);
    checkoutForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        try {
            checkoutBtn.disabled = true;
            const response = await fetch("../assets/php/complete_order.php", {
                method: "POST"
            });
            const responseText = await response.text();
            const result = JSON.parse(responseText);

            if (!response.ok) {
                throw new Error(result.error || "Failed to complete order");
            }

            setCartFeedback("Order #" + result.orderID + " completed successfully.");
            await refreshCart();
        } catch (error) {
            console.error(error);
            setCartFeedback(error.message, true);
            alert(error.message);
        }
    });

    refreshCart();

    fetch("../assets/php/menu.php")
        .then(function (response) {
            if (!response.ok) {
                throw new Error("Server returned " + response.status);
            }
            return response.json();
        })
        .then(function (data) {
            allProducts = data;
            buildClassCheckboxes(data);
            renderMenu(allProducts);
        })
        .catch(function (error) {
            console.error(error);
            menu.innerHTML = "<p id=\"menu-empty\">Unable to load menu items.</p>";
        });
});
