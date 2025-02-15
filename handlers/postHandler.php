<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized Access"]);
    exit;
}

$userId = $_SESSION['user_id'];

//For creating post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['post_id'])) {
    $description = isset($_POST['description']) ? trim($_POST['description']) : "";
    $post_image = NULL;

    if (isset($_FILES['post_image']) && $_FILES['post_image']['size'] > 0) {
        $targetDir = "../assets/posts/";
        $relativeDir = "/assets/posts/";
        $fileName = time() . "_" . basename($_FILES['post_image']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['post_image']['tmp_name'], $targetFile)) {
            $post_image = $relativeDir . $fileName;
        } else {
            echo json_encode(["status" => "error", "message" => "Error uploading image"]);
            exit;
        }
    }

    
    $stmt = $conn->prepare("INSERT into posts (user_id, description, image) values (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $description, $post_image);

    if($description === "" && $post_image === NULL) {
        echo json_encode(["status" => "error", "message" => "Nothing to post? Try adding an image."]);
        exit;
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Post created successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to create post: " . $stmt->error]);
    }
    exit;
}

//For fetching all posts
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT posts.*, users.fullname, users.profile_picture FROM posts JOIN users ON posts.user_id = users.id where users.id = ? ORDER BY posts.created_at desc");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($posts);
    exit;
}

// Handling likes and dislikes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id']) && isset($_POST['reaction'])) {
    $postId = $_POST['post_id'];
    $reaction = $_POST['reaction'];

    // Check if user already reacted
    $stmt = $conn->prepare("SELECT reaction FROM post_reaction WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param('ii', $userId, $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingReaction = $result->fetch_assoc();

    if ($existingReaction) {
        if ($existingReaction['reaction'] === $reaction) {
            // Remove reaction if clicked again
            $stmt = $conn->prepare("DELETE FROM post_reaction WHERE user_id = ? AND post_id = ?");
            $stmt->bind_param('ii', $userId, $postId);
            $stmt->execute();

            $updateField = ($reaction === 'like') ? "likes" : "dislikes";
            $conn->query("UPDATE posts SET $updateField = GREATEST($updateField - 1, 0) WHERE id = $postId");
        } else {
            // Update reaction if different reaction is clicked
            $stmt = $conn->prepare("UPDATE post_reaction SET reaction = ? WHERE user_id = ? AND post_id = ?");
            $stmt->bind_param('sii', $reaction, $userId, $postId);
            $stmt->execute();

            if ($reaction === 'like') {
                $conn->query("UPDATE posts SET likes = likes + 1, dislikes = GREATEST(dislikes - 1, 0) WHERE id = $postId");
            } else {
                $conn->query("UPDATE posts SET dislikes = dislikes + 1, likes = GREATEST(likes - 1, 0) WHERE id = $postId");
            }
        }
    } else {
        // Insert new reaction
        $stmt = $conn->prepare("INSERT INTO post_reaction (user_id, post_id, reaction) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $userId, $postId, $reaction);
        $stmt->execute();

        $updateField = ($reaction === 'like') ? "likes" : "dislikes";
        $conn->query("UPDATE posts SET $updateField = $updateField + 1 WHERE id = $postId");
    }

    echo json_encode(["status" => "success", "message" => "Reaction updated"]);
    exit;
}

//for DELETING posts
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $postId = $_POST['post_id'];
    
    $stmt = $conn->prepare("SELECT * from posts where id =? and user_id = ?");
    $stmt->bind_param('ii', $postId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    if($post) {
        //now delete the post
        $stmt = $conn->prepare("DELETE from posts where id = ?");
        $stmt->bind_param("i", $postId);
        if($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Post deleted successfully"]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete post"]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Unauthorized action (Cannot delete someones post)"]);
        exit;
    }
}
