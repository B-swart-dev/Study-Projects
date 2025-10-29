<?php
session_start();
include 'function.php';
$servername = "localhost";
$username = "root";
$password = "mysql";
$database = "olms";

$conn = new mysqli($servername, $username, $password, $database);
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'user') {
    header("Location: login_index.html");
    exit();
}

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {
        $username = $_POST['username'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $email = $_POST['email'];
        
        // Check if the username already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>alert('Username already exists. Please choose a different username.');</script>";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, email, name, surname) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $username, $password, $role, $email, $name, $surname);
            if ($stmt->execute()) {
                echo "<script>alert('Registration successful.');</script>";
            } else {
                throw new Exception("Registration failed");
            }
        }
        $stmt->close();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
        $userId = $_POST['userId'];
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            echo "<script>alert('User deleted successfully.');</script>";
        } else {
            throw new Exception("Failed to delete user");
        }
        $stmt->close();
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo "<script>alert('An error occurred. Please try again later.');</script>";
}


?>


<!DOCTYPE html>
<html>
<head>
    <title>User Creation</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* User Creation Screen */
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3; 
            /* https://www.wallpaperflare.com/black-wooden-ladder-beside-brown-wooden-bookshelf-library-step-wallpaper-azcer */
            background-image: url("background.jpg");
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        label, input, select {
            display: block;
            margin-bottom: 10px;
            width: 100%;
        }
        button {
            width: 40%;
            padding: 10px;
            background-color: #8C2D38;
            border: none;
            color: white;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover {
            background-color: #00d300;
            color: white;
            font-weight: bold;
        }
        .error {
            color: rgb(255, 0, 0);
            margin-bottom: 10px;
            font-weight: bold;
        }
        .user {
            width: 600px;
            margin: 100px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #c4c0c0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .form-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        .form-container .column {
            width: 45%;
        }
        /* table customization */
        .table-container {
            width: 80%;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #c4c0c0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-button {
            background-color: #8C2D38;
            border: none;
            color: white;
            padding: 5px 10px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #d9534f;
        }
    </style>
</head>
<body>
<?php topbar(); ?>

    <form id="user" method="POST" action="user_creation.php">
        <div class="user">
            <div class="form-container">
                <div class="column">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="column">
                    <label for="password">Password:</label>
                    <input type="text" id="password" name="password" required>
                    <label for="surname">Surname:</label>
                    <input type="text" id="surname" name="surname" required>
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="action" value="register">Register User</button>
        </div>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM users");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['user_id']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['surname']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['role']}</td>
                            <td>
                                <form method='POST' action='user_creation.php' style='display:inline;'>
                                    <input type='hidden' name='userId' value='{$row['user_id']}'>
                                    <input type='hidden' name='action' value='delete'>
                                    <button type='submit' class='delete-button'>Delete</button>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<script>
    document.getElementById("btnlogout").addEventListener("click", function() {
        var confirmation = confirm('Are you sure you want to logout?\n\nClick "Ok" to logout or "Cancel" to cancel.');
    if (confirmation) {
        window.location.href = "login_index.html";
    }
    });
</script>