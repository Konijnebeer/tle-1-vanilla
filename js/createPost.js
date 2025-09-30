import { ajaxRequestPOST, ajaxRequestPUT, ajaxRequestGET } from './utils/fetch.js';
import { requireLogin } from './utils/acount.js';

window.addEventListener("load", init);

const url = "./api/createPost.php";
const uploadUrl = "./api/upload.php?folder=posts";
const groupsUrl = "./api/getUserGroups.php";
let uploadContainer;
let captionContainer;
let imageContainer;
let groupContainer;
let noImageButton;
let postButton;
let postButtonContainer;
let uploadedImageUuid = null; // Store the uploaded image UUID
let userGroups = []; // Store user's groups

function init() {
  // Ensure user is logged in
  requireLogin();
  
  captionContainer = document.querySelector("#captionContainer");
  imageContainer = document.querySelector("#imageContainer");
  groupContainer = document.querySelector("#groupContainer");
  postButtonContainer = document.querySelector("#postButtonContainer");

  uploadContainer = document.querySelector("#uploadContainer");
  uploadContainer.addEventListener("change", uploadFile);

  noImageButton = document.querySelector("#noImageButton");
  noImageButton.addEventListener("click", noImageButtonClickHandler);

  postButton = document.querySelector("#postButton");
  postButton.addEventListener("click", createPost);
  
  // Load user groups
  loadUserGroups();
}

// upload file to the server
function uploadFile() {
  const file = document.querySelector("#upload").files[0];
  
  if (!file) {
    alert("Please select a file");
    return;
  }
  
  // Check file size (2MB limit)
  const maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
  if (file.size > maxFileSize) {
    alert("File is too large. Maximum file size is 2MB. Your file is " + (file.size / 1024 / 1024).toFixed(1) + "MB");
    return;
  }
  
  // Check file type
  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
  if (!allowedTypes.includes(file.type)) {
    alert("Invalid file type. Only JPG, PNG, WebP and GIF files are allowed");
    return;
  }
  
  uploadContainer.classList.add("hidden");
  uploadFileRequest(uploadUrl, file, uploadSuccessHandler, uploadErrorHandler);
  console.log("Uploading file:", file.name, "Size:", (file.size / 1024 / 1024).toFixed(2) + "MB");
}

function noImageButtonClickHandler(e) {
  e.preventDefault();
  uploadContainer.classList.add("hidden");
  noImageButton.classList.add("hidden");

  showPostForm();
}

function showPostForm() {
  groupContainer.classList.remove("hidden");
  captionContainer.classList.remove("hidden");
  postButtonContainer.classList.remove("hidden");
}

function loadUserGroups() {
  ajaxRequestGET(groupsUrl, loadGroupsSuccessHandler, loadGroupsErrorHandler);
}

function loadGroupsSuccessHandler(groups) {
  console.log("User groups loaded:", groups);
  userGroups = groups;
  
  const groupSelect = document.querySelector("#group");
  groupSelect.innerHTML = '';
  
  if (groups.length === 0) {
    groupSelect.innerHTML = '<option value="">Er zijn geen groepen beschikbaar</option>';
    groupSelect.disabled = true;
  } else {
    // Add default option
    groupSelect.innerHTML = '<option value="">Selecteer een groep</option>';
    
    // Add group options
    groups.forEach(group => {
      const option = document.createElement('option');
      option.value = group.id;
      option.textContent = group.name;
      groupSelect.appendChild(option);
    });
    
    // Auto-select first group if only one available
    if (groups.length === 1) {
      groupSelect.value = groups[0].id;
    }
  }
}

function loadGroupsErrorHandler(error) {
  console.error("Failed to load groups:", error);
  const groupSelect = document.querySelector("#group");
  groupSelect.innerHTML = '<option value="">Laden van groepen mislukt</option>';
  groupSelect.disabled = true;
}

function uploadFileRequest(url, file, successCallback, errorCallback) {
  const formData = new FormData();
  formData.append("file", file);

  ajaxRequestPOST(url, successCallback, formData, errorCallback);
}

function uploadSuccessHandler(data) {
  console.log("Upload successful:", data);

  // Store the uploaded image UUID
  uploadedImageUuid = data.uuid;

  // Show the uploaded image
  const uploadedImage = document.querySelector("#uploadedImage");
  uploadedImage.src = `images/${data.folder}/${data.filename}`;
  uploadedImage.alt = data.original_name;
  imageContainer.classList.remove("hidden");
  noImageButton.classList.add("hidden");

  // Show the form after successful upload
  showPostForm();
}

function uploadErrorHandler(error) {
  console.error("Upload failed:", error);
  
  // Check if this is a validation error with details
  if (error.status === 400 && error.responseData && error.responseData.error) {
    alert("Upload failed: " + error.responseData.error.message);
  } else if (error.responseData && error.responseData.error) {
    alert("Upload failed: " + error.responseData.error.message);
  } else {
    alert("Upload failed: " + error.message);
  }
  
  // Reset upload container
  uploadContainer.classList.remove("hidden");
  noImageButton.classList.remove("hidden");
}

function createPost() {
  const caption = document.querySelector("#caption").value.trim();
  const selectedGroupId = document.querySelector("#group").value;

  // Validate that a group is selected if user has multiple groups
  if (userGroups.length > 1 && !selectedGroupId) {
    alert("Please select a group for your post");
    return;
  }

  // Disable the button to prevent double submission
  postButton.disabled = true;
  postButton.textContent = "Creating Post...";

  // Prepare the post data
  const postData = {
    caption: caption,
    image_uuid: uploadedImageUuid, // Will be null if no image was uploaded
    group_id: selectedGroupId ? parseInt(selectedGroupId) : null
  };

  console.log("Creating post with data:", postData);

  // Send the post data using PUT request
  ajaxRequestPUT(url, postSuccessHandler, postData, postErrorHandler);
}

function postSuccessHandler(data) {
  console.log("Post created successfully:", data);
  alert("Post created successfully!");
  // Redirect to home page
  window.location.href = "post.html";
}

function postErrorHandler(error) {
  console.error("Error creating post:", error);
  
  // Check if this is a validation error with details
  if (error.status === 400 && error.responseData && error.responseData.error && error.responseData.error.details) {
    console.log('Validation errors:', error.responseData.error.details);
    // Handle validation errors if needed
    alert("Validation error: " + JSON.stringify(error.responseData.error.details));
  } else if (error.status === 401) {
    // Authentication error - redirect to login
    alert("Please log in to create a post");
    window.location.href = "start2.html";
  } else if (error.responseData && error.responseData.error) {
    // Server returned structured error
    alert("Error: " + error.responseData.error.message);
  } else {
    // Generic error
    alert("Error creating post: " + error.message);
  }
  
  // Re-enable the button
  postButton.disabled = false;
  postButton.textContent = "Create Post";
}
