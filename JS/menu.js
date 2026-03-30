window.addEventListener("load", function() {

    const menu = document.getElementById("menu");

    function handleMenu(data) {

        menu.innerHTML = "";

        for (const product of data) {
            const item = document.createElement("div");
            item.className = "menuitem";

            const img = document.createElement("img");
            img.src = "../assets/images/" + product.productimg;

            const br = document.createElement("br");

            const desc = document.createElement("div");
            desc.className = "desc";

            const name = document.createElement("h2");
            name.textContent = product.productname;

            const price = document.createElement("h3");
            price.textContent = "$" + product.price;

            const itemdesc = document.createElement("p");
            itemdesc.textContent = product.productdesc;

            desc.append(name, price, itemdesc);
            item.append(img, br, desc); 
            menu.appendChild(item);
        }

    }

    fetch("../assets/php/menu.php")
        .then(function (response) {
            if (!response.ok) {
                throw new Error("Server returned " + response.status);
            }
            return response.json()
        })
        .then(handleMenu);
        


})