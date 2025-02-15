$(document).ready(function () {
    fetchPosts();
    $("#logoutBtn").click(function () {
        $.ajax({
            type: "POST",
            url: "../handlers/logout.php",
            success: function () {
                window.location.href = "login.php";
            }
        });
    });


    //Profile update Logic for AJAX

    $("#editProfile").click(function () {
        $("#profileName, #profileAge").prop("readonly", false).focus(); // Enable editing
        $(this).hide();
        $("#editIcon").show(); // Hide edit button
        $("#saveProfile").show(); // Show save button
    });

    $("#profileForm").submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            type: "POST",
            url: "../handlers/profileHandler.php",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    $("#responseMsg").text(response.message).addClass('success');
                    $("#editProfile").show();
                    $("#saveProfile").hide();
                    $("#profileName, #profileAge").prop("readonly", true); // Disable editing after saving

                    setTimeout(() => {
                        window.location.href = "../pages/profile_posts.php";
                    }, 1000);
                } else {
                    $("#responseMsg").text(response.message).addClass('error');
                }
            },
            error: function (xhr) {
                console.log("AJAX Error: ", xhr.responseText);
            }
        });
    });

    $("#editIcon").on("click", function () {
        $("#updateProfilePicture").click();
    })


    //handle post submission
    $("#postForm").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);


        $.ajax({
            type: "POST",
            url: "../handlers/postHandler.php",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    $("#postForm")[0].reset();
                    $("#imagePreview").hide();
                    $("#imagePreviewContainer").hide();
                    fetchPosts();
                }
            }
        });
    })

    // $("#postSubmitBtn").on('click', function () {
    //     window.location.href = "../pages/profile_posts.php";
    // })


    //fetch posts function
    function fetchPosts() {
        $.ajax({
            type: "GET",
            url: "../handlers/postHandler.php",
            success: function (response) {
                let posts = JSON.parse(response);
                let html = "";

                posts.forEach(post => {
                    html += `
                        <div class = "post-card" data-id="$(post.id)">
                            <div class="post-header">
                                <div class="name-pic">
                                    <img src="../${post.profile_picture}" class="profile-pic">
                                    <span>${post.fullname}</span>
                                </div>
                                <button class="delete-btn" data-id="${post.id}">&#10005</button>
                            </div>
                            <p>${post.description}</p>
                            ${post.image ? `<img src="${post.image}" class="post-image">` : ""}
                            <div class="post-actions">
                                <button class="like-btn" data-id="${post.id}">👍 <b>Likes</b> ${post.likes}</button>
                                <button class="dislike-btn" data-id="${post.id}">👎 <b>Dislikes</b> ${post.dislikes}</button>
                            </div>
                        </div>
                    `
                });
                $("#postsContainer").html(html);
            }
        });
    }

    //Like post
    $(document).on("click", ".like-btn", function() {

        let postId = $(this).data("id");
        sendReaction(postId, "like");
    })

    //Dislike post
    $(document).on("click", ".dislike-btn", function() {

        let postId = $(this).data("id");
        sendReaction(postId, "dislike");
    })

    //function to send reaction
    function sendReaction(postId, reaction) {
        $.ajax({
            type: "POST",
            url: "../handlers/postHandler.php",
            data: {
                post_id: postId,
                reaction: reaction
            },
            dataType: "json",
            success: function (response) {
                if(response.status === "success") {
                    fetchPosts();
                }
            }
        });
    }


    //upload image trigger
    $("#addImage").on("click", function() {
        $("#uploadPostImage").click();
    })
});




document.getElementById("uploadPostImage").addEventListener('change', function (e) {
    let file = e.target.files[0];
    let preview = document.getElementById("imagePreview");
    let container = document.getElementById("imagePreviewContainer");


    if (file) {
        let reader = new FileReader();
        reader.onload = function (event) {
            preview.src = event.target.result;
            preview.style.display = "block";
            container.style.display = "block";
        };
        reader.readAsDataURL(file);
    }
})

document.getElementById("removeImage").addEventListener("click", function () {
    let fileInput = document.getElementById("uploadPostImage");
    let preview = document.getElementById("imagePreview");
    let container = document.getElementById("imagePreviewContainer");

    fileInput.value = ""; // Reset file input
    preview.src = ""; // Clear preview source
    container.style.display = "none"; // Hide the container

});

