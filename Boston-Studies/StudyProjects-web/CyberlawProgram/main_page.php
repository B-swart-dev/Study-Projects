<?php
session_start();
include 'function.php';
require_once "bookmanager.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "mysql";
$database = "olms";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SESSION['role'] === 'admin' || isset($_POST['search'])){
// Instantiate BookManager
$bookManager = new BookManager($conn);

// Handle form submission for adding a book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];
    $status = "Available";
    $image_path = "images/noimg.png";

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_path = $bookManager->uploadImage($_FILES['image']);
    }

    if ($bookManager->addBook($title, $author, $quantity, $image_path, $status, $category)) {
        echo "<p>Book added successfully!</p>";
    } else {
        echo "<p>Error adding book. Please try again.</p>";
    }
}
}
// Search Bar functionality
$searchTerm = "";
$books = [];

if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $sql = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%" . $searchTerm . "%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[$row['category']][] = $row;
        }
    } else {
        echo "<p>No books found matching the search criteria.</p>";
    }
} else {
    $sql = "SELECT * FROM books ORDER BY category";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[$row['category']][] = $row;
        }
    }
}

// Check if the user has already borrowed a book
$user_id = $_SESSION['user_id'];
$borrow_check_sql = "SELECT * FROM loans WHERE user_id = ? AND status = 'Borrowed'";
$borrow_check_stmt = $conn->prepare($borrow_check_sql);
$borrow_check_stmt->bind_param("i", $user_id);
$borrow_check_stmt->execute();
$borrow_check_result = $borrow_check_stmt->get_result();
$has_borrowed = $borrow_check_result->num_rows > 0;

