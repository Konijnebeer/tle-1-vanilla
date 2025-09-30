window.addEventListener("load", init)

import {ajaxRequestGET} from './utils/fetch.js';
import {requireLogin} from './utils/acount.js';

function init() {
    requireLogin()

    ajaxRequestGET('api/messages.php', messagesSuccess)

}

function messagesSuccess(data) {

}