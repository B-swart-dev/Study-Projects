<?php
/* I am just adding this to show how i tested it working
INSERT INTO users (username, password, email) VALUES
('user_being_deleted', 'password1', 'userd@example.com'),
('user_update', 'password1', 'useru@example.com');
*/
session_start();

$servername = "localhost";
$username = "root";
$password = "mysql";
$database_name = "database_name";

$conn = mysqli_connect($servername, $username, $password, $database_name);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}else
{
echo "Connected Successfully. Host info: " . mysqli_get_host_info($conn) . "<br>";
}

// Insertion: This is adding a new user record into the "users" table by the name of new user
$new_username = "new_user";
$new_password = "new_password";
$new_email = "new_email@example.com";

$insert_query = "INSERT INTO users (username, password, email) VALUES ('$new_username', '$new_password', '$new_email')";
if (mysqli_query($conn, $insert_query)) {
    echo "<br> " . "New record inserted successfully";
} else {
    echo "<br>" . "Error: " . $insert_query . "<br>" . mysqli_error($conn);
}

// Modification: This is updating an existing user record in the "users" table i used as an example at start
$existing_username = "user_update";
$new_email_address = "Coolemailname@example.com";

$update_query = "UPDATE users SET email = '$new_email_address' WHERE username = '$existing_username'";
if (mysqli_query($conn, $update_query)) {
    echo "<br>" . "Record updated successfully";
} else {
    echo "<br>". "Error: " . $update_query . "<br>" . mysqli_error($conn);
}

//This is the delete query it deletes a user i added at the start
$user_to_delete = "user_being_deleted";
$delete_query = "DELETE FROM users WHERE username = '$user_to_delete'";
if (mysqli_query($conn, $delete_query)) {
    echo "<br>" ."Record deleted successfully";
} else {
    echo "<br>" ."Error: " . $delete_query . "<br>" . mysqli_error($conn);
}

$_SESSION["username"] = $new_username;
echo "<br>" . "Session username: " . $_SESSION["username"];

mysqli_close($conn);
?>