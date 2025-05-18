<!--SAVE INTERN DETAILS-->
<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $gmail = $_POST["gmail"];
    $phone = $_POST["phone-no"];
    $department = $_POST["department"];
    $school = $_POST["school"];
    $dateStarted = $_POST["date-started"];
    $required_hours = $_POST["required-hours"];
    $faceDescriptor = $_POST["face-descriptor"]; // Face Descriptor JSON

    // Check if face descriptor is empty
    if (empty($faceDescriptor)) {
        die("<script>alert('⚠️ Face descriptor is missing. Please try again!'); window.history.back();</script>");
    }

    // Resume Upload
    $resumeDir = "resume/";
    $resumeFile = $resumeDir . basename($_FILES["resume"]["name"]);
    move_uploaded_file($_FILES["resume"]["tmp_name"], $resumeFile);

    // Ensure JSON is valid
    $faceDescriptor = json_encode(json_decode($faceDescriptor)); // Reformat JSON

    // Use prepared statement for security
    $sql = "INSERT INTO interns (fname, lname, gmail, phone_no, department, school, date_started, required_hours, resume, face_descriptor) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $fname, $lname, $gmail, $phone, $department, $school, $dateStarted, $required_hours, $resumeFile, $faceDescriptor);

    if ($stmt->execute()) {
        echo "<script>alert('Intern registered successfully!'); window.history.back();</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
