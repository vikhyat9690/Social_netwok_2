<?php
    require "../config/db.php";

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullname = $_POST['fullname'];
        $dob = $_POST['dob'];
        $email = trim( $_POST['email']);
        $password = $_POST['password'];
        $rePassword = $_POST['rePassword'];


        //check if the users exists
        $checkstmt = $conn->prepare("select * from users where email = ?");
        $checkstmt->bind_param('s', $email);
        $checkstmt->execute();
        $checkstmt->store_result();

        if($checkstmt->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "User already exists with this email"]);
            exit;
        }

        $checkstmt->close();

        //Validate password match
        if($password === $rePassword) {
            $password = password_hash($password, PASSWORD_BCRYPT);
        } else {
            echo json_encode(["status" => "error", "message" => "Password doesn't match"]);
            exit;
        }
        

        //Profile Picture match
        $profile_picture = "/assets/uploads/default.png";
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['size'] > 0) {
            $targetDir = "../assets/uploads/";
            $relativeDir = "/assets/uploads/"; 
            $fileName = time() . "_" . basename($_FILES['profile_picture']['name']);
            $targetFile = $targetDir . $fileName;
            $imgFileType = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    
            // Validate image type
            $validExtensions = ["jpeg", "jpg", "png", "webp", "gif"];
            if (!in_array($imgFileType, $validExtensions)) {
                echo json_encode(["status" => "error", "message" => "Invalid image format. Allowed: jpeg, jpg, png, webp, gif"]);
                exit;
            }
    
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                $profile_picture = $relativeDir . $fileName;
            } else {
                echo json_encode(["status" => "error", "message" => "Error uploading profile picture"]);
                exit;
            }
        }



        //Execute the query
        $stmt = $conn->prepare("insert into users (fullname, dob, email, password, profile_picture) values (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $fullname, $dob, $email, $password, $profile_picture);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        echo json_encode(["status" => "success", "message" => "User Registered Succesfully"]);
    }
?>