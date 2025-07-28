<?php
$localhost = "localhost";
$user = "root";
$password = ""; 
$database = "crud";

$connection = mysqli_connect($localhost, $user, $password, $database);
if ($connection) {
   echo "Connection successful";
}
else {
   die("Error ");
}
?>
