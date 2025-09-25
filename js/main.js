window.addEventListener('load', init);

function init() {
    checkAuthentication();
}

function checkAuthentication() {
    ajaxRequest('api/check-auth.php', handleAuthResponse);
}

function handleAuthResponse(data) {
    if (data.authenticated) {
        // User is logged in, redirect to home
        window.location.href = 'start.html';
    } else {
        // User is not logged in, redirect to login
        window.location.href = 'api/login.php';
    }
}

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
    // On error, redirect to login as fallback
    window.location.href = 'api/login.php';
}