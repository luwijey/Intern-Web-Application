<?php
include 'connection.php';   

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$sql = "SELECT 
            i.id, i.fname, i.lname, i.gmail, i.department, i.date_started, i.required_hours, i.email_sent,
            COALESCE(SUM(a.hours_completed), 0) AS hours_completed
        FROM interns i
        LEFT JOIN attendance a ON i.id = a.intern_id
        GROUP BY i.id";

$result = mysqli_query($conn, $sql);

// Check if query execution failed
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $full_name = $row['fname'] . ' ' . $row['lname'];
        $hours_remaining = $row['required_hours'] - $row['hours_completed'];

        // Determine Status
        if ($hours_remaining > 0) {
            $status = "<span style='color:blue;'>Active</span>";
        } elseif ($hours_remaining <= 0 && $row['email_sent'] == 0 ) {
            $status = "<span style='color:green;'>Completed</span>";

             $mail = new PHPMailer(true);
             try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; 
                $mail->SMTPAuth   = true;
                $mail->Username   = 'louie.jay.castillo@student.pnm.edu.ph';
                $mail->Password   = 'avmmnktcvnyoihse';   
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
    
                $mail->setFrom('louie.jay.castillo@student.pnm.edu.ph', 'Intern Management System');
                $mail->addAddress($row['gmail'], $full_name);
    
                $mail->isHTML(true);
                $mail->Subject = 'Internship Hours Completed';
                $mail->Body    = 
                "Hi! <strong>{$full_name}</strong>,<br><br> Congratulations! You have successfully completed your Internship hours.<br><br>Regards, <br> Colegio De Montalban";
    
                $mail->send();
                $update_sql = "UPDATE interns SET email_sent = 1 WHERE id = " . $row['id']; 
                $mysqli_query = mysqli_query($conn, $update_sql);
            } catch (Exception $e) {
                echo "Mailer Error ({$full_name}): {$mail->ErrorInfo}<br>";
            }
        }

        $editButton = '<button onclick="editIntern(' . $row['id'] . ')" style="cursor:pointer; border:none; background:none;">
                        <img src="uploads/edit.png" alt="Edit" width="15" height="15" style="display:flex; ">
                      </button>';
        $deleteButton = '<button onclick="showModal(\'' . addslashes($row['fname']) . '\', \'' . addslashes($row['lname']) . '\')" style="cursor:pointer; border:none; background:none;">
                      <img src="uploads/trash.png" alt="Delete" width="19" height="16" style="display:flex;  ">
                    </button>';
        $viewButton = '<button onclick="viewButton(' . $row['id'] . ', \'' . addslashes($full_name) . '\')" style="cursor:pointer; border:none; background:none;">
                    <img src="uploads/eye.png" alt="View" width="17" height="17" style="display:flex;">
                </button>';
                

    
        
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$full_name}</td>
                <td>{$row['gmail']}</td>
                <td>{$row['department']}</td>
                <td>{$row['date_started']}</td>
                <td>{$row['required_hours']}</td>
                <td>{$hours_remaining}</td>
                <td>{$status}</td>
                <td> {$viewButton} {$editButton} {$deleteButton} </td>
              </tr>";
              ob_flush();
              flush();

    }
} else {
    echo "<tr><td colspan='8'>No interns found.</td></tr>";
}


mysqli_close($conn);



?>
