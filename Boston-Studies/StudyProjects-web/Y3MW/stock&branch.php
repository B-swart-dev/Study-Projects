<?php
session_start();
require_once 'function.php';
$servername = "localhost";
$username = "root";
$password = "mysql";
$database = "MusicW";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.html");
    exit();
}
if ($_SESSION['role'] === 'user') {
    header("Location: login_index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addbranch') {
    $branchname = $_POST['branch'];
    $location = $_POST['location'];
    $province = $_POST['province'];
    
// Checks if the branch already exists
$stmt = $conn->prepare("SELECT branch_id FROM branch WHERE name = ?");
$stmt->bind_param("s", $branchname);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('Branch already exists. Please choose a different branch name.');</script>";
} else {
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO branch (name, location, province) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $branchname, $location, $province);
    if ($stmt->execute()) {
        echo "<script>alert('Branch added successful.');</script>";
    } else {
        echo "<script>alert('Adding Branch has failed.');</script>";
    }
}
$stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "update_branch") {
    $branch_id = $_POST["branch_id"];
    $branch_name = $_POST["branch_name"];
    $province = $_POST["province"];
    $location = $_POST["location"];
    
    $update_branch_sql = "UPDATE Branch SET name = ?, province = ?, location = ? WHERE branch_id = ?";
    $stmt = $conn->prepare($update_branch_sql);
    $stmt->bind_param("sssi", $branch_name, $province, $location, $branch_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Branch updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating branch: " . $stmt->error . "');</script>";
    }
    
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "add_stock") {
    $branch_id = $_POST["branch_id"];
    $album_id = $_POST["album_id"];
    $quantity = $_POST["quantity"];
    
    $insert_stock_sql = "INSERT INTO Stock_Item (branch_id, album_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_stock_sql);
    $stmt->bind_param("iii", $branch_id, $album_id, $quantity);
    
    if ($stmt->execute()) {
        echo "<script>alert('Stock added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding stock: " . $stmt->error . "');</script>";
    }
    
    $stmt->close();
}

$branch_id = isset($_POST['branch_id']) ? $_POST['branch_id'] : 1;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $branch_Id = $_POST['branch_Id'];
    
    $stmt = $conn->prepare("DELETE FROM branch WHERE branch_id = ?");
    $stmt->bind_param("i", $branch_Id);
    if ($stmt->execute()) {
        echo "<script>alert('Branch removed successfully.');</script>";
    } else {
            echo "<script>alert('Failed to remove branch. Error: ".$conn->error."');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock and Branch Management</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: large;
            font-weight: bold;
            background-color: #000000;
            background-image: url('prity/creator.jpg');
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            margin-top: 70px;
            padding: 0;
        }

        .form-container, .table-container {
            margin: 20px;
            padding: 20px;
            background-color: rgba(47, 196, 163, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            color: white;
            font-weight: bold;
        }

        select, input, button {
            padding: 10px;
            margin: 5px;
        }

        .admin-only {
            display: <?php echo ($_SESSION['role'] === 'admin' || $_SESSION['user_role'] === 'headoffice') ? 'block' : 'none'; ?>;
        }
        select {
            margin-top: 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            text-align: center;
            background-color: #000000;
            color: white;
            font-size: medium;
            font-weight: bold;
            width: 30%;
        }

        select option {
            background-color: #333;
            color: white;
        }
        /* https://developer.mozilla.org/en-US/docs/Web/CSS/box-shadow */
        select:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(81, 203, 238, 1);
            border: 1px solid rgba(81, 203, 238, 1);
        }
    </style>
</head>
<>
<?php top_bar(); ?>
<div class="form-container">
    <form method="POST" action="stock&branch.php">
        <label for="branchSelector">Select Branch:</label>
        <select id="branchSelector" name="branch_id" onchange="this.form.submit()">
            <?php
            $branches = $conn->query("SELECT branch_id, name FROM Branch");
            while ($branch = $branches->fetch_assoc()) {
                $selected = ($branch_id == $branch['branch_id']) ? 'selected' : '';
                echo "<option value='{$branch['branch_id']}' {$selected}>{$branch['name']}</option>";
            }
            ?>
        </select>
    </form>
</div>

