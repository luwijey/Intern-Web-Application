<?php
include 'connection.php';
require 'face_recognition.php'; // Face comparison function

header("Content-Type: application/json");
date_default_timezone_set("Asia/Manila");

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data["descriptor"]) || empty($data["descriptor"])) {
    echo json_encode(["status" => "error", "message" => "❌ No face data received."]);
    exit;
}

$inputDescriptor = json_decode($data["descriptor"], true);
if (!is_array($inputDescriptor)) {
    echo json_encode(["status" => "error", "message" => "❌ Invalid face format."]);
    exit;
}

// Fetch all registered faces
$sql = "SELECT id, fname, lname, department, face_descriptor FROM interns";
$result = $conn->query($sql);

$bestMatch = null;
$minDistance = 0.35;

while ($row = $result->fetch_assoc()) {
    $storedDescriptor = json_decode($row["face_descriptor"], true);
    if (!is_array($storedDescriptor)) continue;

    // Compare face descriptors
    $distance = euclideanDistance($inputDescriptor, $storedDescriptor);
    if ($distance < $minDistance) {
        $minDistance = $distance;
        $bestMatch = $row;
    }
}

if ($bestMatch) {
    $internId = $bestMatch["id"];
    $name = $bestMatch["fname"] . " " . $bestMatch["lname"];
    $department = $bestMatch["department"];
    
    $formattedDate = date("m/d/Y");
    $formattedTime = date("h:i A");
    $timestamp = date("Y-m-d H:i:s");

    // Check if the intern already timed in
    $checkSql = "SELECT * FROM attendance WHERE intern_id = ? AND formatted_date = ? LIMIT 1";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("is", $internId, $formattedDate);
    $stmt->execute();
    $attendanceResult = $stmt->get_result();
    $stmt->close();

    if ($attendanceResult->num_rows > 0) {
        // Already timed in, check if time-out exists
        $row = $attendanceResult->fetch_assoc();
        if ($row["time_out"] === null) {
            // Ask for time-out confirmation
            echo json_encode([
                "status" => "confirm",
                "confirm_needed" => true,
                "message" => "⚠️ " . $name . " You are already timed in at " . $row["time_in"] . ". Do you want to time out now?"
            ]);
        } else {
            echo json_encode([
                "status" => "success",
                "message" => "✅ " . $name . " you already logged out at " . $row["time_out"]
            ]);
        }
    } else {
        // Insert new time-in record
        $insertSql = "INSERT INTO `attendance` (`intern_id`, `name`, `department`, `time_in`, `formatted_date`, `timestamp`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("isssss", $internId, $name, $department, $formattedTime, $formattedDate, $timestamp);
        $stmt->execute();
        $stmt->close();

        
        echo json_encode([
            "status" => "success",
            "message" => "✅ Time-in recorded for $name at $formattedTime"
        ]);
    }
    
    // Handle time-out confirmation
    if (isset($data["confirmTimeout"]) && $data["confirmTimeout"] === true) {
        // Get the time-in record
        $checkSql = "SELECT time_in FROM attendance WHERE intern_id = ? AND formatted_date = ? LIMIT 1";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("is", $internId, $formattedDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $timeIn = DateTime::createFromFormat("h:i A", $row["time_in"]);
            $timeOut = DateTime::createFromFormat("h:i A", $formattedTime);
            $hoursWorked = round(($timeOut->getTimestamp() - $timeIn->getTimestamp()) / 3600, 2);

            // Update time-out and hours_completed
            $updateSql = "UPDATE attendance 
            SET time_out = ?, hours_completed = ? 
            WHERE intern_id = ? AND formatted_date = ? 
            ORDER BY id DESC LIMIT 1"; 

            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("sdsi", $formattedTime, $hoursWorked, $internId, $formattedDate);
            $stmt->execute();
            $stmt->close();

            echo json_encode([
                "status" => "success",
                "message" => "✅ Time-out recorded for $name at $formattedTime. Hours completed: $hoursWorked"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "❌ No time-in record found for today."
            ]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "❌ Face not recognized."]);
}

$conn->close();
?>
