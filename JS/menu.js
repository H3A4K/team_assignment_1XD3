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

            const h2 = document.createElement("h2");
            h2.textContent = product.productname;

            const h3 = document.createElement("h3");
            h3.textContent = "$" + product.price;

            const p = document.createElement("p");
            p.textContent = product.productdesc;

            desc.append(h2, h3, p);
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