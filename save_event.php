<?php
session_start();
print_r($_SESSION);
$servername = "localhost";
$username = "localhost";
$password = "NAVneet345@";
$dbname = "myForm";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Connection failed: " . $conn->connect_error;
    exit;
}

// Ensure user is logged in
$userEmail = $_SESSION['email'] ?? '';
if (!$userEmail) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

// Sanitize input
$eventTitle = $conn->real_escape_string($_POST['event_title'] ?? '');
$eventDate = $conn->real_escape_string($_POST['event_date'] ?? '');

if (!$eventTitle || !$eventDate) {
    http_response_code(400);
    echo "Missing event title or date.";
    exit;
}

$sql = "INSERT INTO events (event_title, startdate ,enddate , user_email ,adddate)
        VALUES ('$eventTitle', '$eventDate','$eventDate', '$userEmail',NOW())";

if ($conn->query($sql) === TRUE) {
    echo "Event saved.";
} else {
    http_response_code(500);
    echo "Error: " . $conn->error;
}
?>