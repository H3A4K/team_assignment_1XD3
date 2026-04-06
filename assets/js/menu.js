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

window.addEventListener("load", function () {

    const menu = document.getElementById("menu");
    const searchInput = document.getElementById("menu-search");
    const sortSelect = document.getElementById("menu-sort");
    const classFilterBtn = document.getElementById("class-filter-btn");
    const classFilterDropdown = document.getElementById("class-filter-dropdown");

    classFilterBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        classFilterDropdown.classList.toggle("hidden");
    });

    document.addEventListener("click", function (e) {
        if (!classFilterDropdown.contains(e.target) && e.target !== classFilterBtn) {
            classFilterDropdown.classList.add("hidden");
        }
    });

    let allProducts = [];
    let selectedClasses = new Set(); // empty = show all
    
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
            price.textContent = "$" + parseFloat(product.price).toFixed(2);

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

                    console.log("Added to order:", result);
                    alert(product.productName + " added to cart");
                } catch (error) {
                    console.error(error);
                    alert(error.message);
                }
            });

            desc.append(classTag, name, price, itemdesc);
            item.append(img, br, desc, addBtn);
            menu.appendChild(item);
        }
    }

    function buildClassCheckboxes(data) {
        const classes = [...new Set(data.map(p => p.productClass))].sort();
        classes.forEach(cls => {
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

        let filtered = allProducts.filter(p =>
            (selectedClasses.size === 0 || selectedClasses.has(p.productClass)) && (
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

    searchInput.addEventListener("input", applyFiltersAndSort);
    sortSelect.addEventListener("change", applyFiltersAndSort);

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
        });

})
