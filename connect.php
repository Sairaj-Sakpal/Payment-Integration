<?php 

include("razorpay-php/Razorpay.php");
$servername = "217.21.91.154";
$username = "u447307974_payment";
$password = "Payment@8652";
$dbname = "u447307974_integration";

  $conn = new mysqli($servername, $username, $password,$dbname);  
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

	// $conn = mysqli_connect('localhost', 'root', '', 'consulting');

	// if ($conn) {
	// 	echo"Connected";
	// }else{
	// 	echo"Not connected";
	// }
?>