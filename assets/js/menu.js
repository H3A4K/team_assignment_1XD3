const cart = document.getElementById("cd-cart");
const closeIcon = document.getElementById("cd-close-icon");
const cartTriggerLink = document.querySelector("#cd-cart-trigger a");

if (cartTriggerLink && cart) {
    cartTriggerLink.addEventListener("click", function (e) {
        if (window.innerWidth <= 900) {
            e.preventDefault();
            cart.classList.add("cart-open");
        }
    });
}

if (closeIcon && cart) {
    closeIcon.addEventListener("click", function () {
        cart.classList.remove("cart-open");
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
    const cartDiscountRow = document.getElementById("cd-cart-discount-row");
    const cartDiscount = document.getElementById("cd-cart-discount");
    const cartTax = document.getElementById("cd-cart-tax");
    const cartTotal = document.getElementById("cd-cart-total");
    const openCheckoutBtn = document.getElementById("cd-cart-open-checkout");
    const checkoutPanel = document.getElementById("cd-cart-checkout-panel");
    const checkoutForm = document.getElementById("cd-cart-checkout-form");
    const checkoutBackBtn = document.getElementById("cd-cart-back");
    const placeOrderBtn = document.getElementById("cd-cart-place-order");
    const fulfillmentInputs = document.querySelectorAll("input[name='fulfillmentMethod']");
    const deliveryFields = document.getElementById("checkout-delivery-fields");
    const deliveryAddressInput = document.getElementById("checkout-delivery-address");
    const checkoutSummaryMethod = document.getElementById("checkout-summary-method");
    const checkoutSummaryTax = document.getElementById("checkout-summary-tax");
    const checkoutSummaryTotal = document.getElementById("checkout-summary-total");
    const checkoutSummaryAddressRow = document.getElementById("checkout-summary-address-row");
    const checkoutSummaryAddress = document.getElementById("checkout-summary-address");
    const cartFeedback = document.getElementById("cd-cart-feedback");
    const promocodeInput = document.getElementById("promocode-input");
    const promocodeApplyBtn = document.getElementById("promocode-apply-btn");
    const appliedPromoBox = document.getElementById("applied-promocode");
    const appliedPromoLabel = document.getElementById("applied-promocode-label");
    const removePromocodeBtn = document.getElementById("remove-promocode-btn");

    if (!menu || !searchInput || !sortSelect || !classFilterBtn || !classFilterDropdown || !cartItems || !cartStatus || !cartSubtotal || !cartTax || !cartTotal || !openCheckoutBtn || !checkoutPanel || !checkoutForm || !checkoutBackBtn || !placeOrderBtn || !deliveryFields || !deliveryAddressInput || !checkoutSummaryMethod || !checkoutSummaryTax || !checkoutSummaryTotal || !checkoutSummaryAddressRow || !checkoutSummaryAddress || !cartFeedback) {
        console.error("Menu page is missing expected cart or filter elements.");
        return;
    }

    let allProducts = [];
    let selectedClasses = new Set();
    let currentCartData = null;

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
            hideCartToast();
            return;
        }

        cartFeedback.hidden = false;
        cartFeedback.textContent = message;
        cartFeedback.classList.toggle("is-error", isError);
        showCartToast(message, isError);
    }

    const cartToast = document.getElementById("cart-toast");
    const cartToastMessage = document.getElementById("cart-toast-message");
    let cartToastTimer = null;

    function showCartToast(message, isError = false) {
        if (!cartToast || !cartToastMessage) return;
        cartToastMessage.textContent = message;
        cartToast.classList.toggle("is-error", isError);
        cartToast.hidden = false;
        void cartToast.offsetWidth;
        cartToast.dataset.visible = "true";

        if (cartToastTimer) clearTimeout(cartToastTimer);
        cartToastTimer = setTimeout(hideCartToast, 3500);
    }

    function hideCartToast() {
        if (!cartToast) return;
        if (cartToastTimer) {
            clearTimeout(cartToastTimer);
            cartToastTimer = null;
        }
        cartToast.dataset.visible = "false";
        setTimeout(function () {
            if (cartToast.dataset.visible !== "true") {
                cartToast.hidden = true;
            }
        }, 250);
    }

    function getSelectedFulfillmentMethod() {
        const selected = document.querySelector("input[name='fulfillmentMethod']:checked");
        return selected ? selected.value : "pickup";
    }

    function syncCheckoutSummary() {
        const method = getSelectedFulfillmentMethod();
        checkoutSummaryMethod.textContent = method === "delivery" ? "Delivery" : "Pickup";
        checkoutSummaryTax.textContent = cartTax.textContent;
        checkoutSummaryTotal.textContent = cartTotal.textContent;

        if (method === "delivery") {
            const address = deliveryAddressInput.value.trim();
            checkoutSummaryAddress.textContent = address || "Enter a delivery address";
            checkoutSummaryAddressRow.hidden = false;
        } else {
            checkoutSummaryAddress.textContent = "";
            checkoutSummaryAddressRow.hidden = true;
        }
    }

    function syncFulfillmentFields() {
        const method = getSelectedFulfillmentMethod();
        const isDelivery = method === "delivery";
        deliveryFields.hidden = !isDelivery;
        deliveryAddressInput.required = isDelivery;
        syncCheckoutSummary();
    }

    function closeCheckoutPanel() {
        checkoutPanel.hidden = true;
        openCheckoutBtn.hidden = false;
        cart.classList.remove("checkout-active");
    }

    function openCheckoutPanel() {
        if (!currentCartData || !currentCartData.loggedIn || !currentCartData.hasOpenOrder || currentCartData.items.length === 0) {
            return;
        }

        syncFulfillmentFields();
        checkoutPanel.hidden = false;
        openCheckoutBtn.hidden = true;
        cart.classList.add("checkout-active");
    }

    function renderCart(cartData) {
        currentCartData = cartData;
        cartItems.innerHTML = "";
        cartSubtotal.textContent = formatCurrency(cartData.subtotal || 0);
        cartTax.textContent = formatCurrency(cartData.tax || 0);
        cartTotal.textContent = formatCurrency(cartData.total || 0);

        // Discount row: only show when there's a real non-zero discount
        const discountAmount = Number(cartData.discount || 0);
        if (cartDiscount) {
            cartDiscount.textContent = discountAmount > 0
                ? "−" + formatCurrency(discountAmount)
                : "−$0.00";
        }
        if (cartDiscountRow) cartDiscountRow.hidden = !(discountAmount > 0);

        // Applied-promo chip: only show when there's a real non-empty code string
        const appliedCode = typeof cartData.appliedPromoCode === "string"
            ? cartData.appliedPromoCode.trim()
            : "";
        if (appliedCode && appliedPromoBox && appliedPromoLabel) {
            appliedPromoLabel.textContent = appliedCode;
            appliedPromoBox.hidden = false;
            if (promocodeInput) promocodeInput.value = "";
        } else {
            if (appliedPromoLabel) appliedPromoLabel.textContent = "";
            if (appliedPromoBox) appliedPromoBox.hidden = true;
        }

        if (!cartData.loggedIn) {
            setCartStatus("Log in to view your cart and checkout.");
            openCheckoutBtn.disabled = true;
            closeCheckoutPanel();
            return;
        }

        if (!cartData.hasOpenOrder || cartData.items.length === 0) {
            setCartStatus("Your cart is empty.");
            openCheckoutBtn.disabled = true;
            closeCheckoutPanel();
            return;
        }

        cartStatus.hidden = true;
        openCheckoutBtn.disabled = false;

        for (const item of cartData.items) {
            const li = document.createElement("li");

            const itemInfo = document.createElement("div");
            itemInfo.className = "cd-item-info";
            const qty = document.createElement("span");
            qty.className = "cd-qty";
            qty.textContent = item.quantity + "x";
            itemInfo.append(qty, " " + item.productName);

            const right = document.createElement("div");
            right.className = "cd-item-right";

            const price = document.createElement("div");
            price.className = "cd-price";
            price.textContent = formatCurrency(item.lineTotal);

            const removeBtn = document.createElement("button");
            removeBtn.type = "button";
            removeBtn.className = "cd-item-remove";
            removeBtn.setAttribute("aria-label", "Remove " + item.productName + " from cart");
            removeBtn.textContent = "×";
            removeBtn.addEventListener("click", () => removeFromCart(item.orderDetailID, item.productName));

            right.append(price, removeBtn);
            li.append(itemInfo, right);
            cartItems.appendChild(li);
        }

        syncCheckoutSummary();
    }

    async function removeFromCart(orderDetailID, productName) {
        if (!orderDetailID) return;
        try {
            const response = await fetch("../assets/php/remove_from_order.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ orderDetailID })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || "Failed to remove item");

            setCartFeedback((productName || "Item") + " removed from cart.");
            await refreshCart();
        } catch (error) {
            console.error(error);
            setCartFeedback(error.message, true);
        }
    }

    async function applyPromocode() {
        if (!promocodeInput) return;
        const code = promocodeInput.value.trim();
        if (code === "") {
            setCartFeedback("Enter a promo code first.", true);
            return;
        }
        try {
            promocodeApplyBtn.disabled = true;
            const response = await fetch("../assets/php/apply_promocode.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ promoCode: code })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || "Invalid promo code");

            setCartFeedback("Promo code \"" + result.promoCode + "\" applied.");
            await refreshCart();
        } catch (error) {
            console.error(error);
            setCartFeedback(error.message, true);
        } finally {
            promocodeApplyBtn.disabled = false;
        }
    }

    async function removePromocode() {
        try {
            const response = await fetch("../assets/php/remove_promocode.php", {
                method: "POST"
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || "Failed to remove promo code");
            setCartFeedback("Promo code removed.");
            await refreshCart();
        } catch (error) {
            console.error(error);
            setCartFeedback(error.message, true);
        }
    }

    if (promocodeApplyBtn) {
        promocodeApplyBtn.addEventListener("click", applyPromocode);
    }
    if (promocodeInput) {
        promocodeInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                applyPromocode();
            }
        });
    }
    if (removePromocodeBtn) {
        removePromocodeBtn.addEventListener("click", removePromocode);
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
            openCheckoutBtn.disabled = true;
            closeCheckoutPanel();
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
    openCheckoutBtn.addEventListener("click", openCheckoutPanel);
    checkoutBackBtn.addEventListener("click", closeCheckoutPanel);
    fulfillmentInputs.forEach((input) => {
        input.addEventListener("change", syncFulfillmentFields);
    });
    deliveryAddressInput.addEventListener("input", syncCheckoutSummary);
    checkoutForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        try {
            const fulfillmentMethod = getSelectedFulfillmentMethod();
            const deliveryAddress = deliveryAddressInput.value.trim();

            if (fulfillmentMethod === "delivery" && deliveryAddress === "") {
                throw new Error("Enter a delivery address before placing the order");
            }

            placeOrderBtn.disabled = true;
            checkoutBackBtn.disabled = true;
            const response = await fetch("../assets/php/complete_order.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    fulfillmentMethod,
                    deliveryAddress
                })
            });
            const responseText = await response.text();
            const result = JSON.parse(responseText);

            if (!response.ok) {
                throw new Error(result.error || "Failed to complete order");
            }

            window.location.href = result.redirectUrl || ("../pickup/?order_id=" + result.orderID);
        } catch (error) {
            console.error(error);
            setCartFeedback(error.message, true);
        } finally {
            placeOrderBtn.disabled = false;
            checkoutBackBtn.disabled = false;
        }
    });

    if (cartFeedback && !cartFeedback.hidden && cartFeedback.textContent.trim()) {
        showCartToast(cartFeedback.textContent.trim(), cartFeedback.classList.contains("is-error"));
    }

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
