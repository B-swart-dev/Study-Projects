<?php
session_start();
include "dblogin.php";
header('Content-Type: application/json');
// $servername = "localhost";
// $username = "root";
// $password = "mysql";
// $database_name = "MusicW";
// $conn = mysqli_connect($servername, $username, $password, $database_name);

// if($conn === false){
//     die("ERROR: Could not connect. " . mysqli_connect_error());
// }else
// {
// echo "Connected Successfully. Host info: " . mysqli_get_host_info($conn) . "<br>";
// }
//if(isset($_POST['user']) && isset($_POST['password'])) {
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
//}

//$user = validate($_POST['user']);
//$pass = validate($_POST['password']);

// if(empty($user)) {
//     header("Location: login_index.php?erro=User Name or Email is required");
//     exit();
// } else if(empty($pass)) {
//     header("Location: login_index.php?erro=Password is required");
//     exit(); 
// }
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = validate($_POST['user']);
    $pass = validate($_POST['password']);

    // if (empty($user) && empty($pass)) {
    //     echo json_encode(['success' => false, 'message' => 'Username and Password are required']);
    //      // header("Location: login_index.php?error=Username and Password are required");
    //     exit();
    // } elseif (empty($user)) {
    //     echo json_encode(['success' => false, 'message' => 'Username is required']);
    //     // header("Location: login_index.php?error=Username is required");
    //     exit();
    // } elseif (empty($pass)) {
    //     echo json_encode(['success' => false, 'message' => 'Password is required']);
    //     // header("Location: login_index.php?error=Password is required");
    //     exit();
    // }

    // Check if the input is email or username
    if (filter_var($user, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    } else {
        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    }

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $user, $pass);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (($row['username'] === $user || $row['email'] === $user) && $row['password'] === $pass) {
            // echo "Loggin In!";   
                $_SESSION['username'] = $row["username"];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['surname'] = $row['surname'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['user_role'] = $row['role'];
                error_log($row['role']);
                //header("Location: main_page.php");
                // https://www.w3schools.com/php/func_json_encode.asp
                echo json_encode(['success' => true]);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Incorrect Username, Email, or Password']);
                //header("Location: login_index.html?error=Incorrect Username, Email, or Password");
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect Username, Email, or Password']);
            //header("Location: login_index.html?error=Incorrect Username, Email, or Password");
            exit();
        }

        // Close the statement
        $stmt->close();
    } else {
    // header("Location: login_index.html");
        error_log("Database query failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database query failed']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}