<?php 
    session_start();

    $_SESSION = [];

    session_unset();

    session_regenerate_id(true);

    session_destroy();
    echo json_encode(["status" => "success", "message" => "Logged out successfully!!"]);
    exit;
?>