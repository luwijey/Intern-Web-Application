<?php 
require 'connection.php'; 
?>

<table border="1">
    <tr>
        <th>Name</th>
        <th>Gmail</th>
        <th>Department</th>
        <th>Date Started</th>
        <th>Required Hours</th>
        <th>Hours Remaining</th>
    </tr>

                <?php
                $sql = "SELECT 
                            i.fname, i.lname, i.gmail, i.department, i.date_started, i.required_hours, 
                            COALESCE(SUM(a.hours_completed), 0) AS hours_completed
                        FROM interns i
                        LEFT JOIN attendance a ON i.id = a.intern_id
                        GROUP BY i.id";

                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }

                while ($row = mysqli_fetch_assoc($result)): 
                    $full_name = $row['fname'] . ' ' . $row['lname']; 
                    $hours_remaining = $row['required_hours'] - $row['hours_completed'];
                ?>

    <tr>
        <td><?php echo htmlspecialchars($full_name); ?></td> 
        <td><?php echo htmlspecialchars($row["gmail"]); ?></td> 
        <td><?php echo htmlspecialchars($row["department"]); ?></td> 
        <td><?php echo htmlspecialchars($row["date_started"]); ?></td> 
        <td><?php echo htmlspecialchars($row["required_hours"]); ?></td> 
        <td><?php echo htmlspecialchars($hours_remaining); ?></td> 
    </tr>

    <?php endwhile; ?>

</table>

<?php 
mysqli_close($conn);
?>
