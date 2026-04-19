/**
 * manage_products.js
 *
 * Client-side script for the admin "Manage Products" page. Loads the
 * product list, renders it into a responsive table with Edit/Remove
 * buttons, and wires up the add/edit popup (including live image
 * preview when a new image file is selected). Image uploads are sent
 * as multipart form data so the PHP backend can validate and save
 * them to disk.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 03, 2026
 */

let allProducts = [];

/**
 * Builds the products table for the admin page. Each row shows ID,
 * name, description, price, image, class, and Edit/Remove buttons.
 * Data-label attributes are added so the responsive mobile CSS can
 * render the cells as stacked key/value pairs.
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

        cell.setAttribute("data-label", "Product ID");
        cell.innerText = product.productID;
        productRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Product Name");
        cell.innerText = product.productName;
        productRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Description");
        cell.innerText = product.productDesc;
        productRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Price");
        cell.innerText = "$" + parseFloat(product.price).toFixed(2);
        productRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Image");
        let img = document.createElement("img");
        img.src = "../assets/images/menu/" + product.productImg;
        img.className = "table-img";
        cell.appendChild(img);
        productRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Product Class");
        cell.innerText = product.productClass;
        productRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Action");
        cell.style.display = "flex";
        cell.style.flexDirection = "column";
        cell.style.alignItems = "stretch";
        cell.style.gap = "5px";
        let editBtn = document.createElement("button");
        let removeBtn = document.createElement("button");
        editBtn.innerText = "Edit";
        removeBtn.innerText = "Remove";
        editBtn.className = "secondary-button";
        removeBtn.className = "secondary-button";
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
 * Fetches every product from the server and re-renders the products
 * table. Called on page load and after any save/remove.
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
 * into the class <select> inside the add/edit popup.
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

let saveProductBtn;

window.addEventListener("load", function () {
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

/**
 * Opens the add-product popup with every field cleared and the image
 * preview reset to the default placeholder image. Save treats this as
 * an insert because productID is empty.
 */
function addProduct() {
    const productIDInput = document.getElementById("productID");
    const productNameInput = document.getElementById("productName");
    const productDescInput = document.getElementById("productDesc");
    const priceInput = document.getElementById("price");
    const productClassSelect = document.getElementById("productClassesSelect");
    const currentProductImgInput = document.getElementById("currentProductImg");
    const productImgFileInput = document.getElementById("productImgFile");
    const productImgPreview = document.getElementById("productImgPreview");
    productIDInput.value = "";
    productNameInput.value = "";
    productDescInput.value = "";
    priceInput.value = "";
    productClassSelect.value = "";
    currentProductImgInput.value = "";
    productImgFileInput.value = "";
    productImgPreview.src = "../assets/images/menu/placeholder.jpg";
    popup.style.visibility = "visible";
}

saveBtn.addEventListener("click", function() {
    const productIDInput = document.getElementById("productID");
    const productNameInput = document.getElementById("productName");
    const productDescInput = document.getElementById("productDesc");
    const priceInput = document.getElementById("price");
    const productClassSelect = document.getElementById("productClassesSelect");
    const currentProductImgInput = document.getElementById("currentProductImg");
    const productImgFileInput = document.getElementById("productImgFile");

    const formData = new FormData();
    formData.append("productID", productIDInput.value);
    formData.append("productName", productNameInput.value);
    formData.append("productDesc", productDescInput.value);
    formData.append("price", priceInput.value);
    formData.append("productClass", productClassSelect.value);
    formData.append("currentProductImg", currentProductImgInput.value);
    if (productImgFileInput.files && productImgFileInput.files[0]) {
        formData.append("productImgFile", productImgFileInput.files[0]);
    }

    saveProduct(formData);
    popup.style.visibility = "hidden";
});

cancelBtn.addEventListener("click", function() {
    popup.style.visibility = "hidden";
});

// Live preview when admin picks a new image file
const productImgFileInput = document.getElementById("productImgFile");
if (productImgFileInput) {
    productImgFileInput.addEventListener("change", function(e) {
        const file = e.target.files && e.target.files[0];
        const preview = document.getElementById("productImgPreview");
        if (!file || !preview) return;
        const reader = new FileReader();
        reader.onload = function(evt) {
            preview.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    });
}

/**
 * POSTs the add/edit-product form to the server as multipart form
 * data (so an uploaded image file is included). When productID is set
 * the server updates, otherwise it inserts. The refreshed product
 * list in the response is rendered back into the table.
 *
 * @param {FormData} formData the built-up form data including any uploaded image
 */
function saveProduct(formData) {
    if (!formData) {
        return;
    }

    fetch("../assets/php/admin.php?saveProduct", {
        method: 'POST',
        body: formData
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
 * Confirms with the admin and, if accepted, deletes the given product
 * on the server and refreshes the table.
 *
 * @param {Object} product the product row being removed (uses productID and productName)
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
 * admin can edit it. The image preview is loaded from the product's
 * current image so the admin can see what they already have.
 *
 * @param {Object} product the product row being edited
 */
function editProduct(product) {
    const productIDInput = document.getElementById("productID");
    const productNameInput = document.getElementById("productName");
    const productDescInput = document.getElementById("productDesc");
    const priceInput = document.getElementById("price");
    const productClassSelect = document.getElementById("productClassesSelect");
    const currentProductImgInput = document.getElementById("currentProductImg");
    const productImgFileInput = document.getElementById("productImgFile");
    const productImgPreview = document.getElementById("productImgPreview");
    productIDInput.value = product.productID;
    productNameInput.value = product.productName;
    productDescInput.value = product.productDesc;
    priceInput.value = product.price;
    productClassSelect.value = product.productClass;
    currentProductImgInput.value = product.productImg || "";
    productImgFileInput.value = "";
    productImgPreview.src = "../assets/images/menu/" + (product.productImg || "placeholder.jpg");
    popup.style.visibility = "visible";    
}