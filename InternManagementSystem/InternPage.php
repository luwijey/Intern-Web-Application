<?php
include 'connection.php';


if (!isset($_SESSION['intern_id'])) {
    header("Location: InternLogin.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$intern_id = $_SESSION['intern_id'];

//attendance records with date_started and required_hours
$query = "SELECT a.id, a.name, a.department, a.time_in, 
          IFNULL(a.time_out, '0') AS time_out,
          a.formatted_date, 
          IFNULL(a.hours_completed, '0') AS hours_completed, 
                 i.date_started, i.required_hours 
          FROM attendance a
          JOIN interns i ON a.intern_id = i.id 
          WHERE a.intern_id = ? 
          ORDER BY a.formatted_date ASC";


// Calculate total hours
$totalHoursQuery = "SELECT COALESCE(SUM(a.hours_completed), 0) AS total_hours FROM attendance a WHERE a.intern_id = ?";
$totalStmt = $conn->prepare($totalHoursQuery);
$totalStmt->bind_param("i", $intern_id);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalStmt->close();

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $intern_id);
$stmt->execute();
$result = $stmt->get_result();

// fetch name 
$sql = "SELECT name FROM attendance WHERE intern_id = ? GROUP BY name";
$nameStmt = $conn->prepare($sql);
if (!$nameStmt) {
    die("Query preparation failed: " . $conn->error);
}
$nameStmt->bind_param("i", $intern_id);
$nameStmt->execute();
$nameResult = $nameStmt->get_result();

$name = "Intern"; // Default if no name is found


if ($nameResult && mysqli_num_rows($nameResult) > 0) {
    $row = mysqli_fetch_assoc($nameResult);
    $name = $row['name'];
}

// Close statements
$nameStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Dashboard</title>
    <link rel="stylesheet" href="./styles.css">
    <script defer src="internAtt.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>
    <div class ="intern-container">
        <div class = "header">
            <h1>Welcome to your attendance history <?php echo htmlspecialchars($name);?> !</h1>
        </div>

        <div id ="historyTable" style="overflow-y:scroll; overflow-x: hidden;">
            <table>
                <thead style >
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Time in</th>
                        <th>Time out</th>
                        <th>Date</th>
                        <th>Hours Completed</th>
                    </tr>
                </thead>

                <tbody id="history-body">
                    <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td><?php echo htmlspecialchars($row['time_in']); ?></td>
                            <td><?php echo htmlspecialchars($row['time_out']); ?></td>
                            <td><?php echo htmlspecialchars($row['formatted_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['hours_completed']); ?></td>
                        </tr>
                    <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="color:black; text-align: center;">No Attendance found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        
                <div class="status">
                    <?php 
                        $query = "SELECT  a.id, COALESCE(SUM(a.hours_completed), 0) AS hours_completed, 
                        i.date_started, i.required_hours 
                        FROM attendance a
                        JOIN interns i ON a.intern_id = i.id 
                        WHERE a.intern_id = ? ";

                        $statusstmt = $conn->prepare($query);
                        $statusstmt->bind_param("i", $intern_id);
                        $statusstmt->execute();
                        $status_result = $statusstmt->get_result();

                    
                        if ($row = mysqli_fetch_assoc($status_result)) {
                            $hours_remaining = $row['required_hours'] - $row['hours_completed'];

                            if ($hours_remaining > 0 ) {
                            $status = "<span style='color:blue;'>Active</span>";
                            } elseif ($hours_remaining <= 0 ) {
                                $status = "<span style='color:green;'>Completed</span>";
                            } else {
                                $status = "<span style='color:gray;'>Inactive</span>";
                            }

                            echo "<p class='userStatus'>Required Hours: " . htmlspecialchars($row['required_hours']) . "</p>";
                            echo "<p class='userStatus'>Completed Hours: " . htmlspecialchars($row['hours_completed']) . "</p>";
                            echo "<p class='userStatus'>Hours Remaining: " . htmlspecialchars($hours_remaining) . "</p>";
                            echo "<p class='userStatus'>Status: "  . $status . "</p>";
                            

                        } else {
                            echo "<p >No Record Found</p>";
                        }
                        $statusstmt->close();
                    
                    ?>
                    
                </div>
            </div>
            <div class="buttons">
                <button id="downloadbtn" style="margin-right:15px;">Download Attendance</button>
                <button id="downloadbtn" style="margin-left:15px;" onclick="window.location.href='InternChangePassword.php'" >Change Password</button>
                <button onclick="openModal()">Logout</button>
            </div>
            <!-- Logout Confirmation Modal -->
            <div id="internLogoutModal" class="modal-container" style="display:none;">
                <div class="modal-content">
                    <h3 style="color:red;">Confirm Logout</h3>
                    <p style="font-weight:bold;">Are you sure you want to log out?</p>
                    <button onclick="window.location.href='internlogout.php'" style="background-color: red; margin:5px; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Confirm</button>
                    <button id="cancelLogout" onclick="closeModal()" style="background-color: gray; margin:5px; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                </div>
            </div>
        </div>  
    </div>        
</body>
   <script>
        function openModal() { 
            document.getElementById("internLogoutModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("internLogoutModal").style.display = "none";
        }

        window.onclick = function(event) {
            let modal = document.getElementById("internLogoutModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        window.onpageshow = function(event) {
      if (event.persisted) {
          window.location.href = "InternLogin.php";
      }
  };
   </script>

<style>
    .modal-container {
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }
    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    }
    .close {
        float: right;
        font-size: 20px;
        cursor: pointer;
    }
     .buttons{
       padding:30px;
    }

    .buttons button{
        background-color: #3D8D7A;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        margin-top: auto;
        align-self: center;
    }

    .status {
        display:flex;
        flex-direction:column;
        text-align:center;
        margin-top:15px;
        margin:0 auto;
        padding:10px;
        border-radius:10px;
        background:rgb(178, 211, 203);
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        width: fit-content;
    }
    .status p {
        font-size: 16px;
        font-weight: bold;
        margin: 5px 0;
        color: #333;
    }
    
    
    body{
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .intern-container{
        display:flex;
        flex-direction:column;
        width: 60%;
        height:90vh;
        padding: 20px;
        background: #e8f5e9;
        border-radius: 10px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);

        text-align: center;
        
        
    }
    .header {
        background: #3D8D7A;
        color: white;
        font-weight: bold;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    .header h1{
        margin: 0;
        font-size:30px;
    }
    .history-table{
        flex-grow: 1;
        overflow-y: auto;
        position: relative;
    }
    table {
        width: 99%;
        border-collapse: collapse;
    }
    tbody {
        font-weight: bold;
        height:30em;
        
    }
    thead {
        background-color: #3D8D7A;
        color: white;
        font-weight: bold;
        position: sticky;
        top: 0;
}

    tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }
    th {
        background-color: #3D8D7A;
        color: white;
        display: table-cell;
        vertical-align: inherit;
        font-weight: bold;
        text-align:center;
        unicode-bidi: isolate;
    }
    th, td {
        border: 1px solid #3D8D7A;
        padding: 10px;
        text-align: center;
    }

</style>

</html>
