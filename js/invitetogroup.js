import { ajaxRequestPOST, ajaxRequestGET } from './utils/fetch.js';
import { requireLogin } from './utils/acount.js';

let groupId;

window.addEventListener('load', function() {
    requireLogin();

    // Get group ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    groupId = urlParams.get('id');

    if (!groupId) {
        alert('Geen groep geselecteerd');
        window.location.href = 'viewgroups.html';
        return;
    }

    setupForm();
});

function setupForm() {
    const form = document.getElementById('inviteForm');
    const messageDiv = document.getElementById('message');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();

        if (!email) {
            messageDiv.innerHTML = '<p class="text-red-600">Voer een e-mailadres in</p>';
            return;
        }

        // Show loading
        messageDiv.innerHTML = '<p>Bezig met toevoegen...</p>';

        // Add user to group
        ajaxRequestPOST('./api/invitetogroup.php',
            function(response) {
                messageDiv.innerHTML = `<p class="text-green-600">${response.message}</p>`;
                form.reset();
            },
            {
                group_id: groupId,
                email: email
            },
            function(error) {
                messageDiv.innerHTML = `<p class="text-red-600">Fout: ${error.message}</p>`;
            }
        );
    });
}