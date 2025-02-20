$(document).ready(function () {
    fetchPosts();
    $("#logoutBtn").click(function () {
        if(confirm("Are you sure you want to logout?")) {
            $.ajax({
                type: "POST",
                url: "../handlers/logout.php",
                success: function () {
                        alert("Logged out successfully!!");
                        window.location.href = "../pages/login.php";

                }
            });
        }
    });


    $("#editProfile").click(function () {
        $("#profileName, #profileDob").prop("readonly", false).focus(); 
        $(this).hide();
        $("#editIcon").show(); 
        $("#saveProfile").show(); 
    });

    $("#profileForm").on("submit", function (e) {
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
                    $("#editProfile").show();
                    $("#saveProfile").hide();
                    $("#profileName, #profileDob").prop("readonly", true); // 
                    $("#responseMsg").text(response.message).addClass('success').fadeOut(2000);
                } else {
                    $("#responseMsg").text(response.message).addClass('error');
                }
            }
        });
    });

    

    $("#editIcon").click(function () {
        $("#updateProfilePicture").click();
    })


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
                } else {
                    alert(response.message);
                }
            }
        });
    })

    function fetchPosts() {
        $.ajax({
            type: "GET",
            url: "../handlers/postHandler.php",
            success: function (response) {
                let posts = JSON.parse(response);
                let html = "";

                posts.forEach(post => {
                    let formattedDescription = post.description.replace(/\n/g, "<br>");
                    html += `
                        <div class = "post-card" data-id="$(post.id)">
                            <div class="post-header">
                                <div class="name-pic">
                                    <img src="../${post.profile_picture}" class="profile-pic">
                                    <div class="name-date-section">
                                        <span>${post.fullname}</span>
                                        <span class="createdAt">Posted on - ${post.created_at}</span>
                                    </div>
                                </div>
                                <button class="delete-btn" data-id="${post.id}">&#10005</button>
                            </div>
                            <p>${formattedDescription}</p>
                            ${post.image ? `<img src="${post.image}" class="post-image">` : ""}
                            <div class="post-actions">
                                <button class="like-btn" data-id="${post.id}"><i class="fa fa-thumbs-up" aria-hidden="true"></i><b> Likes</b> ${post.likes}</button>
                                <button class="dislike-btn" data-id="${post.id}"><i class="fa fa-thumbs-down" aria-hidden="true"></i> <b>Dislikes</b> ${post.dislikes}</button>
                            </div>
                        </div>
                    `
                });
                $("#postsContainer").html(html);
            }
        });
    }

    $(document).on("click", ".like-btn", function() {

        let postId = $(this).data("id");
        sendReaction(postId, "like");
    })

    $(document).on("click", ".dislike-btn", function() {

        let postId = $(this).data("id");
        sendReaction(postId, "dislike");
    })

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


    $("#addImage").on("click", function() {
        $("#uploadPostImage").click();
    })

    



    $(document).on('click', ".delete-btn", function () {  
        let postId = $(this).data("id");
        let postCard = $(this).closest(".post-card");

        if(confirm("Are you sure to delete the post?")) {
            $.ajax({
                type: "POST",
                url: "../handlers/postHandler.php",
                data: {post_id: postId},
                dataType: "json",
                success: function (response) {
                    if(response.status === 'success') {
                        postCard.fadeOut(500, function() {
                            $(this).remove();
                        });
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
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

    fileInput.value = "";
    preview.src = ""; 
    container.style.display = "none"; 

});

function autoResize(textarea) {
    textarea.style.height = "auto"; 
    textarea.style.height = textarea.scrollHeight + "px"; 
}

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {  
        const preview = document.getElementById("preview");
        preview.src = reader.result;
    };

    reader.readAsDataURL(event.target.files[0]);
}