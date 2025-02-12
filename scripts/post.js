$(document).ready(function() {
    $("#logoutBtn").click(function() {
        $.ajax({
            type: "POST",
            url: "../handlers/logout.php",
            success: function(response) {
                window.location.href = "login.php";
            }
        });
    });

    
});

document.getElementById('postImage').addEventListener('change', function(event) {
    let file = event.target.files[0];
    
    if (file) {
        let reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

// Remove image preview when clicking 'Remove' button
document.getElementById('removeImage').addEventListener('click', function() {
    document.getElementById('postImage').value = '';  // Clear file input
    document.getElementById('imagePreviewContainer').style.display = 'none';
});
