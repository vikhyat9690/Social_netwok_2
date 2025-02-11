
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('preview');
            preview.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

$(document).ready(function () {
    //for signup form
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
                    window.location.href = "login.php";
                } else {
                    $("#responseMsg").text(response.message).addClass('error');
                }
            }
        });
    })
})