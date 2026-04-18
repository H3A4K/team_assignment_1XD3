let allProducts = [];

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

let saveProductBtn;

window.addEventListener("load", function () {
    console.log("get products");
    getAllProducts();
    getProductClasses();
});

const addProductBtn = document.getElementById("addProductBtn");
const bottomAddProductBtn = document.getElementById("bottomAddProductBtn");
const popup = document.getElementById("popup");
const saveBtn = document.getElementById("saveProductBtn");
const cancelBtn = document.getElementById("cancelBtn");

addProductBtn.addEventListener("click", addProduct);
bottomAddProductBtn.addEventListener("click", addProduct);

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

function removeProduct(product) {
    // alert(product.productName);
    let result = confirm("Are you sure you want to remove " + product.productName + " " + "from the menu?");
    if (result) {
        // alert("Product will be removed");
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