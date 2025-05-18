<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['fname']) && isset($_POST['lname'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);

    // Check if the intern exists
    $check_sql = "SELECT id FROM interns WHERE fname = ? AND lname = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "ss", $fname, $lname);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $intern_id = $row['id'];

        // Delete attendance records first if necessary (optional)
        $delete_attendance = "DELETE FROM attendance WHERE intern_id = ?";
        $stmt = mysqli_prepare($conn, $delete_attendance);
        mysqli_stmt_bind_param($stmt, "i", $intern_id);
        mysqli_stmt_execute($stmt);

        $delete_account = "DELETE FROM intern_account WHERE intern_id = ?";
        $stmt = mysqli_prepare($conn, $delete_account);
        mysqli_stmt_bind_param($stmt, "i", $intern_id);
        mysqli_stmt_execute($stmt);

        // Delete the intern
        $delete_sql = "DELETE FROM interns WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "i", $intern_id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete intern."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Intern not found."]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

mysqli_close($conn);
?>
