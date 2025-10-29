<?php
session_start();
require_once 'function.php';
include 'dblogin.php'; 

function validate($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if the user is admin and can assign roles
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    if (!empty($_POST['role'])) {
        $role = validate($_POST['role']);
    }
}

$isUpdating = false;
$updateUserId = null;

if (isset($_GET['update_user_id'])) {
    $isUpdating = true;
    $updateUserId = validate($_GET['update_user_id']);

    // Fetch user data to prefill the form
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $updateUserId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    if ($userResult->num_rows > 0) {
        $userData = $userResult->fetch_assoc();
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        // Delete user
        $deleteUserId = validate($_POST['delete_user_id']);
        $deleteQuery = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $deleteUserId);
        if ($stmt->execute()) {
            echo "<script>alert('User deleted successfully');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        $username = validate($_POST['username']);
        $name = validate($_POST['name']);
        $surname = validate($_POST['surname']);
        $email = validate($_POST['email']);
        $password = validate($_POST['password']); 
        $urole = isset($_POST['role']) ? validate($_POST['role']) : 'user';
        $user_role = isset($_POST['user_role']) ? validate($_POST['user_role']) : 'customer'; 
        $cell = validate($_POST['cell']);
        $street_address = validate($_POST['street_address']);
        $province = validate($_POST['province']);
        $postcode = validate($_POST['postcode']);

        if ($urole === 'admin') {
            $user_role = 'admin';
        }

        if ($isUpdating) {
            // Updates an existing user
            $updateQuery = "UPDATE users SET username = ?, name = ?, surname = ?, email = ?, password = ?, role = ?, user_role = ?, cell = ?, street_address = ?, province = ?, postcode = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("sssssssssssi", $username, $name, $surname, $email, $password, $urole, $user_role, $cell, $street_address, $province, $postcode, $updateUserId);
            if ($stmt->execute()) {
                echo "<script>alert('User updated successfully');</script>";
                header("Location: register.php");
                exit();
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }
        } else {
            // Checks that usernames are unique
            $checkUsername = "SELECT username FROM users WHERE username = ?";
            $stmt = $conn->prepare($checkUsername);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                echo "<script>alert('Username already exists.'); setTimeout(function(){location.href='register.php'} , 2500);</script>";
                exit();
            }
            $stmt->close();

            // Insert new user into database
            $insertQuery = "INSERT INTO users (username, name, surname, email, password, role, user_role, cell, street_address, province, postcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("sssssssssss", $username, $name, $surname, $email, $password, $urole, $user_role, $cell, $street_address, $province, $postcode);
            if ($stmt->execute()) {
                echo "<script>alert('New user registered successfully');</script>";
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
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
            border: none; 
            font: Arial, 14px helvetica;
            color: #444444;
        }
        table {
            width: 860px;
            margin: 100px auto;
            padding: 20px;
            background-color: rgba(47, 196, 163, 0.8); 
            border: none; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th {
            text-align: center;
            color: white;
            padding: 10px;
            border-bottom: none; 
        }
        td {
            padding: 10px;
            border-bottom: none; 
            color: white;
            font-weight: bold;
        }
        input, select {
            background-color: rgba(0, 0, 0, 0.7); 
            color: rgba(255, 255, 255, 0.9); 
            font-weight: bold;
            margin-bottom: 10px;
            padding: 10px;
            border: none; 
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
        function resetForm() {
            document.getElementById('register').reset();
            document.getElementById('submit-button').textContent = 'Register New User';
        }

        document.querySelectorAll('.update-button').forEach(button => {
            button.addEventListener('click', function() {
                // Populate the form with user data
                const userId = this.dataset.userid;
                fetch(`get_user_data.php?user_id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('name').value = data.name;
                        document.getElementById('surname').value = data.surname;
                        document.getElementById('username').value = data.username;
                        document.getElementById('password').value = data.password;
                        document.getElementById('email').value = data.email;
                        document.getElementById('role').value = data.role;
                        document.getElementById('user_role').value = data.user_role;
                        document.getElementById('cell').value = data.cell;
                        document.getElementById('street_address').value = data.street_address;
                        document.getElementById('province').value = data.province;
                        document.getElementById('postcode').value = data.postcode;
                        document.getElementById('submit-button').textContent = 'Update User';
                    });
            });
        });

        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                // Handle delete logic
                resetForm();
            });
        });

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
    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') top_bar(); ?>
    <table class="signup" border="0" cellpadding="4" cellspacing="5" bgcolor="#eeeeee">
        <?php if (isset($_SESSION['user_id'])): ?>
            <th colspan="4" align="center">User Profile Form</th>
        <?php else: ?>
            <th colspan="4" align="center">Registration Form</th>
        <?php endif; ?> 
        <form method="post" id="register" onsubmit="return validate(this)">
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $isUpdating ? $userData['user_id'] : ''; ?>">
            <tr>
                <td>Name:</td>
                <td><input type="text" maxlength="100" name="name" id="name" value="<?php echo $isUpdating ? $userData['name'] : ''; ?>"></td>
                <td>Surname:</td>
                <td><input type="text" maxlength="100" name="surname" id="surname" value="<?php echo $isUpdating ? $userData['surname'] : ''; ?>"></td>
            </tr>
            <tr>
                <td>Username:</td>
                <td><input type="text" maxlength="50" name="username" id="username" value="<?php echo $isUpdating ? $userData['username'] : ''; ?>"></td>
                <td>Password:</td>
                <td><input type="password" maxlength="20" name="password" id="password"></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><input type="text" maxlength="255" name="email" id="email" value="<?php echo $isUpdating ? $userData['email'] : ''; ?>"></td>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                    <td><label for="role">Role:</label></td>
                    <td colspan="3">
                        <select id="role" name="role">
                            <option value="user" <?php echo $isUpdating && $userData['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo $isUpdating && $userData['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </td>
            </tr>
            <tr>
                    <td colspan="2" style="text-align: right;"><label for="user_role">User Role:</label></td>
                    <td colspan="2">
                        <select id="user_role" name="user_role">
                            <option value="customer" <?php echo $isUpdating && $userData['user_role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                            <option value="headoffice" <?php echo $isUpdating && $userData['user_role'] === 'headoffice' ? 'selected' : ''; ?>>Headoffice</option>
                            <option value="entry-clerk" <?php echo $isUpdating && $userData['user_role'] === 'entry-clerk' ? 'selected' : ''; ?>>Entry Clerk</option>
                            <option value="store-manager" <?php echo $isUpdating && $userData['user_role'] === 'store-manager' ? 'selected' : ''; ?>>Store Manager</option>
                            <option value="HR" <?php echo $isUpdating && $userData['user_role'] === 'HR' ? 'selected' : ''; ?>>HR</option>
                            <option value="admin" <?php echo $isUpdating && $userData['user_role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </td>
                <?php endif; ?>
            </tr>
            <th colspan="4" align="center">Shipping Details</th>
            <tr>
                <td>Cellphone:</td>
                <td><input type="text" id="cell" name="cell" value="<?php echo $isUpdating ? $userData['cell'] : ''; ?>"></td>
                <td>Street Address:</td>
                <td><input type="text" maxlength="255" name="street_address" id="street_address" value="<?php echo $isUpdating ? $userData['street_address'] : ''; ?>"></td>
            </tr>
            <tr>
                <td>Province:</td>
                <td>
                    <select id="province" name="province">
                        <option value="NONE-SELECTED" <?php echo $isUpdating && $userData['province'] === 'NONE-SELECTED' ? 'selected' : ''; ?>>NONE-SELECTED</option>
                        <option value="EASTERN CAPE" <?php echo $isUpdating && $userData['province'] === 'EASTERN CAPE' ? 'selected' : ''; ?>>Eastern Cape</option>
                        <option value="FREE STATE" <?php echo $isUpdating && $userData['province'] === 'FREE STATE' ? 'selected' : ''; ?>>Free State</option>
                        <option value="GAUTENG" <?php echo $isUpdating && $userData['province'] === 'GAUTENG' ? 'selected' : ''; ?>>Gauteng</option>
                        <option value="KWAZULU-NATAL" <?php echo $isUpdating && $userData['province'] === 'KWAZULU-NATAL' ? 'selected' : ''; ?>>KwaZulu-Natal</option>
                        <option value="MPUMALANGA" <?php echo $isUpdating && $userData['province'] === 'MPUMALANGA' ? 'selected' : ''; ?>>Mpumalanga</option>
                        <option value="NORTHERN CAPE" <?php echo $isUpdating && $userData['province'] === 'NORTHERN CAPE' ? 'selected' : ''; ?>>Northern Cape</option>
                        <option value="LIMPOPO" <?php echo $isUpdating && $userData['province'] === 'LIMPOPO' ? 'selected' : ''; ?>>Limpopo</option>
                        <option value="NORTH WEST" <?php echo $isUpdating && $userData['province'] === 'NORTH WEST' ? 'selected' : ''; ?>>North West</option>
                        <option value="WESTERN CAPE" <?php echo $isUpdating && $userData['province'] === 'WESTERN CAPE' ? 'selected' : ''; ?>>Western Cape</option>
                    </select>
                </td>
                <td>Postcode:</td>
                <td><input type="text" id="postcode" name="postcode" value="<?php echo $isUpdating ? $userData['postcode'] : ''; ?>"></td>
            </tr>
            <tr>
                <td colspan="4" align="center">
                    <button type="submit" id="submit-button">
                        <?php echo $isUpdating ? 'Update User' : 'Register New User'; ?>
                    </button>
                </td>
            </tr>
        </form>
    </table>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
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
                        <th>User Role</th>
                        <th>Cell Number</th>
                        <th>Province</th>
                        <th>Address</th>
                        <th>Postcode</th>
                        <th>Actions</th>
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
                                <td>{$row['user_role']}</td>
                                <td>{$row['cell']}</td>
                                <td>{$row['province']}</td>
                                <td>{$row['street_address']}</td>
                                <td>{$row['postcode']}</td>
                                <td>
                                    <form method='get'>
                                        <input type='hidden' name='update_user_id' value='{$row['user_id']}'>
                                        <button type='submit' class='update-button' data-userid='{$row['user_id']}'>Update</button>
                                    </form>
                                    <form method='post' onsubmit='return confirm(\"Are you sure you want to delete this user?\");'>
                                        <input type='hidden' name='delete_user_id' value='{$row['user_id']}'>
                                        <button type='submit' name='delete' class='delete-button'>Delete</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>