$(document).ready(function () {
    $("#signupForm").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            type: "POST",
            url: "../handlers/signupHandler.php",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                if(response.status === 'success') {
                    $("#responseMsg").text(response.message).addClass('success');
                    
                } else {
                    $("#responseMsg").text(response.message).addClass('error');
                }
            }
        });
    })

    $("#loginForm").submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            type: "POST",
            url: "../handlers/loginHandler.php",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                if(response.status === 'success') {
                    $("#responseMsg").text(response.message).addClass('success');
                    setTimeout(function() {
                        window.location.href = "profile_posts.php";
                    }, 1000)
                } else {
                    $("#responseMsg").text(response.message).addClass('error');
                }
            }
        });
    })
})

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('preview');
        preview.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}