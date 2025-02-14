<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$userId = $_SESSION["user_id"];

//for Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $fullname = $_POST['fullname'];
    $age = $_POST['age'];

    //convert age into dob
    $dob = date('Y-m-d', strtotime("-$age years"));

    $profile_picture = NULL;

    //Logic for profile picture update
    if (isset($_FILES['profile_picture'])) {
        $uploadDir = "../assets/uploads/";
        $relativeDir = "/assets/uploads/";
        $filename = time() . "_" . basename($_FILES['profile_picture']['name']);
        $targetFile = $uploadDir . $filename;

        //move uploaded file
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
            $profile_picture = $relativeDir . $filename;
        }
    }
    //query for saving in database
    if ($profile_picture) {
        $stmt = $conn->prepare("update users set fullname = ?, dob = ?, profile_picture = ? where id = ?");
        $stmt->bind_param("sssi", $fullname, $dob, $profile_picture, $userId);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Profile updated successfully."]);
            exit;
        } else {
            echo  json_encode(["status" => "success", "message" => "Failed to update user details"]);
        }
    } else {
        $stmt = $conn->prepare('update users set fullname = ?, dob = ? where id = ?');
        $stmt->bind_param('ssi', $fullname, $dob, $userId);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Profile Updated successfully."]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Error in updating user details"]);
            exit;
        }
    }
    $stmt->close();
    $conn->close();
}
