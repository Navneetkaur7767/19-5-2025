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

//Ensure user is logged in
$userEmail=$_SESSION['email']??'';
if(!userEmail)
{   http_response_code(401);
    echo "Unauthorized";
    exit();
}

// if user is logged in we will now get the event id 
$eventId=$_POST['event_Id'] ?? '';
$eventId=int($eventId);    //to make sure the event id is in div

// now check if event id exist or not
if(!eventId)
{
    http_response_code(400);
    echo "invalid data or missing ID";
    exit();
}

//delete event only if it belongs to logged in user
$deleteEventQuery="DELETE FROM events WHERE id=$eventId AND user_email='$userEmail'";

if($conn->query(deleteEventQuery)===TRUE)
    {
        echo "Event Deleted";
    }
else
    {
        http_response_code(500);
        echo "server error and error saving event".$conn->error;
    }

$conn->close();

?>