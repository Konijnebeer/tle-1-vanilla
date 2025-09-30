/**
 * AJAX Request Utilities
 * Easy-to-use functions for making HTTP requests with proper error handling
 */

/**
 * Make a GET request
 * @param {string} url The URL to request
 * @param {function} successCallback Function to call on success
 * @param {function} errorCallback Optional custom error handler
 */
function ajaxRequestGET(url, successCallback, errorCallback = null) {
    fetch(url)
        .then((response) => {
            if (!response.ok) {
                throw new Error(
                    `HTTP ${response.status}: ${response.statusText}`,
                );
            }
            return response.json();
        })
        .then((data) => {
            if (data.success === false) {
                throw new Error(data.error?.message || "Request failed");
            }
            successCallback(data.data || data);
        })
        .catch((error) => {
            if (errorCallback) {
                errorCallback(error);
            } else {
                ajaxRequestErrorHandler(error);
            }
        });
}

/**
 * Make a POST request
 * @param {string} url The URL to request
 * @param {function} successCallback Function to call on success
 * @param {object|FormData} data Data to send in the request body
 * @param {function} errorCallback Optional custom error handler
 */
function ajaxRequestPOST(
    url,
    successCallback,
    data = {},
    errorCallback = null,
) {
    const options = {
        method: "POST",
        headers: {},
    };

    // Handle different data types
    if (data instanceof FormData) {
        // Let browser set Content-Type for FormData (includes boundary)
        options.body = data;
    } else {
        // JSON data
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(data);
    }

    fetch(url, options)
        .then((response) => {
            if (!response.ok) {
                throw new Error(
                    `HTTP ${response.status}: ${response.statusText}`,
                );
            }
            return response.json();
        })
        .then((data) => {
            if (data.success === false) {
                throw new Error(data.error?.message || "Request failed");
            }
            successCallback(data.data || data);
        })
        .catch((error) => {
            if (errorCallback) {
                errorCallback(error);
            } else {
                ajaxRequestErrorHandler(error);
            }
        });
}

/**
 * Make a PUT request
 * @param {string} url The URL to request
 * @param {function} successCallback Function to call on success
 * @param {object} data Data to send in the request body
 * @param {function} errorCallback Optional custom error handler
 */
function ajaxRequestPUT(url, successCallback, data = {}, errorCallback = null) {
    const options = {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    };

    fetch(url, options)
        .then(async (response) => {
            const jsonData = await response.json(); // Get JSON regardless of status
            
            if (!response.ok) {
                // Attach response data to error
                const error = new Error(`HTTP ${response.status}: ${response.statusText}`);
                error.status = response.status;
                error.responseData = jsonData;
                throw error;
            }
            
            return jsonData;
        })
        .then((data) => {
            if (data.success === false) {
                throw new Error(data.error?.message || "Request failed");
            }
            successCallback(data.data || data);
        })
        .catch((error) => {
            if (errorCallback) {
                errorCallback(error);
            } else {
                ajaxRequestErrorHandler(error);
            }
        });
}

/**
 * Make a DELETE request
 * @param {string} url The URL to request
 * @param {function} successCallback Function to call on success
 * @param {function} errorCallback Optional custom error handler
 */
function ajaxRequestDELETE(url, successCallback, errorCallback = null) {
    fetch(url, {
        method: "DELETE",
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(
                    `HTTP ${response.status}: ${response.statusText}`,
                );
            }
            // Handle 204 No Content responses
            if (response.status === 204) {
                return {};
            }
            return response.json();
        })
        .then((data) => {
            if (data.success === false) {
                throw new Error(data.error?.message || "Request failed");
            }
            successCallback(data.data || data);
        })
        .catch((error) => {
            if (errorCallback) {
                errorCallback(error);
            } else {
                ajaxRequestErrorHandler(error);
            }
        });
}

/**
 * Default error handler for AJAX requests
 * @param {Error} error The error that occurred
 */
function ajaxRequestErrorHandler(error) {
    console.error("AJAX Error:", error);

    // Check if it's an authentication error
    if (
        error.message.includes("401") ||
        error.message.includes("Authentication")
    ) {
        // Redirect to login page
        window.location.href = "../start2.html";
        return;
    }

    // Show user-friendly error message
    const errorMessage = error.message || "An unexpected error occurred";
    alert("Error: " + errorMessage);
}

/**
 * Show loading indicator (you can customize this)
 * @param {boolean} show Whether to show or hide loading
 */
function showLoading(show = true) {
    const loadingElement = document.getElementById("loading");
    if (loadingElement) {
        loadingElement.style.display = show ? "block" : "none";
    }
}

export {
    ajaxRequestDELETE,
    ajaxRequestErrorHandler,
    ajaxRequestGET,
    ajaxRequestPOST,
    ajaxRequestPUT,
    showLoading,
};
