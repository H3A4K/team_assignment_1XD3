/**
 * admin.js
 *
 * Legacy combined admin dashboard script. Fetches and renders the
 * products, orders, and promo codes tables and provides the
 * add/edit/remove product popup. Superseded on the dedicated
 * Products/Orders/Promo Codes pages by manage_products.js,
 * manage_orders.js, and manage_promo_codes.js respectively.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 03, 2026
 */

let allProducts = [];
let allOrders = [];
let allPromoCodes = [];

/**
 * Builds the products table on the admin dashboard from the server
 * response. Each row shows ID, name, description, price, image,
 * class, and Edit/Remove buttons.
 *
 * @param {Array} data array of product objects returned by admin.php?getAllProducts
 */
function renderProductTable(data) {
    const productTable = document.getElementById("product-table");
    if (!productTable) return;

    productTable.innerHTML = "";

    if (data.length === 0) {
        const empty = document.createElement("p");
        empty.id = "products-empty";
        empty.textContent = "No products found.";
        productTable.appendChild(empty);
        return;
    }

    let table = document.createElement("table");
    table.className = "table";

    const headerRow = document.createElement("tr");
    let name = document.createElement("th");
    name.innerText = "Product ID";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Product Name";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Description";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Price";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Image";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Product Class";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Action";
    headerRow.appendChild(name);    

    table.appendChild(headerRow);

    for (const product of data) {

        const productRow = document.createElement("tr");
        let cell = document.createElement("td");

        cell.innerText = product.productID;
        productRow.appendChild(cell);

        cell = document.createElement("td");
        cell.innerText = product.productName;
        productRow.appendChild(cell);

        cell = document.createElement("td");
        cell.innerText = product.productDesc;
        productRow.appendChild(cell);

        cell = document.createElement("td");
        cell.innerText = "$" + parseFloat(product.price).toFixed(2);
        productRow.appendChild(cell);

        cell = document.createElement("td");
        let img = document.createElement("img");
        img.src = "../assets/images/menu/" + product.productImg;
        img.className = "table-img";
        cell.appendChild(img);
        productRow.appendChild(cell);

        cell = document.createElement("td");
        cell.innerText = product.productClass;
        productRow.appendChild(cell);

        cell = document.createElement("td");
        let editBtn = document.createElement("button");
        let removeBtn = document.createElement("button");
        editBtn.innerText = "Edit";
        removeBtn.innerText = "Remove";
        editBtn.className = "secondary-button";
        removeBtn.className = "secondary-button";
        editBtn.style.marginBottom = "5px";
        editBtn.style.marginRight = "5px";
        editBtn.addEventListener("click", () => editProduct(product));
        removeBtn.addEventListener("click", () => removeProduct(product));
        cell.appendChild(editBtn);
        cell.appendChild(removeBtn);
        productRow.appendChild(cell);

        table.appendChild(productRow);
    }

    productTable.appendChild(table);
}

/**
 * Populates the product-class <select> in the add/edit popup with the
 * list of classes returned by the server.
 *
 * @param {Array} data array of class objects (each with a `name` field)
 */
function reloadProductClassesSelect(data) {
    console.log("reload product")
    const classSelect = document.getElementById("productClassesSelect");
    if (!classSelect) return;

    if (data.length == 0) {
        const newOption = new Option("No classes available", "empty");
    }
    else {
        for (let i = 0; i < data.length; i++) {
            const newOption = new Option(data[i].name, data[i].name);
            classSelect.add(newOption);
        }
    }
}

/**
 * Builds the orders table on the admin dashboard. Each row shows the
 * orderID, customer accountID, order date, address, and a status cell
 * ("Completed" or "Incomplete").
 *
 * @param {Array} data array of order objects returned by admin.php?getAllOrders
 */
function renderOrderTable(data) {
    const orderTable = document.getElementById("order-table");
    if (!orderTable) return;

    orderTable.innerHTML = "";

    if (data.length === 0) {
        const empty = document.createElement("p");
        empty.id = "orders-empty";
        empty.textContent = "No orders found.";
        productTable.appendChild(empty);
        return;
    }

    let table = document.createElement("table");
    table.className = "table";

    const headerRow = document.createElement("tr");
    let name = document.createElement("th");
    name.innerText = "Order ID";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Account ID";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Order Date";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Address";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Status";
    headerRow.appendChild(name);

    table.appendChild(headerRow);

    for (const order of data) {

        const orderRow = document.createElement("tr");
        let cell = document.createElement("td");

        cell.innerText = order.orderID;
        orderRow.appendChild(cell);

        cell = document.createElement("td");
        cell.innerText = order.accountID;
        orderRow.appendChild(cell);

        cell = document.createElement("td");
        cell.innerText = order.orderDate;
        orderRow.appendChild(cell);

        cell = document.createElement("td");
        cell.innerText = order.address;
        orderRow.appendChild(cell);

        cell = document.createElement("td");
        if (order.fullfilled === 1) {
            cell.innerText = "Completed";
        }
        else if (order.fullfilled === 0) {
            cell.innerText = "Incomplete";
        }
        orderRow.appendChild(cell);

        table.appendChild(orderRow);
    }

    orderTable.appendChild(table);
}

