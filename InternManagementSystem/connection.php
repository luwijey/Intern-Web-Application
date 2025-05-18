<?php

$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "ojt_db";

date_default_timezone_set('Asia/Manila');
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
session_start();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


?>