<div class="table-container">
    <h1>Stock Items</h1>
    <table>
        <thead>
            <tr>
                <th>Stock Item ID</th>
                <th>Album ID</th>
                <th>Album Title</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stock_items = $conn->prepare("SELECT Stock_Item.*, album.title FROM Stock_Item 
                JOIN album ON Stock_Item.album_id = album.album_id WHERE Stock_Item.branch_id = ?");
            $stock_items->bind_param("i", $branch_id);
            $stock_items->execute();
            $result = $stock_items->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['stock_item_id']}</td>";
                    echo "<td>{$row['album_id']}</td>";
                    echo "<td>{$row['title']}</td>";
                    echo "<td>{$row['quantity']}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No stock items available for this branch.</td></tr>";
            }

            $stock_items->close();
            ?>
        </tbody>
    </table>
</div>
<?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'headoffice'): ?>
<div class="form-container admin-only">
    <h1>Add Branch</h1>
    <form method="POST" action="stock&branch.php">
        <input type="hidden" name="action" value="addbranch">
        <label for="branch_name">Branch Name:</label>
        <input type="text" id="branch_name" name="branch_name" required>
        <label for="province">Province:</label>
        <select id="province" name="province" required>
            <option value="EASTERN CAPE">EASTERN CAPE</option>
            <option value="FREE STATE">FREE STATE</option>
            <option value="GAUTENG">GAUTENG</option>
            <option value="KWAZULU-NATAL">KWAZULU-NATAL</option>
            <option value="MPUMALANGA">MPUMALANGA</option>
            <option value="NORTHERN CAPE">NORTHERN CAPE</option>
            <option value="LIMPOPO">LIMPOPO</option>
            <option value="NORTH WEST">NORTH WEST</option>
            <option value="WESTERN CAPE">WESTERN CAPE</option>
        </select>
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" required>
        <button type="submit">Add Branch</button>
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Branch Name</th>
                <th>Location</th>
                <th>Province</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM branch");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['branch_id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['location']}</td>
                        <td>{$row['province']}</td>
                        <td>
                            <form method='POST' action='branch.php' style='display:inline;'>
                                <input type='hidden' name='branchId' value='{$row['branch_id']}'>
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

<div class="form-container admin-only">
    <h1>Update Branch</h1>
    <form method="POST" action="stock&branch.php">
        <input type="hidden" name="action" value="update_branch">
        <label for="branch_id">Branch ID:</label>
        <select id="branch_id" name="branch_id" required>
            <?php
            $branches = $conn->query("SELECT branch_id, name FROM Branch");
            while ($branch = $branches->fetch_assoc()) {
                echo "<option value='{$branch['branch_id']}'>{$branch['name']}</option>";
            }
            ?>
        </select>
        <label for="branch_name">Branch Name:</label>
        <input type="text" id="branch_name" name="branch_name" required>
        <label for="province">Province:</label>
        <select id="province" name="province" required>
            <option value="EASTERN CAPE">EASTERN CAPE</option>
            <option value="FREE STATE">FREE STATE</option>
            <option value="GAUTENG">GAUTENG</option>
            <option value="KWAZULU-NATAL">KWAZULU-NATAL</option>
            <option value="MPUMALANGA">MPUMALANGA</option>
            <option value="NORTHERN CAPE">NORTHERN CAPE</option>
            <option value="LIMPOPO">LIMPOPO</option>
            <option value="NORTH WEST">NORTH WEST</option>
            <option value="WESTERN CAPE">WESTERN CAPE</option>
        </select>
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" required>
        <button type="submit">Update Branch</button>
    </form>
</div>
<?php endif; ?>

<div class="form-container">
    <h1>Add Stock to Branch</h1>
    <form method="POST" action="stock&branch.php">
        <input type="hidden" name="action" value="add_stock">
        <label for="branch_id">Branch:</label>
        <select id="branch_id" name="branch_id" required>
            <?php
            $branches = $conn->query("SELECT branch_id, name FROM Branch");
            while ($branch = $branches->fetch_assoc()) {
                echo "<option value='{$branch['branch_id']}'>{$branch['name']}</option>";
            }
            ?>
        </select>
        <label for="album_id">Album:</label>
        <select id="album_id" name="album_id" required>
            <?php
            $albums = $conn->query("SELECT album_id, title FROM album");
            while ($album = $albums->fetch_assoc()) {
                echo "<option value='{$album['album_id']}'>{$album['title']}</option>";
            }
            ?>
        </select>
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" min="1" required>
        <button type="submit">Add Stock</button>
    </form>
</div>

</body>
</html>
<?php
$conn->close();
?>
