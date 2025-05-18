<?php
include("connection.php");


$today = date("m/d/Y");

$sql = "SELECT name, department, time_in, time_out 
        FROM attendance 
        WHERE formatted_date = ? 
        ORDER BY timestamp DESC LIMIT 5"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['department']}</td>
                <td>{$row['time_in']}</td>
                <td>{$row['time_out']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No logins for today</td></tr>";
}


$stmt->close();
$conn->close();
?>
