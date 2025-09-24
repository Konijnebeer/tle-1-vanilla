window.addEventListener('load', init)

const url = 'api/profiel.php'

function init() {
    ajaxRequest(url, profileSuccessHandler)
}
function profileSuccessHandler(data) {
    console.log(data)
    const container = document.getElementById('profiel');
            if (data.error) {
            container.innerHTML = `<p>${data.error}</p>`;
        } else {
            container.innerHTML = `
                <p><strong>Gebruikersnaam:</strong> ${data.username}</p>
                <p><strong>Email:</strong> ${data.email}</p>
                <p><strong>Telefoonnummer:</strong> ${data.phone_number}</p>
                <p><strong>Aangemaakt op:</strong> ${data.created_at}</p>
                <p><strong>Bijgewerkt op:</strong> ${data.updated_at}</p>
            `;
        }
}

// fetch('api/profiel.php')
//     .then(response => response.json())
//     .then(data => {
//         const container = document.getElementById('profiel');
        
//         if (data.error) {
//             container.innerHTML = `<p>${data.error}</p>`;
//         } else {
//             container.innerHTML = `
//                 <p><strong>Gebruikersnaam:</strong> ${data.username}</p>
//                 <p><strong>Email:</strong> ${data.email}</p>
//                 <p><strong>Telefoonnummer:</strong> ${data.phone_number}</p>
//                 <p><strong>Aangemaakt op:</strong> ${data.created_at}</p>
//                 <p><strong>Bijgewerkt op:</strong> ${data.updated_at}</p>
//                 <p><a href="logout.php">Uitloggen</a></p>
//             `;
//         }
//     })
//     .catch(error => {
//         document.getElementById('profiel').innerHTML = `<p>Fout bij laden van profiel: ${error}</p>`;
//     });
    

function ajaxRequest(url, successCallback) {
    fetch(url)
        .then((response) => {
            if (!response.ok) {
                throw new Error(response.statusText);
            }
            return response.json();
        })
        .then((data) => successCallback(data))
        .catch((error) => ajaxRequestErrorHandler(error));
}

function ajaxRequestErrorHandler(error) {
    console.error("Error:", error);
    document.getElementById('profiel').innerHTML = `<p>Fout bij laden van profiel: ${error}</p>`;
    alert("An error occurred while fetching data. Please try again later.");
}