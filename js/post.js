window.addEventListener('load', init);
const url = "./api/posts.php";

let field

function init() {
    field = document.getElementById('field');
    let actionurl = url + "?action=getall";
ajaxRequest(actionurl, success);

}
function success(data) {
    console.log(data);
    if (data.posts && Array.isArray(data.posts)) {
        for (const post of data.posts) {
            console.log(post);
        const div = document.createElement('div')
        div.classList.add('inner')
        div.id = `div${post.id}`

        const title = document.createElement('h2')
        title.innerHTML = `${post.username}`
        div.appendChild(title)

        const image = document.createElement('img')
        image.src = post.image_path
        image.alt = post.image_name
        div.appendChild(image)

        const text = document.createElement('p')
        text.innerHTML = post.text_content
        div.appendChild(text)

        const date = document.createElement('p')
        date.innerHTML = post.created_at
        div.appendChild(date)

        field.appendChild(div)
    }
}}

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