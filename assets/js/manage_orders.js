let allOrders = [];

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

    name = document.createElement("th");
    name.innerText = "Action";
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

        cell = document.createElement("td");
        let editBtn = document.createElement("button");
        editBtn.innerText = "Change Status";
        editBtn.className = "secondary-button";
        editBtn.style.marginBottom = "5px";
        editBtn.style.marginRight = "5px";
        editBtn.addEventListener("click", () => editOrderStatus(order));
        cell.appendChild(editBtn);
        orderRow.appendChild(cell);

        table.appendChild(orderRow);
    }

    orderTable.appendChild(table);
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

window.addEventListener("load", function () {
    console.log("get orders");
    getAllOrders();
});
 
const popup = document.getElementById("popup");
const saveBtn = document.getElementById("saveOrderBtn");
const cancelBtn = document.getElementById("cancelBtn");

function editOrderStatus(order) {
    const orderIDInput = document.getElementById("orderID");
    const orderStatusInput = document.getElementById("fullfilled");
    orderIDInput.value = order.orderID;
    orderStatusInput.value = order.fullfilled;
    popup.style.visibility = "visible";  
}

saveBtn.addEventListener("click", function() {
    const orderIDInput = document.getElementById("orderID");
    const orderStatusInput = document.getElementById("fullfilled");
    let order = { orderID: orderIDInput.value, fullfilled: orderStatusInput.value };
    saveOrder(order);
    popup.style.visibility = "hidden";
});

function saveOrder(order) {
    if (!order) {
        return;
    }
    let urlEncodedProduct = "";
    urlEncodedProduct += "orderID=" + order.orderID + "&";
    urlEncodedProduct += "fullfilled=" + order.fullfilled + "";
    fetch("../assets/php/admin.php?editOrderStatus", {
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
        allOrders = data;
        renderOrderTable(allOrders);
    });

}

cancelBtn.addEventListener("click", function() {
    popup.style.visibility = "hidden";
});