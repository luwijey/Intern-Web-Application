<!--WELCOME GREETINGS-->
<?php
    session_start();

    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "ojt_db";

    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Ensure the user is logged in
    if (!isset($_SESSION['gmail'])) {
        echo "<script>alert('Please log in first!'); window.location.href='index.php';</script>";
        exit();
    }   

    $gmail = $_SESSION['gmail']; // Get the logged-in user's email
    $name = "Guest"; // Default value if the user is not found

    $sql = "SELECT name FROM users WHERE gmail = '$gmail'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $name = $row['name']; 
    }

    mysqli_close($conn);
?>

<!--html-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <title>Welcome!</title>
</head>
<body>
<div class="message-container">
    <!-- Welcome Message -->
    <div id="welcome-message" class="welcome-message">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
        Redirecting to your dashboard....
    </div>
</div>
<script>
        // Redirect to dashboard after 3 seconds
        setTimeout(function() {
            window.location.href = 'dashboard.php'; 
        }, 2000); 
    </script>

</body>
</html>

<style>
    /* General Styles */
    .message-container {
        background-color: #fff;
        padding: 30px;
        border-radius: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        width: 100%;
        text-align: center;
    }
    h1 {
        font-size: 36px;
        color: #4CAF50;
        margin-bottom: 10px;
    }
    .welcome-message {
        font-size: 24px;
        font-weight: bold;  
        color: #333;
        margin-bottom: 15px;
    }
    /* Dashboard Styles */
    .dashboard {
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .dashboard-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .dashboard-item {
        background: #e3e3e3;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
    }
</style>
