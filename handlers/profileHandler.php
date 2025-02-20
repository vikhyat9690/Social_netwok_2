<?php
session_start();
require "../config/db.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit;
}

$userId = $_SESSION["user_id"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : null;
    $age = isset($_POST['age']) ? trim($_POST['age']) : null;

    $dob = date('Y-m-d', strtotime("-$age years"));

    $profile_picture = null;

    if (!empty($_FILES['profile_picture']['name'])) {
        $uploadDir = "../assets/uploads/";
        $relativeDir = "/assets/uploads/";
        $filename = time() . "_" . basename($_FILES['profile_picture']['name']);
        $targetFile = $uploadDir . $filename;

        $ImgExt = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $validExt = ["jpeg", "jpg", "png", "webp"];

        if (!in_array($ImgExt, $validExt)) {
            echo json_encode(["status" => "error", "message" => "Invalid file type"]);
            exit;
        }

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
            $profile_picture = $relativeDir . $filename;
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to upload profile picture"]);
            exit;
        }
    }

    if ($fullname && $dob && $profile_picture) {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, dob = ?, profile_picture = ? WHERE id = ?");
        $stmt->bind_param("sssi", $fullname, $dob, $profile_picture, $userId);
    } elseif ($fullname && $dob) {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, dob = ? WHERE id = ?");
        $stmt->bind_param("ssi", $fullname, $dob, $userId);
    } elseif ($fullname && $profile_picture) {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, profile_picture = ? WHERE id = ?");
        $stmt->bind_param("ssi", $fullname, $profile_picture, $userId);
    } elseif ($dob && $profile_picture) {
        $stmt = $conn->prepare("UPDATE users SET dob = ?, profile_picture = ? WHERE id = ?");
        $stmt->bind_param("ssi", $dob, $profile_picture, $userId);
    } elseif ($fullname) {
        $stmt = $conn->prepare("UPDATE users SET fullname = ? WHERE id = ?");
        $stmt->bind_param("si", $fullname, $userId);
    } elseif ($dob) {
        $stmt = $conn->prepare("UPDATE users SET dob = ? WHERE id = ?");
        $stmt->bind_param("si", $dob, $userId);
    } elseif ($profile_picture) {
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $profile_picture, $userId);
    } else {
        echo json_encode(["status" => "error", "message" => "No fields to update"]);
        exit;
    }



    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error updating profile."]);
    }

    $stmt->close();
    $conn->close();
}
