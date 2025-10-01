import { ajaxRequestGET } from './utils/fetch.js';
import { requireLogin } from './utils/acount.js';

let field;

window.addEventListener('load', function() {
    requireLogin(); // Check authentication first

    // Initialize field element
    field = document.getElementById('field');
    if (!field) {
        console.error('Field element not found');
        return;
    }

    ajaxRequestGET('./api/viewgroups.php?action=getall', success, errorHandler);
});

function success(data) {
    console.log(data);
    for (const groups of data.groups) {
        console.log(groups);
        const div = document.createElement('div')
            div.classList.add('boxNoImage')
            div.id = `${groups.id}`;

        const title = document.createElement('h2');
            title.classList.add('titlebox')
            title.innerHTML = groups.name;
            div.appendChild(title);

        const description = document.createElement('p');
            description.classList.add('textBox')
            description.innerHTML = groups.discription;
            div.appendChild(description);

            const theme = document.createElement('p');
            theme.classList.add('textBox')
            theme.innerHTML = `Thema: ${groups.theme}`;
            div.appendChild(theme);
            field.appendChild(div);

    }
}

function errorHandler(error) {
    console.error("Error loading groups:", error);

    // Clear loading message
    field.innerHTML = '';

    // Check if it's an authentication error
    if (error.message.includes('401') || error.message.includes('Authentication')) {
        window.location.href = 'home.html';
        return;
    }

    field.innerHTML = '<p class="error">Failed to load groups. Please try again later.</p>';
}