// Update book status based on quantity
$update_available_sql = "UPDATE books SET status='Available' WHERE quantity > 0 AND status != 'Preview Only'";
$update_none_sql = "UPDATE books SET status='None in Library' WHERE quantity = 0 AND status != 'Preview Only'";
$conn->query($update_available_sql);
$conn->query($update_none_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["borrow_book"])) {
    $book_id = $_POST["book_id"];
    if (!$has_borrowed) {
        // Check quantity before updating
        $check_sql = "SELECT quantity FROM books WHERE book_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        if ($check_result && $check_result->num_rows > 0) {
            $row = $check_result->fetch_assoc();
            if ($row['quantity'] > 0) {
                // Update quantity
                $update_quantity_sql = "UPDATE books SET quantity = quantity - 1 WHERE book_id = ?";
                $update_quantity_stmt = $conn->prepare($update_quantity_sql);
                $update_quantity_stmt->bind_param("i", $book_id);
                if ($update_quantity_stmt->execute()) {
                    // Insert into loans
                    $insert_loan_sql = "INSERT INTO loans (user_id, book_id) VALUES (?, ?)";
                    $insert_loan_stmt = $conn->prepare($insert_loan_sql);
                    $insert_loan_stmt->bind_param("ii", $user_id, $book_id);
                    if ($insert_loan_stmt->execute()) {
                        if ($row['quantity'] - 1 == 0) {
                            $update_status_sql = "UPDATE books SET status='None in Library' WHERE book_id = ?";
                            $update_status_stmt = $conn->prepare($update_status_sql);
                            $update_status_stmt->bind_param("i", $book_id);
                            $update_status_stmt->execute();
                        }
                        echo "<script>alert('Book borrowed successfully!');</script>";
                        $has_borrowed = true;
                    } else {
                        echo "<script>alert('Error recording borrow transaction: " . $conn->error . "');</script>";
                    }
                } else {
                    echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
                }
            } else {
                echo "<script>alert('No more copies available for borrowing.');</script>";
            }
        } else {
            echo "<script>alert('Error fetching quantity: " . $conn->error . "');</script>";
        }
    } 
    if ($has_borrowed) {
        echo "<script>alert('You have already borrowed a book. Please return it before borrowing another.');</script>";
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>OLMS Main Page for HPXS302-1</title>
    <link rel="stylesheet" href="styles.css">
    <style>
       body {
            font-family: Arial, sans-serif;
            /* background-color: #f3f3f3; */
            /* https://www.wallpaperflare.com/black-wooden-ladder-beside-brown-wooden-bookshelf-library-step-wallpaper-azcer */
            background-image: url("background.jpg");
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
            padding: 0;
        }       
        /* Books Customization */
        .genre-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .genre-section {
            width: 100%;
            margin: 20px 0;
            padding: 20px;
            border-radius: 10px;
            background-color: #0002A169;
            text-align: center;
        }
        .genre-title {
            text-align: center;
            font-size: 1.5em;
            color: #08FF07;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
        }
        .book {
            margin: 20px;
            text-align: center;
            max-width: 200px;
            color: #08FF07;
        }
        .book img {
            width: 150px;
            height: 200px;
        }
        .book button {
            margin-top: 10px;
            padding: 10px;
            cursor: pointer;
            border: none;
            color: white;
            width: 100%;
        }
        .borrow-button {
            background-color: #4CAF50;
        }
        .none-in-library-button {
            background-color: #b85c5c;
        }
        .preview-button {
            background-color: #f39c12;
        }
        /* Search Bar */
        .search-bar {
            margin: 10px;
            text-align: center;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 50%;
            font-size: 1.2em;
            color: white; 
            background-color: black; 
        }
        .search-bar button {
            padding: 10px;
            font-size: 1.2em;
            cursor: pointer;
            margin-top: 10px;
            padding: 10px;
            cursor: pointer;
            border: none;
            color: white;
            background-color: #4CAF50;
        }
        .search-bar button:hover {
            padding: 10px;
            font-size: 1.2em;
            cursor: pointer;
            margin-top: 10px;
            padding: 10px;
            cursor: pointer;
            border: none;
            color: white;
            background-color: #07FEFF;
        }
        /* Adding book menu */
        .admin-section {
            margin: 20px;
            padding: 20px;
            background-color: #0002A169;
            border-radius: 10px;
            text-align: center;
            color: white;
        }
        .admin-section input[type="text"], .admin-section input[type="number"], .admin-section select, .admin-section input[type="file"] {
            padding: 10px;
            width: 80%;
            font-size: 1.2em;
            color: white;
            background-color: black;
            border: none;
            margin-bottom: 10px;
        }
        .admin-section button {
            padding: 10px;
            font-size: 1.2em;
            cursor: pointer;
            margin-top: 10px;
            padding: 10px;
            cursor: pointer;
            border: none;
            color: white;
            background-color: #4CAF50;
        }
    </style>
</head>
<body>
<?php 
topbar(); 
// I am just showing that i do know about objects classes and parent class
class categories {
    public $categories = array("Not Specified","Fantasy", "Horror", "Educational", "IT", "Romance");
    public $selectedCategory;

    public function __construct($selectedCategory = "Not Specified") {
        $this->selectedCategory = $selectedCategory;
    }

    public function getCategories() {
        return $this->categories;
    }

    public function getSelectedCategory() {
        return $this->selectedCategory;
    }
}
$parentClass = new categories("Not Specified");
$categories = $parentClass->getCategories();
$selectedCategory = $parentClass->getSelectedCategory();
?>

<div class="search-bar" style="margin-top: 70px; background-color: #0002A169;">
    <form method="POST" action="main_page.php">
        <input type="text" name="search" placeholder="Search Books by Title or Author..." value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button type="submit" name="btnsearch">Search</button>
    </form>
</div>

<?php if ($_SESSION['role'] === 'admin'): ?>
<div class="admin-section">
    <h2>Add New Book</h2>
    <form method="POST" action="main_page.php" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br>
        <label for="author">Author:</label>
        <input type="text" id="author" name="author" required><br>
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br>
        <label for="category">Category:</label>
        <select id="category" name="category" required>
        <?php foreach ($categories as $category): ?>
        <option value="<?php echo $category; ?>" <?php if ($category == $selectedCategory) echo 'selected'; ?>>
            <?php echo $category; ?>
        </option>
    <?php endforeach; ?>
        </select><br>
        <label for="image">Image:</label>
        <input type="file" id="image" name="image"><br>
        <button type="submit" name="add_book">Add Book</button>
    </form>
</div>
<?php endif; ?>

<?php foreach ($books as $category => $books_in_category): ?>
    <?php if (!empty($books_in_category)): ?>
    <div class="genre-section">
        <div class="genre-title"><?php echo htmlspecialchars($category); ?></div>
        <div class="genre-container">
        <?php foreach ($books_in_category as $book): ?>
            <div class="book">
                <h2><?php echo htmlspecialchars($book['title']); ?></h2>
                <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                <p>Author: <?php echo htmlspecialchars($book['author']); ?></p>     
                <?php 
                    switch ($book['status']): 
                        case 'Available': 
                            ?>
                            <form action="main_page.php" method="post">
                            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                            <button type="submit" name="borrow_book" class="borrow-button">Borrow</button>
                            </form>
                            <?php break;
                        case 'None in Library': 
                            ?>
                            <button class="none-in-library-button">None Available</button>

                            <?php break;
                        case 'Preview Only': 
                            ?>
                            <button class="preview-button">Preview Only</button>
                        <?php break;
                    endswitch; 
                ?>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

</body>
</html>
<script>
    document.getElementById("btnlogout").addEventListener("click", function() {
        var confirmation = confirm('Are you sure you want to logout?\n\nClick "Ok" to logout or "Cancel" to cancel.');
    if (confirmation) {
        window.location.href = "login_index.html";
    }
    });
    document.addEventListener('DOMContentLoaded', function() {
    const noneInLibraryButton = document.querySelector('.none-in-library-button');
    const previewButton = document.querySelector('.preview-button');

    if (noneInLibraryButton) {
        noneInLibraryButton.addEventListener('click', function() {
            alert('Appologies, No copies available in the library at this time.');
        });
    }

    if (previewButton) {
        previewButton.addEventListener('click', function() {
            alert('This book is displaying as preview only.');
        });
    }
});
</script>
<?php
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location:login_index.html");
    exit();
}
?>