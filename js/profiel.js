async function laadProfiel() {
    try {
        const response = await fetch('api/profiel.php');
        const data = await response.json();

        const container = document.createElement('div');
        container.className = 'profiel-container';

        const title = document.createElement('h1');
        title.textContent = 'Welkom op je profielpagina';
        container.appendChild(title);

        if (data.error) {
            const error = document.createElement('p');
            error.textContent = data.error;
            container.appendChild(error);
        } else {
            const fields = [
                { label: 'Gebruikersnaam', value: data.username },
                { label: 'Email', value: data.email },
                { label: 'Telefoonnummer', value: data.phone_number },
                { label: 'Aangemaakt op', value: data.created_at },
                { label: 'Bijgewerkt op', value: data.updated_at }
            ];

            fields.forEach(field => {
                const p = document.createElement('p');
                p.innerHTML = `<strong>${field.label}:</strong> ${field.value}`;
                container.appendChild(p);
            });

            const logout = document.createElement('a');
            logout.href = 'logout.php';
            logout.textContent = 'Uitloggen';
            container.appendChild(logout);
        }

        document.body.appendChild(container);

        // Voeg CSS toe
        const style = document.createElement('style');
        style.textContent = `
      .profiel-container {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        padding: 20px;
        margin: 50px auto;
        width: 400px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
      }

      .profiel-container p {
        margin: 10px 0;
      }

      .profiel-container a {
        display: inline-block;
        margin-top: 15px;
        color: #007bff;
        text-decoration: none;
      }

      .profiel-container a:hover {
        text-decoration: underline;
      }
    `;
        document.head.appendChild(style);

    } catch (err) {
        console.error('Fout bij laden profiel:', err);
    }
}

laadProfiel();