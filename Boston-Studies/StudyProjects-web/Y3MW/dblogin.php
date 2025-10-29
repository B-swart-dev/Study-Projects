<?php
$servername = "localhost";
$username = "root";
$password = "mysql";
$database_name = "Musicw";
$conn = mysqli_connect($servername, $username, $password, $database_name);
// if($conn === false){
//     die("ERROR: Could not connect. " . mysqli_connect_error());
// }else
// {
// echo "Connected Successfully. Host info: " . mysqli_get_host_info($conn) . "<br>";
// }
