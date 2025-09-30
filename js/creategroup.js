import { ajaxRequestPUT } from './utils/fetch.js'

window.addEventListener('load', init);

function init() {
    const form = document.getElementById('groupForm');
    form.addEventListener('submit', handleFormSubmit);
}

function handleFormSubmit(event) {
    event.preventDefault(); // Prevent default form submission

    // Clear previous errors
    clearErrors();

    // Get form data
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    // console.log('Sending data:', data); // Debug log

    // Send AJAX request using PUT
    ajaxRequestPUT(
        './api/groups.php',
        handleSuccess,
        data,
        handleError
    );
}

function handleError(error) {
    // console.error('Account creation failed:', error);
    // console.log('Error object:', error);
    // console.log('Error responseData:', error.responseData);

    // Check if the error has responseData (validation errors)
    if (error.responseData && error.responseData.error && error.responseData.error.details) {
        displayErrors(error.responseData.error.details);
    } else if (error.responseData && error.responseData.errors) {
        // Alternative format - direct errors object
        displayErrors(error.responseData.errors);
    } else if (error.status === 400) {
        alert('Validation error occurred but details were not received properly');
    } else {
        alert('Er is een fout opgetreden: ' + error.message);
    }
}

function handleSuccess(response) {
    console.log('Groep succesvol aangemaakt', response);
    // Redirect to home page on success
    window.location.href = './home.html';
}

function clearErrors() {
    // Clear all error messages
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.textContent = '';
        element.style.display = 'none';
    });
}