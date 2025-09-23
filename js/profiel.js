fetch('profiel.php')
    .then(response => response.json())
    .then(data => {
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
                <p><a href="logout.php">Uitloggen</a></p>
            `;
        }
    })
    .catch(error => {
        document.getElementById('profiel').innerHTML = `<p>Fout bij laden van profiel: ${error}</p>`;
    });