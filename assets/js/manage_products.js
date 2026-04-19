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

function saveProduct(formData) {
    if (!formData) {
        return;
    }
    // NOTE: Do NOT set Content-Type header manually — the browser must set
    // it to multipart/form-data with the correct boundary for file uploads.
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