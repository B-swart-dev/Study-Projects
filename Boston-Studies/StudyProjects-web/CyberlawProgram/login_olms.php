<?php
session_start();
include "dblogin.php";
header('Content-Type: application/json');

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = validate($_POST['user']);
    $pass = validate($_POST['password']);

    if (empty($user) && empty($pass)) {
        echo json_encode(['success' => false, 'message' => 'Username and Password are required']);
        exit();
    } elseif (empty($user)) {
        echo json_encode(['success' => false, 'message' => 'Username is required']);
        exit();
    } elseif (empty($pass)) {
        echo json_encode(['success' => false, 'message' => 'Password is required']);
        exit();
    }

    // Check if the input is email or username
    if (filter_var($user, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    } else {
        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    }

    // Prepare the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("ss", $user, $pass);
        // Execute the statement
        $stmt->execute();
        // Get the result
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (($row['username'] === $user || $row['email'] === $user) && $row['password'] === $pass) {
                $_SESSION['username'] = $row["username"];
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['surname'] = $row['surname'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['random_code'] = md5(uniqid(rand(), true)); // Generate MD5 random code
                
                echo json_encode(['success' => true, 'random_code' => $_SESSION['random_code']]);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Incorrect Username, Email, or Password']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect Username, Email, or Password']);
            exit();
        }

        // Close the statement
        $stmt->close();
    } else {
        error_log("Database query failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database query failed']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}