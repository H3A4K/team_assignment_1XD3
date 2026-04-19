let allPromotions = [];

function renderPromotionTable(data) {
    const promotionTable = document.getElementById("promotion-table");
    if (!promotionTable) return;

    promotionTable.innerHTML = "";

    if (!data || data.length === 0) {
        const empty = document.createElement("p");
        empty.id = "promotions-empty";
        empty.textContent = "No promotions found. Click 'Add Promotion' to create one.";
        promotionTable.appendChild(empty);
        return;
    }

    const table = document.createElement("table");
    table.className = "table";

    const headers = ["ID", "Title", "Eyebrow", "Price", "Badge", "Image", "Theme", "Active", "Order", "Action"];
    const headerRow = document.createElement("tr");
    headers.forEach(h => {
        const th = document.createElement("th");
        th.innerText = h;
        headerRow.appendChild(th);
    });
    table.appendChild(headerRow);

    for (const promo of data) {
        const row = document.createElement("tr");

        let cell = document.createElement("td");
        cell.setAttribute("data-label", "ID");
        cell.innerText = promo.promotionID;
        row.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Title");
        cell.innerText = promo.title || "";
        row.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Eyebrow");
        cell.innerText = promo.eyebrow || "";
        row.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Price");
        cell.innerText = promo.price || "";
        row.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Badge");
        cell.innerText = promo.badge || "";
        row.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Image");
        const img = document.createElement("img");
        img.src = "../assets/images/menu/" + (promo.image || "placeholder.jpg");
        img.className = "table-img";
        cell.appendChild(img);
        row.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Theme");
        cell.innerText = promo.theme || "orange";
        row.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Active");
        cell.innerText = parseInt(promo.active) === 1 ? "Yes" : "No";
        row.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Order");
        cell.innerText = promo.sortOrder || 0;
        row.appendChild(cell);

        cell = document.createElement("td");
        cell.setAttribute("data-label", "Action");
        cell.style.display = "flex";
        cell.style.flexDirection = "column";
        cell.style.alignItems = "stretch";
        cell.style.gap = "5px";
        const editBtn = document.createElement("button");
        const removeBtn = document.createElement("button");
        editBtn.innerText = "Edit";
        removeBtn.innerText = "Remove";
        editBtn.className = "secondary-button";
        removeBtn.className = "secondary-button";
        editBtn.addEventListener("click", () => editPromotion(promo));
        removeBtn.addEventListener("click", () => removePromotion(promo));
        cell.appendChild(editBtn);
        cell.appendChild(removeBtn);
        row.appendChild(cell);

        table.appendChild(row);
    }

    promotionTable.appendChild(table);
}

function getAllPromotions() {
    fetch("../assets/php/admin.php?getAllPromotions")
        .then(r => {
            if (!r.ok) throw new Error("Server returned " + r.status);
            return r.json();
        })
        .then(data => {
            allPromotions = data;
            renderPromotionTable(allPromotions);
        });
}

window.addEventListener("load", getAllPromotions);

const popup = document.getElementById("popup");
const addBtn = document.getElementById("addPromotionBtn");
const bottomAddBtn = document.getElementById("bottomAddPromotionBtn");
const saveBtn = document.getElementById("savePromotionBtn");
const cancelBtn = document.getElementById("cancelBtn");

addBtn.addEventListener("click", addPromotion);
bottomAddBtn.addEventListener("click", addPromotion);

function addPromotion() {
    document.getElementById("promotionID").value = "";
    document.getElementById("title").value = "";
    document.getElementById("eyebrow").value = "";
    document.getElementById("price").value = "";
    document.getElementById("badge").value = "";
    document.getElementById("description").value = "";
    document.getElementById("finePrint").value = "";
    document.getElementById("theme").value = "orange";
    document.getElementById("ctaLabel").value = "Order Now";
    document.getElementById("active").value = "1";
    document.getElementById("sortOrder").value = "0";
    document.getElementById("promoCode").value = "";
    document.getElementById("currentPromotionImg").value = "";
    document.getElementById("promotionImgFile").value = "";
    document.getElementById("promotionImgPreview").src = "../assets/images/menu/placeholder.jpg";
    popup.style.visibility = "visible";
}

