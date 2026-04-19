/**
 * pickup.js
 *
 * Client-side script for the pickup/delivery confirmation page. Calls
 * assets/php/pickup.php to fetch the estimated wait time for the user's
 * current (or specifically requested) order, then fills in the wait
 * time, label, detail sentence, and delivery address on the page. If
 * the server returns an error, a friendly message replaces the time.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 01, 2026
 */

window.addEventListener("load", function () {
    const timeDisplay = document.getElementById("time");
    const timeLabel = document.getElementById("time-label");
    const detail = document.getElementById("pickup-detail");
    const address = document.getElementById("pickup-address");
    const table = document.getElementById("pickup-table");
    const main = document.querySelector("main");

    /**
     * Shows an error message in place of the wait time when the
     * pickup endpoint returns a failure.
     *
     * @param {String} message the user-facing error message to display
     */
    function showError(message) {
        if (timeDisplay) timeDisplay.textContent = "--";
        if (detail) detail.textContent = message;
        if (address) address.textContent = "";
        if (main) main.classList.add("pickup-error");
    }

    fetch("../assets/php/pickup.php" + window.location.search)
        .then((response) => response.json().then((data) => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) {
                throw new Error(data.error || "Unable to load your order wait time.");
            }

            if (timeDisplay) {
                timeDisplay.textContent = data.time;
            }

            const isDelivery = data.fulfillmentMethod === "delivery";
            if (timeLabel) {
                timeLabel.textContent = isDelivery ? "Estimated Delivery Prep Time" : "Estimated Wait Time";
            }
            if (detail) {
                detail.textContent = isDelivery
                    ? "Your delivery order has been placed. This estimate covers kitchen prep before the driver heads out."
                    : "Your pickup order has been placed. Head over once the kitchen has had time to prepare it.";
            }
            if (address) {
                address.textContent = isDelivery && data.address
                    ? "Delivery address: " + data.address
                    : "";
            }
            if (table) {
                table.innerHTML = data.orderTable;
                console.log(data.orderTable);
            }

            // console.log(data.order);
        })
        .catch((error) => {
            showError(error.message);
        });
});
