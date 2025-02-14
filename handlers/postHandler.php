<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized Access"]);
    exit;
}

$userId = $_SESSION['user_id'];

//For creating post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = isset($_POST['description']) ? trim($_POST['description']) : "+-----+";
    $post_image = NULL;
    if (isset($_FILES['post_image']) && $_FILES['post_image']['size'] > 0) {
        $targetDir = "../assets/posts/";
        $relativeDir = "/assets/posts/";
        $fileName = time() . "_" . basename($_FILES['post_image']['name']);
        $targetFile = $targetDir . $fileName;

        // Move uploaded file
        if (move_uploaded_file($_FILES['post_image']['tmp_name'], $targetFile)) {
            $post_image = $relativeDir . $fileName;
        } else {
            echo json_encode(["status" => "error", "message" => "Error uploading profile picture"]);
            exit;
        }
    }

    $stmt = $conn->prepare("insert into posts (user_id, description, image) values (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $description, $post_image);
    if($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Post created successfully"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to created post"]);
        exit;
    }
}

//For Fetching all posts
if($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("select posts.*, users.fullname, users.profile_picture from posts join users on posts.user_id = users.id order by posts.created_at desc");
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($posts);
    exit;
}


//handling likes and dislikes
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id']) && isset($_POST['reaction'])) {
    $postId = $_POST['post_id'];
    $reaction = $_POST['reaction'];

    //If user already reacted then
    $stmt = $conn->prepare("select reaction from post_reactions where user_id = ? and post_id = ?");
    $stmt->bind_param('ii', $userId, $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingReaction = $result->fetch_assoc();


    if($existingReaction) {
        if($existingReaction['reaction'] === $reaction) {
            //removing reaction if clicked again
            $stmt = $conn->prepare("delete from post_reactions where user_id = ? and post_id = ?");
            $stmt->bind_param('ii', $userId, $postId);
            $stmt->execute();

            $updateField = ($reaction === 'like') ? "likes" : "dislikes";
            $conn->query("update posts set $updateField = $updateField - 1 where id = $postId");
        } else {
            //update reaction if different reaction is clickied
            $stmt = $conn->prepare("update posts.reactions set reaction = ? where user_id = ? and post_id = ?");
            $stmt->bind_param('sii', $reaction, $userId, $postId);
            $stmt->execute();

            $likeChange =  ($reation === 'like') ? '+1' : '-1';
            $dislikeChange = ($reaction === 'dislike') ? '+1' : '-1';

            $conn->query("update posts set likes = likes $likeChange , dislikes = dislikes $dislikeChange where id = $postId");
        }
    } else {
        //insert new reaction
        $stmt = $conn->prepare("insert into post_reaction (user_id, post_id, reaction) values (?, ?, ?)");
        $stmt->bind_param("iis", $userId, $postId, $reaction);
        $stmt->execute();


        $updateField = ($reaction === 'like') ? "likes" : "dislikes";

        $conn->query("update posts set $updateField = $updateField + 1 where id = $postId");
    }
    echo json_encode(["status" => "success", "message" => "Reaction updated"]);
    exit;
}