/**
 * Builds the promo codes table on the admin dashboard. Each row shows
 * the ID, code text, formatted discount value (percentage or dollar
 * amount), status, and expiry date.
 *
 * @param {Array} data array of promo code objects returned by admin.php?getAllPromoCodes
 */
function renderPromoCodeTable(data) {
    const promoCodeTable = document.getElementById("promocodes-table");
    if (!promoCodeTable) return;

    promoCodeTable.innerHTML = "";

    if (data.length === 0) {
        const empty = document.createElement("p");
        empty.id = "promocodes-empty";
        empty.textContent = "No promo codes found.";
        promoCodeTable.appendChild(empty);
        return;
    }

    let table = document.createElement("table");
    table.className = "table";

    const headerRow = document.createElement("tr");
    let name = document.createElement("th");
    name.innerText = "Promo Code ID";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Promo Code";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Discount Value";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Status";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Expiry Date";
    headerRow.appendChild(name);

    table.appendChild(headerRow);

    for (const promocodes of data) {

        const promoCodeRow = document.createElement("tr");
        let cell = document.createElement("td");

        cell.innerText = promocodes.promoID;
        promoCodeRow.appendChild(cell);

        cell = document.createElement("td");
        cell.innerText = promocodes.promoCode;
        promoCodeRow.appendChild(cell);

        cell = document.createElement("td");
        if (promocodes.discountType === "percentage") {
            cell.innerText = promocodes.discountValue + "%";
        }
        else if (promocodes.discountType === "fixed") {
            cell.innerText = "$" + parseFloat(promocodes.discountValue).toFixed(2);
        }
        promoCodeRow.appendChild(cell);

        cell = document.createElement("td");
        if (promocodes.active === 1) {
            cell.innerText = "Active";
        }
        else if (promocodes.active === 0) {
            cell.innerText = "Inoperative";
        }
        promoCodeRow.appendChild(cell);

        cell = document.createElement("td");
        cell.innerText = promocodes.expiryDate;
        promoCodeRow.appendChild(cell);

        table.appendChild(promoCodeRow);
    }

    promoCodeTable.appendChild(table);
}

/**
 * Fetches every product from the server and re-renders the products
 * table. Called on page load and after any add/remove/edit action.
 */
function getAllProducts() {
    fetch("../assets/php/admin.php?getAllProducts")
        .then(function (response) {
            if (!response.ok) {
                throw new Error("Server returned " + response.status);
            }
            return response.json();
        })
        .then(function (data) {
            allProducts = data;
            renderProductTable(allProducts);
        });
}

/**
 * Fetches the list of product classes from the server and loads them
 * into the class <select> in the add/edit popup.
 */
function getProductClasses() {
    fetch("../assets/php/admin.php?getProductClasses")
        .then(function (response) {
            if (!response.ok) {
                throw new Error("Server returned " + response.status);
            }
            return response.json();
        })
        .then(function (data) {
            allProductClasses = data;
            reloadProductClassesSelect(allProductClasses);
        });
}

/**
 * Fetches every order from the server and re-renders the orders table.
 */
function getAllOrders() {
    fetch("../assets/php/admin.php?getAllOrders")
    .then(function (response) {
            if (!response.ok) {
                throw new Error("Server returned " + response.status);
            }
            return response.json();
        })
        .then(function (data) {
            allOrders = data;
            renderOrderTable(allOrders);
        });
}

/**
 * Fetches every promo code from the server and re-renders the promo
 * codes table.
 */
function getAllPromoCodes() {
    fetch("../assets/php/admin.php?getAllPromoCodes")
        .then(function (response) {
            if (!response.ok) {
                throw new Error("Server returned " + response.status);
            }
            return response.json();
        })
        .then(function (data) {
            allPromoCodes = data;
            renderPromoCodeTable(allPromoCodes);
        });
}

