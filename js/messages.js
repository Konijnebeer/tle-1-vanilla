window.addEventListener("load", init)

import {ajaxRequestGET} from './utils/fetch.js';
import {requireLogin} from './utils/acount.js';

function init() {
    requireLogin()
    const params = new URLSearchParams(window.location.search);
    const userId = params.get('user'); // userId will be the value of ?user=...
    const url = `api/messages.php?user=${userId}`;

    ajaxRequestGET(url, messagesSuccess);

}

function messagesSuccess(data) {
    const main = document.querySelector('main')
    main.innerHTML = ''
    data.forEach(message => {
        const card = document.createElement('div');
        card.className = 'message-card bg-white rounded-lg shadow-md p-6 mb-4 flex flex-col items-start';
        card.innerHTML = `
            <h2 class="text-xl font-bold mb-2">Van: ${message.sender_name}</h2>
            <p class="mb-1"><span class="font-semibold">Bericht:</span> ${message.message_text}</p>
            <p class="text-sm text-gray-500 mt-2">${new Date(message.sent_at).toLocaleString()}</p>
        `;
        main.appendChild(card);
    });
    if (data.length === 0) {
        main.innerHTML = '<p>Geen berichten gevonden.</p>'
    }
}