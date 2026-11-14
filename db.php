<?php
// Lesson 1: variables start with $ and use underscores for multiple words ($db_host)
$db_host = "localhost";
$db_user = "root";   // default XAMPP user
$db_pass = "";       // default XAMPP password is empty
$db_name = "serrano_garage";

mysqli_report(MYSQLI_REPORT_OFF);   // so we can handle errors ourselves with if/else

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Lesson 2: if statement
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);   // Lesson 1: "." joins strings
}

$conn->set_charset("utf8mb4");
