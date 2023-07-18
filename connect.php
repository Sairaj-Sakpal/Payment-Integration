<?php 

include("razorpay-php/Razorpay.php");
$servername = "YOUR SERVERNAME";
$username = "YOUR USERNAME";
$password = "YOUR DB PASSWORD";
$dbname = "YOUR DB NAME";

  $conn = new mysqli($servername, $username, $password,$dbname);  
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
?>
