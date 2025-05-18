<?php
include 'connection.php';

if (!isset($_SESSION['gmail'])){
    echo "<script>alert('Gmail not found !'); window.location.href='index.php'; </script>";
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST['currentpassword'], $_POST['newpassword'], $_POST['confirm-password'])) {
        $gmail = $_SESSION['gmail'];
        $currentPassword = $_POST['currentpassword'];
        $newPassword = $_POST['newpassword'];
        $confirmPassword = $_POST['confirm-password'];

        //check if the entered password are the same.
if ($newPassword !== $confirmPassword){
    echo "<script>alert('Password do not Match!'); window.location.href='AdminChangePassword.php'; </script>";
    exit();
}

//fetch the old password in the database 
$sql = "SELECT password FROM users WHERE gmail = ? ";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $gmail);
$stmt->execute();
$stmt->bind_result($hashedPassword);
$stmt->fetch();
$stmt->close();

//check if the current input password is the same as the password registered;
if(!password_verify ($currentPassword, $hashedPassword)) {
    echo "<script>alert('Old Password is incorrect !'); window.location.href='AdminChangePassword.php'; </script>";
    exit();
}

$newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$newpass = "UPDATE users SET password = ? WHERE gmail = ?";
$updatestmt = $conn->prepare($newpass);
$updatestmt->bind_param("ss", $newHashedPassword, $gmail);

if($updatestmt->execute()){
    echo "<script>alert('Password Changed succesfully!'); window.location.href='dashboard.php'; </script>";
    exit();
}else{
    echo "<script>alert('Error Changing Password.');</script>";
}

$updatestmt->close();
$conn->close();
    }
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="./styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="newpass-container">
        <form method="POST">
        <div class="content">
            <div class="header">
                <h1>Change Password</h1>
            </div>
            <div class="inputsection">  
                <label for="currentpassword">Current Password</label>
                <div class="input-container">
                    <input type="password" name="currentpassword" id="currentpassword" placeholder="Current Password" >
                </div>
        
                <label for="newpassword">New Password</label>
                <div class="input-container">
                    <input type="password" name="newpassword" id="newpassword" placeholder="New Password" >
                </div>
                
                <label for="confirm-password">Confirm Password</label>
                <div class="input-container">
                    <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password">
                    <i class="fa-regular fa-eye toggle-password" toggle="#confirm-password"></i>
                </div>
            </div>
            <div class="buttonsection">
                <button id="changePass">Change Password</button>
                <a href="dashboard.php">Cancel</a>
            </div>
        </div>
        </form>     
    </div>
</body>
</html>
<script>
  const toggleIcons = document.querySelectorAll('.toggle-password');

  toggleIcons.forEach(icon => {
    icon.addEventListener('click', function () {
      const input = document.querySelector(this.getAttribute('toggle'));
      const isPassword = input.getAttribute('type') === 'password';
      input.setAttribute('type', isPassword ? 'text' : 'password');

      // Toggle the icon style
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });
  });
</script>
<style>
    .input-container {
        position: relative;
        display:flex;
        align-items:center;
        width:100%;
    }
    .input-container i {
        position: absolute;
        padding:5px;
        right: 20px; 
        width: 15px;
        height: 15px;
        cursor: pointer;
    }
    .content{
        background: #ffffff;
        padding: 2.5em;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;

    }
    .header{
        margin-top:-2em;
    }
    h1 {
        display:flex;
        color: #2e7d32; 
        font-family: 'Playfair Display', serif;
    }
    h5{
        margin-top:-1em;
        font-family: 'Montserrat', sans-serif;
        font-size:15px;
        font-weight:normal;
    }
    label {
        display: block;
        margin-top: 20px;
        margin-bottom: 4px;
        margin-left:5px;
        font-size: 12px;
        color: #1b5e20; 
        text-align: left;
        font-weight: bold;
    }
    input {
        padding:10px;
        width:93.5%;
        border: 1px solid #4caf50;
        border-radius: 5px;
        outline: none;
    }
    input:focus{
        border-color: #1b5e20;
    box-shadow: 0 0 5px #1b5e20;
    }

    button{
        width: 100%;
        padding: 10px;
        background-color: #388e3c;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 20px;
        margin-bottom:20px;
    }
    button:hover {
        background-color: #2e7d32; 
    }
    a {
        color: #1b5e20;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        text-decoration: underline;
    }
</style>