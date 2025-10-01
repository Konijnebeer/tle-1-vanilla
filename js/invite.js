window.addEventListener("load", init);
import { requireLogin } from "./utils/acount.js";
import { ajaxRequestGET, ajaxRequestPUT } from "./utils/fetch.js";

const groupsUrl = "./api/getUserGroups.php";
const inviteUrl = "./api/invite.php";
let userGroups = []; // Store user's groups

function init() {
    requireLogin();

    const inviteButton = document.querySelector("#inviteButton");
    inviteButton.addEventListener("click", sendInvite);
    
    loadUserGroups();
}

function loadUserGroups() {
    ajaxRequestGET(groupsUrl, loadGroupsSuccessHandler, loadGroupsErrorHandler);
}

function loadGroupsSuccessHandler(groups) {
    console.log("gebruikers groepen zijn geladen:", groups);
    userGroups = groups;
    
    const groupSelect = document.querySelector("#group");
    groupSelect.innerHTML = '';
    
    if (groups.length === 0) {
        groupSelect.innerHTML = '<option value="">Geen groepen gevonden</option>';
        groupSelect.disabled = true;
    } else {
        // Add default option
        groupSelect.innerHTML = '<option value="">Selecter een groep</option>';
        
        // Add group options
        groups.forEach(group => {
            const option = document.createElement('option');
            option.value = group.id;
            option.textContent = group.name;
            groupSelect.appendChild(option);
        });
        
        // Auto-select first group if only one available
        if (groups.length === 1) {
            groupSelect.value = groups[0].id;
        }
    }
}

function loadGroupsErrorHandler(error) {
    console.error("Laden van groepen mislukt:", error);
    const groupSelect = document.querySelector("#group");
    groupSelect.innerHTML = '<option value="">Kan groepen niet laden</option>';
    groupSelect.disabled = true;
}

function sendInvite() {
    const email = document.querySelector("#email").value.trim();
    const selectedGroupId = document.querySelector("#group").value;

    // Validate email
    if (!email) {
        alert("Geef een email op");
        return;
    }

    // Validate group selection
    if (!selectedGroupId) {
        alert("Selecteer een groep");
        return;
    }

    // Disable the button to prevent double submission
    const inviteButton = document.querySelector("#inviteButton");
    inviteButton.disabled = true;
    inviteButton.textContent = "Bezig met uitnodigen...";

    // Prepare the invite data
    const inviteData = {
        email: email,
        group: parseInt(selectedGroupId)
    };

    console.log("Verstuur uitnodiging met gegevens:", inviteData);

    // Send the invite data using PUT request
    ajaxRequestPUT(inviteUrl, inviteSuccessHandler, inviteData, inviteErrorHandler);
}

function inviteSuccessHandler(data) {
    console.log("Uitnodiging is verstuurd:", data);
    alert("Uitnodiging is succesvol verstuurd");
    // Reset form
    document.querySelector("#email").value = '';
    document.querySelector("#group").value = '';
}

function inviteErrorHandler(error) {
    console.error("Error uitnodiging mislukt:", error);
    
    // Check if this is a validation error with details
    if (error.status === 400 && error.responseData && error.responseData.error && error.responseData.error.details) {
        console.log('Validation errors:', error.responseData.error.details);
        // Handle validation errors
        const details = error.responseData.error.details;
        let errorMessage = "Validation errors:\n";
        for (const field in details) {
            errorMessage += `${field}: ${details[field]}\n`;
        }
        alert(errorMessage);
    } else if (error.status === 401) {
        // Authentication error - redirect to login
        alert("login om uitnodigingen te versturen");
        window.location.href = "start2.html";
    } else if (error.responseData && error.responseData.error) {
        // Server returned structured error
        alert("Error: " + error.responseData.error.message);
    } else {
        // Generic error
        alert("Error versturen mislukt: " + error.message);
    }
    
    // Re-enable the button
    const inviteButton = document.querySelector("#inviteButton");
    inviteButton.disabled = false;
    inviteButton.textContent = "verstuur uitnodiging";
}
