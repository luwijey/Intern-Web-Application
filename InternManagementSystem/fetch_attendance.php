<?php
include 'connection.php';

if (isset($_GET['intern_id'])) {
    $intern_id = $_GET['intern_id'];
    $sql = "SELECT 
            name, 
            department, 
            time_in, 
            IFNULL(time_out, '0') AS time_out, 
            formatted_date, 
            IFNULL(hours_completed, 0) AS hours_completed 
            FROM attendance 
            WHERE intern_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $intern_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $attendanceRecords = [];

    while ($row = $result->fetch_assoc()) {
        $attendanceRecords[] = $row;
    }

    echo json_encode($attendanceRecords);
    $stmt->close();
}

$conn->close();
?>
