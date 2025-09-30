import { requireLogin } from "./utils/acount.js"
import { ajaxRequestGET } from './utils/fetch.js'
window.addEventListener("load", init);

const url = "./api/profiel.php";

function init() {
    // check if the user is logged in
    requireLogin()
    // fetch user info
    ajaxRequestGET(url, profileSuccessHandler, ajaxRequestErrorHandler)
}
function profileSuccessHandler(data) {
    // console.log(data);
    const container = document.getElementById("profiel");
    if (data.error) {
        container.innerHTML =
            `<p class="text-red-600 font-semibold">${data.error}</p>`;
    } else {
        container.innerHTML = `
            <div class="space-y-3">
                <p><strong class="text-background py-2">Gebruikersnaam:</strong> <span class="text-background">${data.username}</span></p>
                <p><strong class="text-background py-2">Email:</strong> <span class="text-background">${data.email}</span></p>
                <p><strong class="text-background py-2">Telefoonnummer:</strong> <span class="text-background">${data.phone_number}</span></p>
                <p><strong class="text-background py-2">Aangemaakt op:</strong> <span class="text-background">${data.created_at}</span></p>
                <p><strong class="text-background py-2">Bijgewerkt op:</strong> <span class="text-background">${data.updated_at}</span></p>
            </div>
        `;
    }
}

function ajaxRequestErrorHandler(error) {
    console.error("Error:", error);
    document.getElementById("profiel").innerHTML =
        `<p class="text-red-600 font-semibold">Fout bij laden van profiel: ${error}</p>`;
    alert("An error occurred while fetching data. Please try again later.");
}
