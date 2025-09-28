import { requireLogin } from "./utils/acount.js"
window.addEventListener('load', init)

function init() {
    requireLogin(redirect)
}

// Redirect to home page when logged in
function redirect(data) {
    window.location.href = './home.html';
}


