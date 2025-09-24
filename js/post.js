window.addEventListener('load', init);
const url = "./api/posts.php";

let field;

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
            if (post.image_path !== null){
                div.classList.add('box')
            } else {
                div.classList.add('boxNoImage')
            }
        div.id = `${post.id}`

        const nameAndGroupBox =  document.createElement('div')
        nameAndGroupBox.classList.add('nameGroupBox')

        const title = document.createElement('h2')
        title.innerHTML = `${post.username}`
        title.classList.add('title')
            nameAndGroupBox.appendChild(title)

            const group = document.createElement('p')
            group.innerHTML = `${post.group_id}`
            group.classList.add('group')
            nameAndGroupBox.appendChild(group)
            div.appendChild(nameAndGroupBox)

        const image = document.createElement('img')
            if (post.image_path !== null){
                image.src = post.image_path
                image.alt = post.name
                image.classList.add('postImage')
                div.appendChild(image)
            } else {
            }

        const text = document.createElement('p')
        text.classList.add('textBox')
        text.innerHTML = post.text_content
        div.appendChild(text)

        const date = document.createElement('p')
        date.innerHTML = post.created_at
        date.classList.add('dateTime')
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