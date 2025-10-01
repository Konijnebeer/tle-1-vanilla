import { ajaxRequestGET } from "./utils/fetch.js";
import { requireLogin } from "./utils/acount.js";

let field;

window.addEventListener("load", function () {
    // Initialize field element
    field = document.getElementById("field");
    if (!field) {
        console.error("Field element not found");
        return;
    }
    if (new URLSearchParams(window.location.search).has("code")) {
        // Loading invite posts

        const url = new URL(window.location.href);
        const code = url.searchParams.get("code");
        console.log(`Invite code found: ${code}`);
        // Load invite with code parameter
        ajaxRequestGET(`api/invite.php?code=${code}`, success, errorHandler);
    } else {
        // Loading normal posts.

        requireLogin(); // Check authentication first
        // Load posts with action parameter
        ajaxRequestGET("api/posts.php?action=getall", success, errorHandler);
    }
});

// Helper function to format relative time
function getTimeAgo(dateString) {
    const now = new Date();
    const postDate = new Date(dateString);
    const diffInMs = now - postDate;
    const diffInSeconds = Math.floor(diffInMs / 1000);
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    const diffInHours = Math.floor(diffInMinutes / 60);
    const diffInDays = Math.floor(diffInHours / 24);
    const diffInWeeks = Math.floor(diffInDays / 7);
    const diffInMonths = Math.floor(diffInDays / 30);
    const diffInYears = Math.floor(diffInDays / 365);

    if (diffInSeconds < 60) {
        return diffInSeconds <= 1 ? "just now" : `${diffInSeconds} seconds ago`;
    } else if (diffInMinutes < 60) {
        return diffInMinutes === 1
            ? "1 minute ago"
            : `${diffInMinutes} minutes ago`;
    } else if (diffInHours < 24) {
        return diffInHours === 1 ? "1 hour ago" : `${diffInHours} hours ago`;
    } else if (diffInDays < 7) {
        return diffInDays === 1 ? "1 day ago" : `${diffInDays} days ago`;
    } else if (diffInWeeks < 4) {
        return diffInWeeks === 1 ? "1 week ago" : `${diffInWeeks} weeks ago`;
    } else if (diffInMonths < 12) {
        return diffInMonths === 1
            ? "1 month ago"
            : `${diffInMonths} months ago`;
    } else {
        return diffInYears === 1 ? "1 year ago" : `${diffInYears} years ago`;
    }
}

function success(data) {
    console.log(data);

    // Clear loading message
    field.innerHTML = "";

    if (!data.posts || !Array.isArray(data.posts)) {
        console.error("No posts data received");
        field.innerHTML = '<p class="no-posts">Geen berichten beschikbaar</p>';
        return;
    }

    if (data.posts.length === 0) {
        field.innerHTML =
            '<p class="no-posts">Geen berichten gevonden in jouw groepen</p>';
        return;
    }

    for (const post of data.posts) {
        console.log(post);
        const div = document.createElement("div");
        if (post.image_path !== null) {
            div.classList.add("box");
        } else {
            div.classList.add("boxNoImage");
        }
        div.id = `${post.id}`;

        const nameAndGroupBox = document.createElement("div");
        nameAndGroupBox.classList.add("nameGroupBox");

        const title = document.createElement("h2");
        // title.innerHTML = `${post.username}`
        title.classList.add("title");
        // nameAndGroupBox.appendChild(title)
        const usernameSpan = document.createElement("span");
        usernameSpan.textContent = post.username;
        title.appendChild(usernameSpan);
        // if (post.badge_name === '') {
        console.log(`adding badge`);
        const badge = document.createElement("span");
        // badge.innerHTML = post.badge_name
        badge.textContent = "Verified";
        badge.classList.add("badge");
        nameAndGroupBox.appendChild(badge);
        // }

        nameAndGroupBox.appendChild(title);

        const group = document.createElement("p");
        // Use group name from database instead of hardcoded switch case
        group.innerHTML = post.group_name || `Group ${post.group_id}`;
        group.classList.add("group");
        nameAndGroupBox.appendChild(group);
        div.appendChild(nameAndGroupBox);

        const image = document.createElement("img");
        if (post.image_path !== null) {
            image.src = post.image_path;
            image.alt = post.image_name || "Post image";
            image.classList.add("postImage");
            div.appendChild(image);
        }

        const text = document.createElement("p");
        text.classList.add("textBox");
        text.innerHTML = post.text_content || "";
        div.appendChild(text);

        const date = document.createElement("p");
        // Format the date as relative time (e.g., "5 minutes ago")
        date.innerHTML = getTimeAgo(post.created_at);
        date.classList.add("dateTime");
        div.appendChild(date);

        field.appendChild(div);
    }
}

function errorHandler(error) {
    console.error("Error loading posts:", error);

    // Clear loading message
    field.innerHTML = "";

    // Check if it's an authentication error
    if (
        error.message.includes("401") ||
        error.message.includes("Authentication")
    ) {
        window.location.href = "start2.html";
        return;
    }

    field.innerHTML =
        '<p class="error">Fout bij het laden van bericht. Probeer het opnieuw.</p>';
}
