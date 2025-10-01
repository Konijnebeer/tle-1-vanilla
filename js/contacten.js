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
            </div>
    
      
        `;

        const div = document.createElement('div');
        div.className = 'buttons';
        card.appendChild(div);

        const link = document.createElement('a');
        link.href = `messages.html?user=${contact.id}`;
        link.className = 'mt-4 inline-block';

        const phoneIcon = document.createElement('img');
        phoneIcon.src = 'icons/phone-icon.png';
        phoneIcon.alt = 'Telefoon';
        phoneIcon.className = 'w-8 h-8';
        div.appendChild(phoneIcon);

        const chatIcon = document.createElement('img');
        chatIcon.src = 'icons/chat-icon.png';
        chatIcon.alt = 'Berichten';
        chatIcon.className = 'ml-4 w-8 h-8';
        link.appendChild(chatIcon);

        div.appendChild(link);

        main.appendChild(card);

    });
    if (data.length === 0) {
        main.innerHTML = '<p>Geen contacten gevonden.</p>'
    }
}