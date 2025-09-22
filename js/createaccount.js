window.addEventListener('load', init);

let createAccount;

function init() {
    createAccount = document.querySelector('#create-account');
    const form = createAccountForm();
    fillForm(form);
}

function createAccountForm() {
    const accountForm = document.createElement('form');
    accountForm.classList.add('form-container');
    accountForm.setAttribute('method', 'post');
    accountForm.setAttribute('action', '#');
    createAccount.appendChild(accountForm);
    return accountForm;
}

function fillForm(accountForm) {
    // Helper to create label + input
    function addField(labelText, type, name) {
        const label = document.createElement('label');
        label.innerText = labelText;
        label.setAttribute('for', name);

        const input = document.createElement('input');
        input.setAttribute('type', type);
        input.setAttribute('name', name);
        input.setAttribute('id', name);
        input.required = true;

        accountForm.appendChild(label);
        accountForm.appendChild(document.createElement('br'));
        accountForm.appendChild(input);
        accountForm.appendChild(document.createElement('br'));
    }

    // Add fields
    addField('Email:', 'email', 'email');
    addField('Username:', 'text', 'username');
    addField('Phone Number:', 'tel', 'phone');
    addField('Password:', 'password', 'password');
    addField('Confirm Password:', 'password', 'confirm_password');


    // Submit button
    const submit = document.createElement('button');
    submit.setAttribute('type', 'submit');
    submit.innerText = 'Create Account';
    accountForm.appendChild(document.createElement('br'));
    accountForm.appendChild(submit);
}