let saveProductBtn;

window.addEventListener("load", function () {
    getAllProducts();
    getProductClasses();
});

window.addEventListener("load", function () {
    getAllOrders();
});

window.addEventListener("load", function () {
    getAllPromoCodes();
});

const addProductBtn = document.getElementById("addProductBtn");
const bottomAddProductBtn = document.getElementById("bottomAddProductBtn");
const popup = document.getElementById("popup");
const saveBtn = document.getElementById("saveProductBtn");
const cancelBtn = document.getElementById("cancelBtn");

addProductBtn.addEventListener("click", addProduct);
bottomAddProductBtn.addEventListener("click", addProduct);

/**
 * Opens the add-product popup with all fields blank (and productID
 * cleared, so save treats this as an insert rather than an update).
 */
function addProduct() {
    const productIDInput = document.getElementById("productID");
    const productNameInput = document.getElementById("productName");
    const productDescInput = document.getElementById("productDesc");
    const priceInput = document.getElementById("price");
    const productClassSelect = document.getElementById("productClassesSelect");
    productIDInput.value = "";
    productNameInput.value = "";
    productDescInput.value = "";
    priceInput.value = "";
    productClassSelect.value = "";
    popup.style.visibility = "visible";
}

saveBtn.addEventListener("click", function() {
    const productIDInput = document.getElementById("productID");
    const productNameInput = document.getElementById("productName");
    const productDescInput = document.getElementById("productDesc");
    const priceInput = document.getElementById("price");
    const productClassSelect = document.getElementById("productClassesSelect");
    let product = { productID: productIDInput.value, productName: productNameInput.value, productDesc: productDescInput.value, price: priceInput.value, productClass: productClassSelect.value };
    saveProduct(product);
    popup.style.visibility = "hidden";
});

cancelBtn.addEventListener("click", function() {
    popup.style.visibility = "hidden";
});


/**
 * POSTs the add/edit-product form to the server. If `product.productID`
 * is set the server updates an existing row, otherwise it inserts a
 * new one. After the save the products table is re-rendered.
 *
 * @param {Object} product the product fields to save (productID, productName, productDesc, price, productClass)
 */
function saveProduct(product) {
    if (!product) {
        return;
    }
    let urlEncodedProduct = "";
    urlEncodedProduct += "productID=" + product.productID + "&";
    urlEncodedProduct += "productName=" + product.productName + "&";
    urlEncodedProduct += "productDesc=" + product.productDesc + "&";
    urlEncodedProduct += "price=" + product.price + "&";
    urlEncodedProduct += "productClass=" + product.productClass + "";
    fetch("../assets/php/admin.php?saveProduct", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: urlEncodedProduct
    })
    .then(function (response) {
        if (!response.ok) {
            throw new Error("Server returned " + response.status);
        }
        return response.json();
    })
    .then(function (data) {
        allProducts = data;
        renderProductTable(allProducts);
    });

}

/**
 * Confirms with the admin and, if accepted, asks the server to delete
 * the given product. The products table is refreshed after the delete.
 *
 * @param {Object} product the product object for the row being removed (uses productID and productName)
 */
function removeProduct(product) {
    let result = confirm("Are you sure you want to remove " + product.productName + " " + "from the menu?");
    if (result) {
        let urlEncodedProduct = "";
        urlEncodedProduct += "productID=" + product.productID + "";
        fetch("../assets/php/admin.php?removeProduct", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: urlEncodedProduct
    })
    .then(function (response) {
        if (!response.ok) {
            throw new Error("Server returned " + response.status);
        }
        return response.json();
    })
    .then(function (data) {
        allProducts = data;
        renderProductTable(allProducts);
    });
    }
}

/**
 * Opens the popup pre-filled with the given product's values so the
 * admin can edit it. On save, the filled-in productID tells the server
 * to UPDATE the existing row rather than INSERT a new one.
 *
 * @param {Object} product the product row being edited
 */
function editProduct(product) {
    const productIDInput = document.getElementById("productID");
    const productNameInput = document.getElementById("productName");
    const productDescInput = document.getElementById("productDesc");
    const priceInput = document.getElementById("price");
    const productClassSelect = document.getElementById("productClassesSelect");
    productIDInput.value = product.productID;
    productNameInput.value = product.productName;
    productDescInput.value = product.productDesc;
    priceInput.value = product.price;
    productClassSelect.value = product.productClass;
    popup.style.visibility = "visible";    
}