<?php
    session_start();
    require "../config/db.php";

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        $checkstmt = $conn->prepare("select * from users where email = ?");
        $checkstmt->bind_param('s', $email);
        $checkstmt->execute();
        $result = $checkstmt->get_result();

        if($result->num_rows == 0){
            echo json_encode(["status" => "error", "message" => "User doesn't exists please register."]);
            exit;
        }

        $user = $result->fetch_assoc();


        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['fullname'];

            echo json_encode(["status" => "success" , "message" => "Login successful!!"]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid Credentials"]);
            exit;
        }
        
        $checkstmt->close();
        $conn->close();
    }
?>