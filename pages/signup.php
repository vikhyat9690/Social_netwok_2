<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <div class="main-container">
        <div class="form">
            <form action="../handlers/signupHandler.php" method="post" id="signupForm">
                <h2>Join Social Network</h2>
                <div class="profile_picture">
                    <label class="file-upload">
                        <img id="preview" src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="">
                        <input type="file" name="profile_picture" accept="image/*" id="fileInput" onchange="previewImage(event)">
                    </label><br>
                    <label class="edit-icon" for="fileInput">Upload Profile Pic</label>
                </div><br>

                <label for="fullname">Full Name</label>
                <input type="text" name="fullname" id="fullname" required>

                <label for="dob">DOB</label>
                <input type="date" name="dob" id="dob" required>

                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" required>

                <div class="password">
                    <div class="label-password">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required>
                    </div>

                    <div class="input-password">
                        <label for="rePassword">Re-password</label>
                        <input type="password" name="rePassword" id="rePassword" required>
                    </div>
                </div>

                <button style="font-size: large;" type="submit" id="submitBtn">Sign Up</button><br>
                <span style="text-align: center; color: gray;">Already User? <a style="color: #0056b3;" href="login.php">Login</a></span>
                <span style="text-align: center;" id="responseMsg"></span>
            </form>
        </div>
    </div>
    <script src="../scripts/auth.js"></script>
</body>

</html>