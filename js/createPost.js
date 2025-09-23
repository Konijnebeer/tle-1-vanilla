window.addEventListener("load", init);

const url = "./api/createPost.php";
const uploadUrl = "./api/upload.php?folder=posts";
let uploadContainer;
let titleContainer;
let captionContainer;
let imageContainer;

function init() {
  titleContainer = document.querySelector("#titleContainer");
  titleContainer.classList.add("hidden");
  captionContainer = document.querySelector("#captionContainer");
  captionContainer.classList.add("hidden");
  imageContainer = document.querySelector("#imageContainer");

  uploadContainer = document.querySelector("#uploadContainer");
  uploadContainer.addEventListener("change", uploadFile);
}
function uploadFile() {
  uploadContainer.classList.add("hidden");
  const file = document.querySelector("#upload").files[0];
  if (file) {
    uploadRequest(uploadUrl, file, uploadSuccessHandler);
  }
  console.log(file);
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

  // Show the uploaded image
  const uploadedImage = document.querySelector("#uploadedImage");
  uploadedImage.src = `images/${data.folder}/${data.filename}`;
  uploadedImage.alt = data.original_name;
  imageContainer.classList.remove("hidden");

  // Show the title and caption fields after successful upload
  titleContainer.classList.remove("hidden");
  captionContainer.classList.remove("hidden");
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
