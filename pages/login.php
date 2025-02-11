<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://code.jquery.com/jquery-6.3.4.min.js"></script>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Liter&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="main-container">
        <div class="form">
            <form action="../handlers/loginHandler.php" method="post" id="loginForm">
                <h1 style="text-align: center;">Login</h1><br>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <button style="padding: 13px; font-size: 20px" type="submit" id="submitBtn">Log In</button><br>
                <span class="create-acc">Don't have account? <a href="signup.php">Create Account</a></span>
                
            </form>
            <span id="responseMsg"></span>
        </div>
    </div>
</body>

</html>