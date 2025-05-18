<!--INTERN LOGIN ATTENDANCE-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Login</title>
    <meta http-equiv="refresh" content="10">
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
    <script defer src="face_login.js"></script>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <h2>Automatic Face Login with Liveness Detection</h2>
    <span id="dateTime"></span><br>
        <video id="video" width="720" height="560" autoplay></video>
        <div id="status">Initializing...</div><br>
        <button> <a href= "InternLogin.php" target="_blank">View Attendance</a></button>
        
<style>

a {
  text-decoration: none;
  color: white;
}
    body {
    margin: 0;
    padding: 12px;
    display: flex;
    flex-direction: column; /* Stack items vertically */
    justify-content: center; /* Center vertically */
    align-items: center; /* Center horizontally */
    height: 100vh;
    background: #e8f5e9; /* Maintain your background */
    }

    h2 {
        text-align: center;
        font-size: 24px;
        color: #2e7d32;
        margin-bottom: 20px; /* Space between elements */
    }
    button {
            background: #2e7d32;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s, transform 0.2s;
        }

    button:hover {
        background: #1b5e20;
        transform: scale(1.05);
    }

    
     video {
        width:500px;
        height: auto;
        border-radius: 8px;
        border: 2px solid #2e7d32;
        margin-bottom: 15px;
    }

    #status,     span {
        margin-top: 10px;
        font-size: 16px;
        font-weight: bold;
        color: #2e7d32;
    }
    
</style>

</html>
