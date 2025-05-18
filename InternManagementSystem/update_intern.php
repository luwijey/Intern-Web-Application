<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $gmail = mysqli_real_escape_string($conn, $_POST['gmail']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $required_hours = intval($_POST['required_hours']);

    $sql = "UPDATE interns SET 
                fname = '$fname', 
                lname = '$lname', 
                gmail = '$gmail', 
                department = '$department', 
                required_hours = '$required_hours' 
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "Intern updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed: " . mysqli_error($conn)]);
    }
}
mysqli_close($conn);
?>
