<?php
    session_start();
    require "../config/db.php";

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // header('Content-Type: application/json');
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        //check if the users exists or not
        $checkstmt = $conn->prepare("select * from users where email = ?");
        $checkstmt->bind_param('s', $email);
        $checkstmt->execute();
        $result = $checkstmt->get_result();

        if($result->num_rows == 0){
            echo json_encode(["status" => "error", "message" => "User doesn't exists please register."]);
            exit;
        }

        //fetch user details
        $user = $result->fetch_assoc();


        //check for the valid password
        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['fullname'];

            echo json_encode(["status" => "success" , "message" => "Login successful!!"]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect password"]);
            exit;
        }

    }
?>