<?php
include 'connection.php'; 

$sql = "SELECT department, COUNT(*) as count FROM interns GROUP BY department";
$result = $conn->query($sql);

$counts = [
    "Institute of Computer Studies (ICS)" => 0,
    "Institute of Education (IOE)" => 0,
    "Institute of Business (IOB)" => 0
];

while ($row = $result->fetch_assoc()) {
    $counts[$row['department']] = $row['count'];
}

echo json_encode($counts); // Send data in JSON format
?>
