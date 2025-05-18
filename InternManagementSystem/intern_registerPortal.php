<?php
    include 'connection.php';

    if ($_SERVER["REQUEST_METHOD"]== "POST") {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $intern_id = mysqli_real_escape_string($conn, $_POST['intern_id']);
        $gmail = mysqli_real_escape_string($conn, $_POST['gmail']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm-password']);

        $sql = "SELECT * FROM intern_account WHERE intern_id ='$intern_id'";
        $check_result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($check_result) > 0){
            echo "<script>alert('Account already exists! Please log in.'); window.location.href='InternLogin.php';</script>";
            exit();
        }

        if (!filter_var($gmail, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail.com$/', $gmail)) {
            echo "<script>alert('Please enter a valid Gmail address');</script>";
        } elseif ($password !== $confirm_password) {
            echo "<script>alert('Passwords do not match!');</script>";
        } else {
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO intern_account (`name`, `intern_id`, `gmail`, `password`) VALUES ('$name', '$intern_id', '$gmail', '$hashed_password')";

            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Registration Successful!'); window.location.href='InternLogin.php';</script>";
            } else{
                echo "<script>alert('Please fill up in the application form first!'); window.location.href='InternAttPortal.php';</script>";
            }
        }
    }
    mysqli_close($conn);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./styles.css">
    <script defer src="script.js"></script>
</head>

<body>
    <div class="register">
        <h1 id="register-form">Intern Register</h1>
        
            <form action="" method="POST">
            <label for="name">Username</label>
            <input type="text" name="name" id="name" required placeholder="Enter your username">
            <br><br>

            <label for="intern_id">Intern Number:</label>
            <input type="text" name="intern_id" id="intern_id" required placeholder="Enter your Intern number">
            <br><br>
            
            <label for="gmail">Gmail</label>
            <input type="text" name="gmail" id="gmail" required placeholder="Enter your Gmail">
            
            <br><br>
            
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="Enter your Password">
            
            <br><br>
            
            <label for="confirm-password">Confirm Password</label>
            <input type="password" name="confirm-password" id="confirm-password" required placeholder="Confirm your Password">
            
            <br><br>    
            
            <div class="button-group">
                <button type="submit" name="submit" id="submit">Register</button>
                <button type="button" name="cancel" id="cancel" onclick="cancelIntern()">Cancel</button>
            </div>
            </form>
    </div>

    <script>
    document.getElementById('submit').addEventListener('click', function(e) {
        const username = document.getElementById('name').value;
        if (/\s/.test(username)) {
            alert('Username should not contain spaces !');
            e.preventDefault();
        }
    });
    </script>

    
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #e8f5e9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register {
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
            margin-top: 10px;
            font-size: 14px;
            color: #1b5e20;
            text-align: left;
            padding-left: 16px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            width: 85%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #4caf50;
            border-radius: 5px;
            outline: none;
        }

        input:focus {
            border-color: #1b5e20;
            box-shadow: 0 0 5px #1b5e20;
        }

        .button-group {
            display: flex;
            margin-right:-30px;
            margin-top: 15px;
        }

        .button-group button {
            width: 40%;
            padding: 10px;
            margin-left:15px;
            background-color: #388e3c;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .button-group button:hover {
            background-color: #2e7d32;
        }

        #submit {
            background-color: #4CAF50;
            color: white;
        }

        #cancel {
            background-color: #f44336;
            color: white;
        }

        #submit:hover {
            background-color: #45a049;
        }

        #cancel:hover {
            background-color: #e53935;
        }
    </style>
   
</body>
</html>
