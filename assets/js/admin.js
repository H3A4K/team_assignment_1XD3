let allProducts = [];

function renderProductTable(data) {
    const productTable = document.getElementById("product-table");

    productTable.innerHTML = "";

    if (data.length === 0) {
        const empty = document.createElement("p");
        empty.id = "menu-empty";
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

window.addEventListener("load", function () {
    console.log("load");
    fetch("../assets/php/menu.php")
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

    
});