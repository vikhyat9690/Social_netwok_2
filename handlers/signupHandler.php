<?php
    require "../config/db.php";

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullname = htmlspecialchars($_POST['fullname']);
        $dob = htmlspecialchars($_POST['dob']);
        $email = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $rePassword = $_POST['rePassword'];


        $checkstmt = $conn->prepare("select * from users where email = ?");
        $checkstmt->bind_param('s', $email);
        $checkstmt->execute();
        $checkstmt->store_result();

        if($checkstmt->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "User already exists with this email"]);
            exit;
        }

        if($password === $rePassword) {
            $password = password_hash($password, PASSWORD_BCRYPT);
        } else {
            echo json_encode(["status" => "error", "message" => "Password doesn't match"]);
            exit;
        }
        

        $profile_picture = "/assets/uploads/default.png";
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['size'] > 0) {
            $targetDir = "../assets/uploads/";
            $relativeDir = "/assets/uploads/"; 
            $fileName = time() . "_" . basename($_FILES['profile_picture']['name']);
            $targetFile = $targetDir . $fileName;
            $imgFileType = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    
            $validExtensions = ["jpeg", "jpg", "png", "webp"];
            if (!in_array($imgFileType, $validExtensions)) {
                echo json_encode(["status" => "error", "message" => "Invalid image format. Allowed: jpeg, jpg, png, webp, gif"]);
                exit;
            }

            if($_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
                echo json_encode(["status" => "error", "message" => "Image must me less than 2MB"]);
                exit;
            }
    
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                $profile_picture = $relativeDir . $fileName;
            } else {
                echo json_encode(["status" => "error", "message" => "Error uploading profile picture"]);
                exit;
            }
        }



        $stmt = $conn->prepare("insert into users (fullname, dob, email, password, profile_picture) values (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $fullname, $dob, $email, $password, $profile_picture);
        if($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "User Registered Succesfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to Register user"]);
            exit;
        }
        
        $checkstmt->close();
        $stmt->close();
        $conn->close();
    }
?>