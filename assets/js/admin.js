
let allProducts = [];
let allOrders = [];
let allPromoCodes = [];

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

        table.appendChild(productRow);
    }

    productTable.appendChild(table);
}

function reloadProductClassesSelect(data) {
    console.log("reload product")
    const classSelect = document.getElementById("productClassesSelect");
    if (!classSelect) return;

    if (data.length == 0) {
        const newOption = new Option("No classes available", "empty");
    }
    else {
        for (let i = 0; i < data.length; i++) {
            const newOption = new Option(data[i].name, data[i].classID);
            classSelect.add(newOption);
        }
    }
}

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

    // name = document.createElement("th");
    // name.innerText = "Discount Type";
    // headerRow.appendChild(name);

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

        // cell = document.createElement("td");
        // cell.innerText = promocodes.discountType;
        // promoCodeRow.appendChild(cell);

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



window.addEventListener("load", function () {
    console.log("get products");
    getAllProducts();
    getProductClasses();
});

window.addEventListener("load", function () {
    console.log("get orders");
    getAllOrders();
});

window.addEventListener("load", function () {
    console.log("get promo codes");
    getAllPromoCodes();
});