function editPromotion(promo) {
    document.getElementById("promotionID").value = promo.promotionID;
    document.getElementById("title").value = promo.title || "";
    document.getElementById("eyebrow").value = promo.eyebrow || "";
    document.getElementById("price").value = promo.price || "";
    document.getElementById("badge").value = promo.badge || "";
    document.getElementById("description").value = promo.description || "";
    document.getElementById("finePrint").value = promo.finePrint || "";
    document.getElementById("theme").value = promo.theme || "orange";
    document.getElementById("ctaLabel").value = promo.ctaLabel || "Order Now";
    document.getElementById("active").value = String(parseInt(promo.active) === 1 ? 1 : 0);
    document.getElementById("sortOrder").value = promo.sortOrder || 0;
    document.getElementById("promoCode").value = promo.promoCode || "";
    document.getElementById("currentPromotionImg").value = promo.image || "";
    document.getElementById("promotionImgFile").value = "";
    document.getElementById("promotionImgPreview").src = "../assets/images/menu/" + (promo.image || "placeholder.jpg");
    popup.style.visibility = "visible";
}

saveBtn.addEventListener("click", function () {
    const formData = new FormData();
    formData.append("promotionID", document.getElementById("promotionID").value);
    formData.append("title", document.getElementById("title").value);
    formData.append("eyebrow", document.getElementById("eyebrow").value);
    formData.append("price", document.getElementById("price").value);
    formData.append("badge", document.getElementById("badge").value);
    formData.append("description", document.getElementById("description").value);
    formData.append("finePrint", document.getElementById("finePrint").value);
    formData.append("theme", document.getElementById("theme").value);
    formData.append("ctaLabel", document.getElementById("ctaLabel").value);
    formData.append("active", document.getElementById("active").value);
    formData.append("sortOrder", document.getElementById("sortOrder").value);
    formData.append("promoCode", document.getElementById("promoCode").value);
    formData.append("currentPromotionImg", document.getElementById("currentPromotionImg").value);

    const fileInput = document.getElementById("promotionImgFile");
    if (fileInput.files && fileInput.files[0]) {
        formData.append("promotionImgFile", fileInput.files[0]);
    }

    savePromotion(formData);
    popup.style.visibility = "hidden";
});

cancelBtn.addEventListener("click", function () {
    popup.style.visibility = "hidden";
});

// Live preview when admin picks a new banner image file
const promotionImgFileInput = document.getElementById("promotionImgFile");
if (promotionImgFileInput) {
    promotionImgFileInput.addEventListener("change", function (e) {
        const file = e.target.files && e.target.files[0];
        const preview = document.getElementById("promotionImgPreview");
        if (!file || !preview) return;
        const reader = new FileReader();
        reader.onload = function (evt) {
            preview.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function savePromotion(formData) {
    if (!formData) return;
    fetch("../assets/php/admin.php?savePromotion", {
        method: "POST",
        body: formData
    })
        .then(r => {
            if (!r.ok) throw new Error("Server returned " + r.status);
            return r.json();
        })
        .then(data => {
            allPromotions = data;
            renderPromotionTable(allPromotions);
        });
}

function removePromotion(promo) {
    const confirmed = confirm('Are you sure you want to remove promotion "' + promo.title + '"?');
    if (!confirmed) return;

    const body = "promotionID=" + encodeURIComponent(promo.promotionID);
    fetch("../assets/php/admin.php?removePromotion", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body
    })
        .then(r => {
            if (!r.ok) throw new Error("Server returned " + r.status);
            return r.json();
        })
        .then(data => {
            allPromotions = data;
            renderPromotionTable(allPromotions);
        });
}
