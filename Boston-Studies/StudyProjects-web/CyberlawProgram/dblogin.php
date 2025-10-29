<?php
try {
    $servername = "localhost";
    $username = "root";
    $password = "mysql";
    $database_name = "olms";
    $conn = mysqli_connect($servername, $username, $password, $database_name);
    if ($conn === false) {
        throw new Exception("ERROR: Could not connect. " . mysqli_connect_error());
    }
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    echo "Connection failed. Please try again later.";
    exit();
}
// if($conn === false){
//     die("ERROR: Could not connect. " . mysqli_connect_error());
// }else
// {
// echo "Connected Successfully. Host info: " . mysqli_get_host_info($conn) . "<br>";
// }