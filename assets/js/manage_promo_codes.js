/**
 * manage_promo_codes.js
 *
 * Client-side script for the admin "Manage Promo Codes" page. Fetches
 * the full list of discount promo codes and renders them into a table
 * with Edit/Remove buttons, and wires up the add/edit popup for the
 * code text, discount type and value, status, expiry date, and
 * required product IDs.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 16, 2026
 */

let allPromoCodes = [];

/**
 * Builds the promo codes table. Each row shows ID, code text, formatted
 * discount value, status, expiry date, required product IDs, and
 * Edit/Remove buttons.
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

    name = document.createElement("th");
    name.innerText = "Required Products";
    headerRow.appendChild(name);

    name = document.createElement("th");
    name.innerText = "Action";
    headerRow.appendChild(name); 

    table.appendChild(headerRow);

    for (const promocodes of data) {

        const promoCodeRow = document.createElement("tr");
        let cell = document.createElement("td");

        cell.setAttribute("data-label", "Promo Code ID");
        cell.innerText = promocodes.promoID;
        promoCodeRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Promo Code");
        cell.innerText = promocodes.promoCode;
        promoCodeRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Discount Value");
        if (promocodes.discountType === "percentage") {
            cell.innerText = promocodes.discountValue + "%";
        }
        else if (promocodes.discountType === "fixed") {
            cell.innerText = "$" + parseFloat(promocodes.discountValue).toFixed(2);
        }
        promoCodeRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Status");
        if (promocodes.active === 1) {
            cell.innerText = "Active";
        }
        else if (promocodes.active === 0) {
            cell.innerText = "Inoperative";
        }
        promoCodeRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Expiry Date");
        cell.innerText = promocodes.expiryDate;
        promoCodeRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Required Products");
        cell.innerText = promocodes.requiredProductIDs || "—";
        promoCodeRow.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Action");
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

/**
 * Fetches every promo code from the server and re-renders the table.
 * Called on page load and after any save/remove.
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

window.addEventListener("load", function () {
    getAllPromoCodes();
});

const addPromoCodeBtn = document.getElementById("addPromoCodeBtn");
const bottomAddPromoCodeBtn = document.getElementById("bottomAddPromoCodeBtn"); 
const popup = document.getElementById("popup");
const saveBtn = document.getElementById("savePromoCodeBtn");
const cancelBtn = document.getElementById("cancelBtn");

addPromoCodeBtn.addEventListener("click", addPromoCode);
bottomAddPromoCodeBtn.addEventListener("click", addPromoCode);

/**
 * Opens the add-promo-code popup with every field cleared. Save treats
 * this as an insert because promoID is empty.
 */
function addPromoCode() {
    const promoCodeIDInput = document.getElementById("promoID");
    const promoCodeInput = document.getElementById("promoCode");
    const discountTypeInput = document.getElementById("discountType");
    const discountValueInput = document.getElementById("discountValue");
    const statusInput = document.getElementById("active");
    const expiryDateInput = document.getElementById("expiryDate");
    const requiredProductIDsInput = document.getElementById("requiredProductIDs");
    promoCodeIDInput.value = "";
    promoCodeInput.value = "";
    discountTypeInput.value = "";
    discountValueInput.value = "";
    statusInput.value = "";
    expiryDateInput.value = "";
    requiredProductIDsInput.value = "";
    popup.style.visibility = "visible";
}

saveBtn.addEventListener("click", function() {
    const promoCodeIDInput = document.getElementById("promoID");
    const promoCodeInput = document.getElementById("promoCode");
    const discountTypeInput = document.getElementById("discountType");
    const discountValueInput = document.getElementById("discountValue");
    const statusInput = document.getElementById("active");
    const expiryDateInput = document.getElementById("expiryDate");
    const requiredProductIDsInput = document.getElementById("requiredProductIDs");
    let promoCode = {
        promoID: promoCodeIDInput.value,
        promoCode: promoCodeInput.value,
        discountType: discountTypeInput.value,
        discountValue: discountValueInput.value,
        active: statusInput.value,
        expiryDate: expiryDateInput.value,
        requiredProductIDs: requiredProductIDsInput.value,
    };
    savePromoCode(promoCode);
    popup.style.visibility = "hidden";
});

cancelBtn.addEventListener("click", function() {
    popup.style.visibility = "hidden";
});

/**
 * POSTs the add/edit-promo-code form to the server. When promoID is
 * set the server updates the row, otherwise inserts a new one. The
 * refreshed promo code list is rendered back into the table.
 *
 * @param {Object} promoCode the fields to save (promoID, promoCode, discountType, discountValue, active, expiryDate, requiredProductIDs)
 */
function savePromoCode(promoCode) {
    if (!promoCode) {
        return;
    }
    let urlEncodedPromoCode = "";
    urlEncodedPromoCode += "promoID=" + encodeURIComponent(promoCode.promoID) + "&";
    urlEncodedPromoCode += "promoCode=" + encodeURIComponent(promoCode.promoCode) + "&";
    urlEncodedPromoCode += "discountType=" + encodeURIComponent(promoCode.discountType) + "&";
    urlEncodedPromoCode += "discountValue=" + encodeURIComponent(promoCode.discountValue) + "&";
    urlEncodedPromoCode += "active=" + encodeURIComponent(promoCode.active) + "&";
    urlEncodedPromoCode += "expiryDate=" + encodeURIComponent(promoCode.expiryDate) + "&";
    urlEncodedPromoCode += "requiredProductIDs=" + encodeURIComponent(promoCode.requiredProductIDs || "");
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

/**
 * Confirms with the admin and, if accepted, deletes the given promo
 * code on the server and refreshes the table.
 *
 * @param {Object} promocode the promo code row being removed (uses promoID and promoCode)
 */
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

/**
 * Opens the popup pre-filled with the given promo code's values so
 * the admin can edit it. The expiry date control is populated via
 * valueAsDate so it binds correctly to the <input type="date">.
 *
 * @param {Object} promocode the promo code row being edited
 */
function editPromoCode(promocode) {
    const promoCodeIDInput = document.getElementById("promoID");
    const promoCodeInput = document.getElementById("promoCode");
    const discountTypeInput = document.getElementById("discountType");
    const discountValueInput = document.getElementById("discountValue");
    const statusInput = document.getElementById("active");
    const expiryDateInput = document.getElementById("expiryDate");
    const requiredProductIDsInput = document.getElementById("requiredProductIDs");
    promoCodeIDInput.value = promocode.promoID;
    promoCodeInput.value = promocode.promoCode;
    discountTypeInput.value = promocode.discountType;
    discountValueInput.value = promocode.discountValue;
    statusInput.value = promocode.active;
    if (promocode.expiryDate) {
        expiryDateInput.valueAsDate = new Date(promocode.expiryDate);
    } else {
        expiryDateInput.value = "";
    }
    requiredProductIDsInput.value = promocode.requiredProductIDs || "";
    popup.style.visibility = "visible";    
}