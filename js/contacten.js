window.addEventListener("load", init)

import {ajaxRequestGET} from './utils/fetch.js';
import {requireLogin} from './utils/acount.js';

function init() {
    requireLogin()

    ajaxRequestGET('api/contacten.php', contactenSuccess)

}

function contactenSuccess(data) {
    const main = document.querySelector('main')
    main.innerHTML = ''

    data.forEach(contact => {
        const card = document.createElement('div');
        card.className = 'contact-card bg-white rounded-lg shadow-md p-6 mb-4 flex flex-col items-start';
        card.innerHTML = `
            <h2 class="text-xl font-bold mb-2">${contact.username}</h2>
            <p class="mb-1"><span class="font-semibold">Email:</span> ${contact.email}</p>
            <p><span class="font-semibold">Telefoon:</span> ${contact.phone_number}</p>
        `;
        main.appendChild(card);
    });
    if (data.length === 0) {
        main.innerHTML = '<p>Geen contacten gevonden.</p>'
    }
}