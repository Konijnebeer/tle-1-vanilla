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
        const div = document.createElement('div')
        div.classList.add('contact');
        div.innerHTML = `
            <h2>${contact.naam}</h2>
            <p>Email: ${contact.email}</p>
            <p>Telefoon: ${contact.telefoon}</p>
        `;
        main.appendChild(div)
    });
    if (data.length === 0) {
        main.innerHTML = '<p>Geen contacten gevonden.</p>'
    }
}