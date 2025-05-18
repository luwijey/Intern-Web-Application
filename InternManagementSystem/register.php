    <!--REGISTER NG USER -->
<?php
include 'connection.php';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $gmail = mysqli_real_escape_string($conn, $_POST['gmail']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm-password']);

        //check if the email is already used
        $check_sql="SELECT *FROM USERS WHERE gmail='$gmail'";
        $check_result= mysqli_query($conn, $check_sql);

        if(mysqli_num_rows($check_result) > 0 ){
            echo "<script>alert('Account already exists! Please log in.'); window.location.href='index.php';</script>";
            exit();
        }
    
      
        if (!filter_var($gmail, FILTER_VALIDATE_EMAIL) || !preg_match('/@pnm.edu.ph$/', $gmail)) {
            echo "<script>alert('Please enter a valid Gmail address');</script>";
        } elseif ($password !== $confirm_password) {
            echo "<script>alert('Passwords do not match!');</script>";
        } else {
            // Hash password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
            $sql = "INSERT INTO users (gmail, name, password) VALUES ('$gmail', '$name', '$hashed_password')";
    
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Registration successful!'); window.location.href='index.php';</script>";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
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
</head>

<body>
    <div class="register">
        <h1 id="register-form">REGISTER</h1>
        
            <form action="" method="POST">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required placeholder="Enter your name">
            
            <br><br>
            
            <label for="gmail">Gmail</label>
            <input type="text" name="gmail" id="gmail" required placeholder="Enter your gmail">
            
            <br><br>
            
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="Enter your Password">
            
            <br><br>
            
            <label for="confirm-password">Confirm Password</label>
            <input type="password" name="confirm-password" id="confirm-password" required placeholder="Confirm your Password">
            
            <br><br>    
            
            <div class="button-group">
                <button type="submit" name="submit" id="submit">Register</button>
                <button type="button" name="cancel" id="cancel" onclick="cancelForm()">Cancel</button>
            </div>
            </form>
    </div>
    
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
    <script src="script.js"></script>
</body>
</html>
