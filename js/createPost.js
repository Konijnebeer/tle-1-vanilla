window.addEventListener("load", init);

const url = "./api/createPost.php";
const uploadUrl = "./api/upload.php?folder=posts";
let uploadContainer;
let captionContainer;
let imageContainer;
let noImageButton;
let postButton;
let postButtonContainer;
let uploadedImageUuid = null; // Store the uploaded image UUID

function init() {
  captionContainer = document.querySelector("#captionContainer");
  imageContainer = document.querySelector("#imageContainer");
  postButtonContainer = document.querySelector("#postButtonContainer");

  uploadContainer = document.querySelector("#uploadContainer");
  uploadContainer.addEventListener("change", uploadFile);

  noImageButton = document.querySelector("#noImageButton");
  noImageButton.addEventListener("click", noImageButtonClickHandler);

  postButton = document.querySelector("#postButton");
  postButton.addEventListener("click", createPost);
}

// upload file to the server
function uploadFile() {
  uploadContainer.classList.add("hidden");
  const file = document.querySelector("#upload").files[0];
  if (file) {
    uploadRequest(uploadUrl, file, uploadSuccessHandler);
  }
  console.log(file);
}

function noImageButtonClickHandler(e) {
  e.preventDefault();
  uploadContainer.classList.add("hidden");
  noImageButton.classList.add("hidden");

  captionContainer.classList.remove("hidden");
  postButtonContainer.classList.remove("hidden");
}

function uploadRequest(url, file, successCallback) {
  const formData = new FormData();
  formData.append("file", file);

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(response.statusText);
      }
      return response.json();
    })
    .then((data) => successCallback(data))
    .catch((error) => ajaxRequestErrorHandler(error));
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

  // Show the caption fields after successful upload
  captionContainer.classList.remove("hidden");
  postButtonContainer.classList.remove("hidden");
}

function createPost() {
  const caption = document.querySelector("#caption").value.trim();

  // Validate required fields

  // if (!caption) {
  //   alert("Please enter a caption for your post");
  //   return;
  // }

  // Disable the button to prevent double submission
  postButton.disabled = true;
  postButton.textContent = "Creating Post...";

  // Prepare the post data
  const postData = {
    caption: caption,
    image_uuid: uploadedImageUuid, // Will be null if no image was uploaded
  };

  // Send the post data
  fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(postData),
  })
    .then((response) => {
      // Log the response for debugging
      console.log("Response status:", response.status);
      if (!response.ok) {
        // Try to get error details from response
        return response.text().then((text) => {
          console.log("Error response:", text);
          throw new Error(`${response.statusText}: ${text}`);
        });
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        alert("Post created successfully!");
        // Reset the form or redirect
        window.location.href = "home.html"; // or wherever you want to redirect
      } else {
        throw new Error(data.error || "Failed to create post");
      }
    })
    .catch((error) => {
      console.error("Error creating post:", error);
      alert("Error creating post: " + error.message);
    })
    .finally(() => {
      // Re-enable the button
      postButton.disabled = false;
      postButton.textContent = "Create Post";
    });
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
  alert("An error occurred while fetching data. Please try again later.");
}
