const url = "./api/createPost.php";


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
