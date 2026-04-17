let allPromoCodes = [];

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

    name = document.createElement("th");
    name.innerText = "Action";
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

        cell = document.createElement("td");
        let editBtn = document.createElement("button");
        let removeBtn = document.createElement("button");
        editBtn.innerText = "Edit";
        removeBtn.innerText = "Remove";
        editBtn.className = "secondary-button";
        removeBtn.className = "secondary-button";
        editBtn.style.marginBottom = "5px";
        editBtn.style.marginRight = "5px";
        editBtn.addEventListener("click", () => editPromoCode(promocodes));
        removeBtn.addEventListener("click", () => removePromoCode(promocodes));
        cell.appendChild(editBtn);
        cell.appendChild(removeBtn);
        promoCodeRow.appendChild(cell);

        table.appendChild(promoCodeRow);
    }

    promoCodeTable.appendChild(table);
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
    console.log("get promo codes");
    getAllPromoCodes();
});

const addPromoCodeBtn = document.getElementById("addPromoCodeBtn");
const bottomAddPromoCodeBtn = document.getElementById("bottomAddPromoCodeBtn"); 
const popup = document.getElementById("popup");
const saveBtn = document.getElementById("savePromoCodeBtn");
const cancelBtn = document.getElementById("cancelBtn");

addPromoCodeBtn.addEventListener("click", addPromoCode);
bottomAddPromoCodeBtn.addEventListener("click", addPromoCode);

function addPromoCode() {
    const promoCodeIDInput = document.getElementById("promoID");
    const promoCodeInput = document.getElementById("promoCode");
    const discountTypeInput = document.getElementById("discountType");
    const discountValueInput = document.getElementById("discountValue");
    const statusInput = document.getElementById("active");
    const expiryDateInput = document.getElementById("expiryDate");
    promoCodeIDInput.value = "";
    promoCodeInput.value = "";
    discountTypeInput.value = "";
    discountValueInput.value = "";
    statusInput.value = "";
    expiryDateInput.value = "";
    popup.style.visibility = "visible";
}

saveBtn.addEventListener("click", function() {
    const promoCodeIDInput = document.getElementById("promoID");
    const promoCodeInput = document.getElementById("promoCode");
    const discountTypeInput = document.getElementById("discountType");
    const discountValueInput = document.getElementById("discountValue");
    const statusInput = document.getElementById("active");
    const expiryDateInput = document.getElementById("expiryDate");
    let promoCode = { promoID: promoCodeIDInput.value, promoCode: promoCodeInput.value, discountType: discountTypeInput.value, discountValue: discountValueInput.value, active: statusInput.value, expiryDate: expiryDateInput.value };
    savePromoCode(promoCode);
    popup.style.visibility = "hidden";
});

cancelBtn.addEventListener("click", function() {
    popup.style.visibility = "hidden";
});

function savePromoCode(promoCode) {
    if (!promoCode) {
        return;
    }
    let urlEncodedPromoCode = "";
    urlEncodedPromoCode += "promoID=" + promoCode.promoID + "&";
    urlEncodedPromoCode += "promoCode=" + promoCode.promoCode + "&";
    urlEncodedPromoCode += "discountType=" + promoCode.discountType + "&";
    urlEncodedPromoCode += "discountValue=" + promoCode.discountValue + "&";
    urlEncodedPromoCode += "active=" + promoCode.active + "&";
    urlEncodedPromoCode += "expiryDate=" + promoCode.expiryDate + "";
    fetch("../assets/php/admin.php?savePromoCode", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: urlEncodedPromoCode
    })
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

function removePromoCode(promocode) {
    let result = confirm("Are you sure you want to remove the promo code " + promocode.promoCode + "?");
    if (result) {
        let urlEncodedPromoCode = "";
        urlEncodedPromoCode += "promoID=" + promocode.promoID + "";
        fetch("../assets/php/admin.php?removePromoCode", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: urlEncodedPromoCode
    })
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
}

function editPromoCode(promocode) {
    //console.log("Date: ", promocode.expiryDate);
    const promoCodeIDInput = document.getElementById("promoID");
    const promoCodeInput = document.getElementById("promoCode");
    const discountTypeInput = document.getElementById("discountType");
    const discountValueInput = document.getElementById("discountValue");
    const statusInput = document.getElementById("active");
    const expiryDateInput = document.getElementById("expiryDate");
    promoCodeIDInput.value = promocode.promoID;
    promoCodeInput.value = promocode.promoCode;
    discountTypeInput.value = promocode.discountType;
    discountValueInput.value = promocode.discountValue;
    statusInput.value = promocode.active;
    //expiryDateInput.value = promocode.expiryDate;
    if (promocode.expiryDate) {
        expiryDateInput.valueAsDate = new Date(promocode.expiryDate);
    }
    popup.style.visibility = "visible";    
    
}