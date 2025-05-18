<?php
include "connection.php"; // Ensure database connection

// Fetch interns who have completed their required hours
$sql = "SELECT fname, lname FROM interns 
        WHERE required_hours <= (SELECT COALESCE(SUM(hours_completed), 0) FROM attendance WHERE interns.id = attendance.intern_id)";

$result = mysqli_query($conn, $sql);
$notifications = [];

while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = ["message" => $row['fname'] . " " . $row['lname'] . " has completed their internship."];
}

// Return JSON response
echo json_encode($notifications);
?>
