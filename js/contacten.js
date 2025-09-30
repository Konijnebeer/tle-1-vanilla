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
        card.className = 'contact';
        card.innerHTML = `
            <div>
                <h2 class="text-xl font-bold mb-2">@${contact.username}</h2>
                <p class="mb-1"><span class="font-semibold">Email:</span> ${contact.email}</p>
                <p><span class="font-semibold">Telefoon:</span> ${contact.phone_number}</p>
                <a href="messages.html?user=${contact.id}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Stuur bericht</a>
            </div>
    
      
        `;

        const div = document.createElement('div');
        div.className = 'buttons';
        card.appendChild(div);

        const phoneIcon = document.createElement('img');
        phoneIcon.src = 'icons/phone-icon.png';
        phoneIcon.alt = 'Telefoon';
        div.appendChild(phoneIcon);

        const chatIcon = document.createElement('img');
        chatIcon.src = 'icons/chat-icon.png';
        chatIcon.alt = 'Berichten';
        div.appendChild(chatIcon);

        main.appendChild(card);

    });
    if (data.length === 0) {
        main.innerHTML = '<p>Geen contacten gevonden.</p>'
    }
}