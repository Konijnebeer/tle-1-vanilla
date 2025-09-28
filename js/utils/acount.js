/**
 * Client-side Account Authentication Utilities
 * Simple functions to check if user is logged in and handle authentication
 */

import { ajaxRequestGET } from "./fetch.js";

/**
 * Check if user is logged in and redirect to start.html if not
 * Call this on protected pages
 * @param {function} onSuccess Optional callback when user is authenticated
 * @param {string} redirectUrl URL to redirect to if not logged in (default: '../start.html')
 */
function requireLogin(onSuccess = null, redirectUrl = "../start.html") {
    checkAuthStatus(
        (userData) => {
            // User is logged in
            console.log("User authenticated:", userData.user.username);
            if (onSuccess) {
                onSuccess(userData);
            }
        },
        () => {
            // User is not logged in - redirect
            console.log("User not authenticated, redirecting to login");
            window.location.href = redirectUrl;
        },
    );
}

/**
 * Check authentication status without redirecting
 * @param {function} onLoggedIn Callback when user is logged in
 * @param {function} onLoggedOut Callback when user is not logged in
 */
function checkAuthStatus(onLoggedIn, onLoggedOut) {
    ajaxRequestGET(
        "../api/utils/acount.php?type=user",
        (data) => {
            // Success - user is logged in
            if (onLoggedIn) {
                onLoggedIn(data);
            }
        },
        (error) => {
            // Error - likely not logged in (401 status)
            console.log("Authentication check failed:", error.message);
            if (onLoggedOut) {
                onLoggedOut(error);
            }
        },
    );
}

/**
 * Get current user data
 * @param {function} callback Function to call with user data
 */
function getCurrentUser(callback) {
    ajaxRequestGET(
        "../api/utils/acount.php?type=user",
        (data) => {
            callback(data.user);
        },
        (error) => {
            console.error("Failed to get user data:", error);
            callback(null);
        },
    );
}

/**
 * Logout user and redirect to start page
 * @param {string} redirectUrl URL to redirect to after logout (default: '../start.html')
 */
function logout(redirectUrl = "../start.html") {
    ajaxRequestGET(
        "../api/logout.php",
        () => {
            // Successful logout
            console.log("User logged out successfully");
            window.location.href = redirectUrl;
        },
        (error) => {
            // Even if logout fails, redirect anyway
            console.log("Logout error, redirecting anyway:", error);
            window.location.href = redirectUrl;
        },
    );
}

/**
 * Show/hide elements based on authentication status
 * @param {string} loggedInSelector CSS selector for elements to show when logged in
 * @param {string} loggedOutSelector CSS selector for elements to show when logged out
 */
function toggleAuthElements(
    loggedInSelector = ".logged-in",
    loggedOutSelector = ".logged-out",
) {
    checkAuthStatus(
        (userData) => {
            // User is logged in
            const loggedInElements = document.querySelectorAll(
                loggedInSelector,
            );
            const loggedOutElements = document.querySelectorAll(
                loggedOutSelector,
            );

            loggedInElements.forEach((el) => el.style.display = "block");
            loggedOutElements.forEach((el) => el.style.display = "none");
        },
        () => {
            // User is not logged in
            const loggedInElements = document.querySelectorAll(
                loggedInSelector,
            );
            const loggedOutElements = document.querySelectorAll(
                loggedOutSelector,
            );

            loggedInElements.forEach((el) => el.style.display = "none");
            loggedOutElements.forEach((el) => el.style.display = "block");
        },
    );
}

/**
 * Initialize authentication check on page load
 * Automatically check authentication and set up UI
 * @param {boolean} requireAuth Whether to redirect if not logged in
 */
function initAuth(requireAuth = false) {
    if (requireAuth) {
        requireLogin();
    } else {
        toggleAuthElements();
    }
}

export {
    checkAuthStatus,
    getCurrentUser,
    initAuth,
    logout,
    requireLogin,
    toggleAuthElements,
};
