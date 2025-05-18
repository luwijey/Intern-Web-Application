<!--login form -->
<?php
include 'connection.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gmail = mysqli_real_escape_string($conn, $_POST['gmail']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if email exists in the database
    $sql = "SELECT * FROM users WHERE gmail = '$gmail'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if($user) {
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['gmail'] = $user['gmail']; // Store user session
            header("location: welcome.php");
            exit();
        } else {
            echo "<script>alert('Invalid Gmail or Password'); window.location.href='index.php';</script>";
        }
    } else { 
        echo "<script>alert('Gmail not found please register first !'); window.location.href='index.php';</script>";
    }

}
mysqli_close($conn);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Playfair Display">
    <title>OJT SYSTEM</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
<div class="containerContent">
        <div class="logoCdm">
            <img src="uploads/cdm.png" id="cdm-logo" name="cdm-logo" alt="cdm logo" style="height: 10em; width: 10em; display: block; margin: 0 auto; position:relative; top:1em;">
        </div>

    
    <div class="loginForm">
        <!-- Login Form -->
      
        <form action="" method="POST">
        <h1 style="text-align: center; font-family: Playfair Display">LOGIN FORM</h1>
        <label for="gmail">Gmail</label>
        <input type="text" name="gmail" id="gmail" class="input-data" placeholder="Enter your Gmail" required>
        <br>
        <label for="password">Password</label>
        <input type="password" name="password" class="input-data" id="password" placeholder="Enter your Password" required>

        <div class="checkbox-container">
            <input type="checkbox" name="checkbox" id="checkbox" onclick="togglePassword('password', 'checkbox')">
            <label for="checkbox">Show Password</label>
        </div>
        <a href="ForgotAdminPassword.php" class="forgot-password" style="font-size:12px;">Forgot your password?</a>
        <div>   
        <br>
        <br>
         <!-- Button -->
        <button type="submit" id="submit">LOGIN</button>
        <p style="text-align: center;">Don't have an account ? <a href="register.php">Register here</a></p>
        </form>
     
    </div>

</div>

    <script src="script.js"></script>

</body>

<style>
    .containerContent {
    background: #ffffff;
    padding: 2em;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 350px;
    text-align: center;
}

h1 {
    color: #2e7d32; 
    font-family: 'Playfair Display', serif;
}

label {
    display: block;
    margin-top: 20px;
    font-size: 12px;
    color: #1b5e20; 
    text-align: left;
    padding-left:16px;
    font-weight: bold;
}

.input-data {
    width: 85%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #4caf50;
    border-radius: 5px;
    outline: none;
}

.input-data:focus {
    border-color: #1b5e20;
    box-shadow: 0 0 5px #1b5e20;
}

button {
    width: 90%;
    padding: 10px;
    background-color: #388e3c;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 15px;
}

button:hover {
    background-color: #2e7d32; 
}

p {
    margin-top: 30px;
    font-size: 14px;
}

a {
    color: #1b5e20;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}
.checkbox-container {
    display: flex;
    align-items: center;
    margin-top: 10px;
    cursor: pointer;
}

#checkbox {
    width: 10px;
    height: 10px;
    margin-left:17px;
    margin-right: -10px; 
    accent-color: #388e3c;
    margin-bottom:-8.5px;
    margin-top:-5px;
}

.checkbox-container label {
    font-size: 12px;
    color: #1b5e20;
    cursor: pointer;
    font-weight:normal;
    margin-top:5px;
}
.forgot-password {
    display: block;
    text-align: center;
    font-size: 12px;
    color: #388e3c;
    margin-top: 25px;
    margin-bottom: -23px;
    text-decoration: none;
}

.forgot-password:hover {
    color:rgb(38, 106, 41);
    text-decoration: underline;
}
</style>
</html>

