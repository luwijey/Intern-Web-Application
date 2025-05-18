<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Playfair Display">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <div class="forgotpass-container">
    <form action="mailerAdmin.php" method="POST">
        <div class="content">
            <div class="header">
                <h1>Forgot your Password ?</h1>
                <h5>Please enter your gmail below to reset your password.</h5>
            </div>
            <div class="inputsection">
                <label for="gmail">Enter your gmail</label>
                <input type="text" name="gmail" id="gmail" placeholder="Gmail">
            </div>
           
            <div class="buttonsection">
                <button id="sendotp">Send OTP</button>
                <a href="index.php">Cancel</a>
            </div>
        </div>
    </div>
    </form>
</body>
</html>


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