<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $userid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$fullname = $user['fullname'];
$profile_picture = $user['profile_picture'];
$email = $user['email'];
$dob = $user['dob'];

function dateToAge($dobParam)
{
    $dobData = new DateTime($dobParam);
    $today = new DateTime();
    $age = $dobData->diff($today)->y;
    return $age;
}
$age = dateToAge($dob);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile & Posts</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/2272961514.js" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container">
       
        <div class="profile-section">
            <div class="profile-card">
                <form action="../handlers/profileHandler.php" method="post" id="profileForm" enctype="multipart/form-data">
                    <div class="profile-pic-container">
                        <img  id="preview" src="<?php echo $profile_picture; ?>" alt="logo">
                        <input type="file" accept="image/*" style="display: none;" name="profile_picture" id="updateProfilePicture" onchange="previewImage(event)">
                        <span class="edit-icon add-image" style="display: none;" id="editIcon"><i class="far fa-edit"></i>
                        </span>
                    </div>
                    <input style="font-weight: bold; font-size: large;" type="text" name="fullname" id="profileName" value="<?php echo $fullname; ?>" readonly>
                    <p><?php echo $email; ?></p><br>
                    <div class="ageContainer">
                        <span>DOB: </span>
                        <input type="number" name="age" id="profileDob" value="<?php echo $age; ?>" readonly>
                    </div>
                    <button type="button" id="editProfile">Edit</button>
                    <button type="submit" id="saveProfile" style="display: none;">Save</button>
                </form>
                <br>
                <span id="responseMsg" style="text-align: center;"></span>
            </div>
        </div>

        
        <div class="main-section">
            <div class="post-form">
                <div class="logout-container">
                    <h3>Add Post</h3>
                    <span><button type="submit" id="logoutBtn"><a href="../handlers/logout.php">Logout</a></button></span>
                </div>
                <form action="../handlers/postHandler.php" method="post" id="postForm" enctype="multipart/form-data">
                    <textarea name="description" id="postContent" oninput="autoResize(this)" placeholder="What's on your mind?"></textarea>
                    <div class="input-container-upload">
                        <div class="input-container">
                            <label class="add-image-label" id="addImage"><i class="fa-regular fa-image"></i> Add Image</label>
                            <input type="file" accept="image/*" name="post_image" class="postImageInput" style="display: none;" id="uploadPostImage">
                        </div>
                        <button id="postSubmitBtn" class="post-btn" type="submit">Post</button>
                    </div>
                    <div id="imagePreviewContainer">
                        <img src="" alt="" style="display: none;" id="imagePreview">
                        <span id="removeImage">&#10005</span>
                    </div>
                </form>
            </div>
            <div class="posts" id="postsContainer">
               
            </div>
        </div>
    </div>
    <script src="../scripts/post.js"></script>
</body>

</html>