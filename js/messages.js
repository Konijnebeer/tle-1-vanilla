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
    if (data.length > 0) {
        const currentUser = data[0].sender_id === parseInt(localStorage.getItem('user_id'))
            ? data[0].sender_name
            : data[0].receiver_name;

        const heading = document.createElement('h2');
        heading.className = 'text-2xl font-bold mb-4 flex flex-col items-center mt-8"';
        heading.textContent = `Conversation with: ${currentUser}`;
        main.appendChild(heading);
    }
    data.forEach(message => {
        const card = document.createElement('div');
        card.className = 'contact bg-white rounded-lg shadow-md p-8 mb-6 flex flex-col items-start text-lg sm:text-xl';
        card.innerHTML = `
    <h2 class="text-2xl font-bold mb-4">Van: ${message.sender_name}</h2>
    <p class="mb-2"><span class="font-semibold">Bericht:</span> ${message.message_text}</p>
    <p class="text-base text-gray-500 mt-4">${new Date(message.sent_at).toLocaleString()}</p>
`;
        main.appendChild(card);
    });
    if (data.length === 0) {
        main.className = 'text-2xl font-bold mb-4 flex flex-col items-center mt-8"';
        main.innerHTML = '<p>Geen berichten gevonden.</p>'
    }
}