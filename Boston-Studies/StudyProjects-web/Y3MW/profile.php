<?php
session_start();
require_once 'function.php';
include 'dblogin.php'; // Ensure this file contains the correct database connection setup

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.html");
    exit();
}

$isUpdating = true;
$updateUserId = $_SESSION['user_id'];

// Fetch user data to prefill the form
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $updateUserId);
$stmt->execute();
$userResult = $stmt->get_result();
if ($userResult->num_rows > 0) {
    $userData = $userResult->fetch_assoc();
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input
    $username = validate($_POST['username']);
    $name = validate($_POST['name']);
    $surname = validate($_POST['surname']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']); // Consider hashing the password
    $cell = validate($_POST['cell']);
    $street_address = validate($_POST['street_address']);
    $province = validate($_POST['province']);
    $postcode = validate($_POST['postcode']);

    // Updates the user's profile
    $updateQuery = "UPDATE users SET username = ?, name = ?, surname = ?, email = ?, password = ?, cell = ?, street_address = ?, province = ?, postcode = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssssssi", $username, $name, $surname, $email, $password, $cell, $street_address, $province, $postcode, $updateUserId);
    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully');</script>";
        header("Location: profile.php");
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>User Profile</title>
<link rel="stylesheet" href="styles.css">
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000000;
            background-image: url('prity/register_back.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: scroll;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
        }
        .signup {
            border: none; /* Removed the border */
            font: Arial, 14px helvetica;
            color: #444444;
        }
        table {
            width: 860px;
            margin: 100px auto;
            padding: 20px;
            background-color: rgba(47, 196, 163, 0.8); /* Increased opacity for better visibility */
            border: none; /* Removed the border */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th {
            text-align: center;
            color: white;
            padding: 10px;
            border-bottom: none; /* Removed the border */
        }
        td {
            padding: 10px;
            border-bottom: none; /* Removed the border */
            color: white;
            font-weight: bold;
        }
        input, select {
            background-color: rgba(0, 0, 0, 0.7); /* Darker background for inputs */
            color: rgba(255, 255, 255, 0.9); /* Brighter text color */
            font-weight: bold;
            margin-bottom: 10px;
            padding: 10px;
            border: none; /* Removed the border */
            box-sizing: border-box;
        }
        button {
        width: 100%;
        padding: 10px;
        background-color: #c9df0494;
        border: none;
        color: white;
        cursor: pointer;
        font-size: medium;
        font-weight: bold;
        }
        button:hover {
        background-color: #00d300a9;
        color: white;
        font-weight: bold;
        }
        .error {
            color: rgb(255, 0, 234);
            margin-bottom: 10px;
            font-weight: bold;
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
<script>
function validateName(name) {
    if (name === "") return "No name was entered.\n";
    return "";
}

function validateSurname(surname) {
    if (surname === "") return "No surname was entered.\n";
    return "";
}

function validateUsername(username) {
    if (username === "") return "No username was entered.\n";
    return "";
}

function validatePassword(password) {
    if (password === "") return "No password was entered.\n";
    else if (password.length < 6) return "Passwords must be at least 6 characters.\n";
    return "";
}

function validateEmail(email) {
    if (email === "") return "No email was entered.\n";
    else if (!((email.indexOf(".") > 0) && (email.indexOf("@") > 0)) || /[^a-zA-Z0-9.@_-]/.test(email))
        return "The Email address is invalid.\n";
    return "";
}

function validatePostcode(postcode) {
    if (postcode === "");
    else if (!/^[0-9]{4}$/.test(postcode)) return "Invalid Post Code, 4 digits required.\n";
    return "";
}

function validateCell(cell) {
    if (cell === "");
    else if (!/^[0-9]{10}$/.test(cell)) return "Invalid cell phone number, 10 digits required.\n";
    return "";
}

function validate(form) {
    let fail = "";
    fail += validateName(form.name.value);
    fail += validateSurname(form.surname.value);
    fail += validateUsername(form.username.value);
    fail += validatePassword(form.password.value);
    fail += validateEmail(form.email.value);
    fail += validatePostcode(form.postcode.value);
    fail += validateCell(form.cell.value);
    if (fail === "") return true;
    else {
        alert(fail);
        return false;
    }
}
</script>
</head>
<body>
<?php top_bar(); ?>
           
<table class="signup" border="0" cellpadding="4" cellspacing="5" bgcolor="#eeeeee">
    <th colspan="4" align="center">User Profile Form</th>
    <form method="post" id="register" onsubmit="return validate(this)">
        <input type="hidden" name="user_id" id="user_id" value="<?php echo $userData['user_id']; ?>">
        <tr>
            <td>Name:</td>
            <td><input type="text" maxlength="100" name="name" id="name" value="<?php echo $userData['name']; ?>"></td>
            <td>Surname:</td>
            <td><input type="text" maxlength="100" name="surname" id="surname" value="<?php echo $userData['surname']; ?>"></td>
        </tr>
        <tr>
            <td>Username:</td>
            <td><input type="text" maxlength="50" name="username" id="username" value="<?php echo $userData['username']; ?>"></td>
            <td>Password:</td>
            <td><input type="password" maxlength="20" name="password" id="password"></td>
        </tr>
        <tr>
            <td>Email:</td>
            <td><input type="text" maxlength="255" name="email" id="email" value="<?php echo $userData['email']; ?>"></td>
        </tr>
        <th colspan="4" align="center">Shipping Details</th>
        <tr>
            <td>Cellphone:</td>
            <td><input type="text" id="cell" name="cell" value="<?php echo $userData['cell']; ?>"></td>
            <td>Street Address:</td>
            <td><input type="text" maxlength="255" name="street_address" id="street_address" value="<?php echo $userData['street_address']; ?>"></td>
        </tr>
        <tr>
            <td>Province:</td>
            <td>
                <select id="province" name="province">
                    <option value="NONE-SELECTED" <?php echo $userData['province'] === 'NONE-SELECTED' ? 'selected' : ''; ?>>NONE-SELECTED</option>
                    <option value="EASTERN CAPE" <?php echo $userData['province'] === 'EASTERN CAPE' ? 'selected' : ''; ?>>Eastern Cape</option>
                    <option value="FREE STATE" <?php echo $userData['province'] === 'FREE STATE' ? 'selected' : ''; ?>>Free State</option>
                    <option value="GAUTENG" <?php echo $userData['province'] === 'GAUTENG' ? 'selected' : ''; ?>>Gauteng</option>
                    <option value="KWAZULU-NATAL" <?php echo $userData['province'] === 'KWAZULU-NATAL' ? 'selected' : ''; ?>>KwaZulu-Natal</option>
                    <option value="MPUMALANGA" <?php echo $userData['province'] === 'MPUMALANGA' ? 'selected' : ''; ?>>Mpumalanga</option>
                    <option value="NORTHERN CAPE" <?php echo $userData['province'] === 'NORTHERN CAPE' ? 'selected' : ''; ?>>Northern Cape</option>
                    <option value="LIMPOPO" <?php echo $userData['province'] === 'LIMPOPO' ? 'selected' : ''; ?>>Limpopo</option>
                    <option value="NORTH WEST" <?php echo $userData['province'] === 'NORTH WEST' ? 'selected' : ''; ?>>North West</option>
                    <option value="WESTERN CAPE" <?php echo $userData['province'] === 'WESTERN CAPE' ? 'selected' : ''; ?>>Western Cape</option>
                </select>
            </td>
            <td>Postcode:</td>
            <td><input type="text" id="postcode" name="postcode" value="<?php echo $userData['postcode']; ?>"></td>
        </tr>
        <tr>
            <td colspan="4" align="center">
                <button type="submit" id="submit-button">Update Profile</button>
            </td>
        </tr>
    </form>
</table>
</body>
</html>
