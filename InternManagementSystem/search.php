<?php
header("Content-Type: text/html; charset=UTF-8");
include("connection.php");

if (isset($_GET['query'])) {
    $search = trim($conn->real_escape_string($_GET['query']));

    // Fetch intern data with flexible name matching
    $sql = "SELECT 
                i.id, i.fname, i.lname, i.gmail, i.department, i.date_started, i.required_hours, 
                COALESCE(SUM(a.hours_completed), 0) AS hours_completed
            FROM interns i
            LEFT JOIN attendance a ON i.id = a.intern_id
            WHERE REPLACE(CONCAT(i.fname, ' ', i.lname), '  ', ' ') LIKE ? 
            OR i.fname LIKE ? 
            OR i.lname LIKE ? 
            OR i.gmail LIKE ? 
            OR i.department LIKE ? 
            GROUP BY i.id
            ORDER BY i.fname ASC LIMIT 10";

    $stmt = $conn->prepare($sql);   
    $search_param = "%$search%";
    $stmt->bind_param("sssss", $search_param, $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $hours_remaining = $row['required_hours'] - $row['hours_completed'];

            $status = ($hours_remaining > 0) 
                ? "<span style='color:blue;'>Active</span>" 
                : "<span style='color:green;'>Completed</span>";

            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['fname']} {$row['lname']}</td>
                    <td>{$row['gmail']}</td>
                    <td>{$row['department']}</td>
                    <td>{$row['date_started']}</td>
                    <td>{$row['required_hours']}</td>
                    <td>{$hours_remaining}</td>
                    <td>{$status}</td>
                    <td>
                        <button onclick='viewButton({$row['id']})' style='cursor:pointer; border:none; background:none;'>
                        <img src='uploads/eye.png' alt='Edit' width='15' height='15'>
                        </button>
                        <button onclick='editIntern({$row['id']})' style='cursor:pointer; border:none; background:none;'>
                            <img src='uploads/edit.png' alt='Edit' width='15' height='15'>
                        </button>
                        <button onclick='showModal(\"{$row['fname']}\", \"{$row['lname']}\")' style='cursor:pointer; border:none; background:none;'>
                            <img src='uploads/trash.png' alt='Delete' width='19' height='16'>
                        </button>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No results found</td></tr>";
    }

    $stmt->close();
}
$conn->close();
?>
