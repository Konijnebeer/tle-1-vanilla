import { requireLogin } from "./utils/acount.js"
window.addEventListener('load', init)

function init() {
    requireLogin()
}

