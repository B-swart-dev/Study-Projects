<?php
session_start();
require_once 'function.php';
$servername = "localhost";
$username = "root";
$password = "mysql";
$database = "olms";

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["return_book"])) {
    $loan_id = $_POST["loan_id"];
    $update_loan_sql = "UPDATE loans SET status = 'Returned', return_date = CURRENT_TIMESTAMP WHERE loan_id = $loan_id";
    if ($conn->query($update_loan_sql) === TRUE) {
        $book_id = $_POST["book_id"];
        $update_quantity_sql = "UPDATE books SET quantity = quantity + 1 WHERE book_id = $book_id";
        $conn->query($update_quantity_sql);
        $update_status_sql = "UPDATE books SET status = 'Available' WHERE book_id = $book_id AND status = 'None in Library'";
        $conn->query($update_status_sql);
        echo "<script>alert('Book returned successfully!');</script>";
        } else {
        echo "<script>alert('Error updating record: '" . $conn->error . ");</script>";     
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_book"])) {
    $book_id = $_POST["book_id"];
    $delete_book_sql = "DELETE FROM books WHERE book_id = $book_id";
    if ($conn->query($delete_book_sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            echo "<script>alert('Book deleted successfully!');</script>";
        } else {
            echo "<script>alert('Book does not exist.');</script>";
        }
    } else {
        echo "<script>alert('Error deleting record: '" . $conn->error . ");</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "add_book") {
    $title = $_POST["title"];
    $author = $_POST["author"];
    $quantity = $_POST["quantity"];
    $status = $_POST["status"];
    $image_path = "images/noimg.png";

    if (isset($_FILES['filename']) && $_FILES['filename']['error'] == 0) {
        $allowed_types = ['image/jpeg' => 'jpg', 'image/gif' => 'gif', 'image/png' => 'png', 'image/tiff' => 'tif'];
        $file_type = $_FILES['filename']['type'];
        if (array_key_exists($file_type, $allowed_types)) {
            $ext = $allowed_types[$file_type];
            $filename = "images/" . basename($_FILES['filename']['name']);
            if (move_uploaded_file($_FILES['filename']['tmp_name'], $filename)) {
                $image_path = $filename;
            } else {
                echo "<script>alert('Error uploading image.');</script>";
            }
        } else {
            echo "<script>alert('Invalid image type. Allowed types are: JPEG, GIF, PNG, TIFF.');</script>";
        }
    }

    $insert_book_sql = "INSERT INTO books (title, author, quantity, image_path, status) VALUES ('$title', '$author', $quantity, '$image_path', '$status')";
    // echo "<p>Executing SQL: $insert_book_sql</p>";
    if ($conn->query($insert_book_sql) === TRUE) {
        echo "<script>alert('Book added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding book: '" . $conn->error . ");</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Librarian Management</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
        /* Book Customization  */
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
        .book {
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
        h1{
            font: Arial;
            display: flex;
            text-align: center;
            justify-content: space-between;
            padding: 0 10px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<?php topbar(); ?>
    <form id="book" method="POST" action="librarian_screen.php" enctype="multipart/form-data">
        <div class="book">
            <div class="form-container">
                <div class="column">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                    
                    <label for="quantity">Quantity:</label>
                    <input type="text" id="quantity" name="quantity" required>
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="Preview Only">Preview Only</option>
                        <option value="None in Library">None in Library</option>
                        <option value="Available">Available</option>
                    </select>
                </div>
                <div class="column">
                    <label for="author">Author:</label>
                    <input type="text" id="author" name="author" required>
                    
                    <label for="filename">Image Path:</label>
                    <input type="file" id="filename" name="filename">
                </div>
            </div>
            <button type="submit" name="action" value="add_book">Add Book</button>
        </div>
    </form>   
        
        <h1>Available Books</h1>
    <div class="table-container">
        <table border="1">
            <tr>
                <th>Book ID</th>
                <th>Book Title</th>
                <th>Author</th>
                <th>Image Path</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM books");
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["book_id"] . "</td>";
                    echo "<td>" . $row["title"] . "</td>";
                    echo "<td>" . $row["author"] . "</td>";
                    echo "<td>" . $row["image_path"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "<td>";
                    echo '<form method="POST" action="">';
                    echo '<input type="hidden" name="book_id" value="' . $row["book_id"] . '">';
                    echo '<button type="submit" name="remove_book">Remove Book</button>';
                    echo '</form>';
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No books currently listed</td></tr>";
            }
            ?>
        </table>
    </div>

    <h1>Return Books</h1>
    <div class="table-container">
        <table border="1">
            <tr>
                <th>Loan ID</th>
                <th>Book Title</th>
                <th>Author</th>
                <th>Borrow Date</th>
                <th>User</th>
                <th>Action</th>
            </tr>
            <?php
            $sql = "SELECT loans.loan_id, books.book_id, books.title, books.author, loans.loan_date, users.name, users.surname FROM loans
                INNER JOIN books ON loans.book_id = books.book_id 
                INNER JOIN users ON loans.user_id = users.user_id 
                WHERE loans.status = 'Borrowed'";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["loan_id"] . "</td>";
                    echo "<td>" . $row["title"] . "</td>";
                    echo "<td>" . $row["author"] . "</td>";
                    echo "<td>" . $row["loan_date"] . "</td>";
                    echo "<td>" . $row["name"] . " " . $row["surname"] . "</td>";
                    echo "<td>";
                    echo '<form method="POST" action="">';
                    echo '<input type="hidden" name="loan_id" value="' . $row["loan_id"] . '">';
                    echo '<input type="hidden" name="book_id" value="' . $row["book_id"] . '">';
                    echo '<button type="submit" name="return_book">Mark as Returned</button>';
                    echo '</form>';
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No books currently borrowed</td></tr>";
            }
            $conn->close();
            ?>
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