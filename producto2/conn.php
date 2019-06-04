<?php
// Connection variables
$dbhost	= "localhost";	   // localhost or IP
$dbuser	= "root";		  // database username
$dbpass	= "";		     // database password
$dbname	= "producto2";    // database name

//exception handling
/*mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
set_exception_handler(function($e) {
  error_log($e->getMessage());
  exit('Error connecting to database'); //Should be a message a typical user could understand
});*/

// Create connection
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
$conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

?>