<?php
session_start();
require "../config/db.php";

if (isset($_SESSION['user_id'])) {
    $userid = $_SESSION['user_id'];
    $stmt = $conn->prepare("select * from users where id = ?");
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    $profile_picture = $user['profile_picture'];
    $fullname = $user['fullname'];
    $email = $user['email'];
    $dob = $user['dob'];
    $dobTimestamp = strtotime($dob);
    $age = date('Y') - date('Y', $dobTimestamp);

    if (date('md', $dobTimestamp) > date('md')) {
        $age--;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile & Posts</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
</head>

<body>
    <a href="../handlers/logout.php"><button id="logoutBtn" class="logout-btn">Logout</button></a>
    <div class="main-container">
        <div class="profile-container">
            <div class="profile-picture">
                <img id="profilePic" src="<?php echo $profile_picture; ?>" alt="Profile Picture">
                <label for="profileUpload" class="edit-icon">&#9998;</label>
                <input type="file" id="profileUpload" accept="image/*" style="display: none;">
            </div>

            <!-- User Info -->
            <div class="profile-details">
                <h2 id="userName"><?php echo $fullname ?> <span class="edit-icon" data-field="name">&#9998;</span></h2>
                <p><strong>Email:</strong> <span id="userEmail"><?php echo $email; ?></span></p>
                <p><strong>Age:</strong> <span id="userAge"><?php echo $age; ?></span> <span class="edit-icon" data-field="age">&#9998;</span></p>
            </div>
        </div>

        <div class="post-container">
            <!-- Add Post -->
            <div class="add-post">
                <h3>Add a Post</h3>
                <input type="file" id="postImage" accept="image/*">
                <div id="imagePreviewContainer" style="display: none;">
                    <img id="imagePreview" src="" alt="Image Preview">
                    <button id="removeImage">Remove</button>
                </div>

                <textarea id="postDesc" placeholder="Write a description..."></textarea>
                <button id="addPostBtn">Post</button>
            </div>

            <!-- Posts Section -->
            <div class="posts-container">
                <h3>Your Posts</h3>
                <div id="postsList">

                </div>
            </div>
        </div>
    </div>



    <script src="../scripts/post.js"></script>
</body>

</html>