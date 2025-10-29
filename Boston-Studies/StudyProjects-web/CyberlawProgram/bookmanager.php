<?php
class Book {
    public $title;
    public $author;
    public $quantity;
    public $image_path;
    public $status;
    public $category;

    public function __construct($title, $author, $quantity, $image_path, $status, $category) {
        $this->title = $title;
        $this->author = $author;
        $this->quantity = $quantity;
        $this->image_path = $image_path;
        $this->status = $status;
        $this->category = $category;
    }
}

class BookManager extends Book {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addBook($title, $author, $quantity, $image_path, $status, $category) {
        try {
            $sql = "INSERT INTO books (title, author, quantity, image_path, status, category) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssisss", $title, $author, $quantity, $image_path, $status, $category);
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to add book");
            }
        } catch (Exception $e) {
            error_log("Error adding book: " . $e->getMessage());
            return false;
        }
    }

    public function removeBook($book_id) {
        try {
            $sql = "DELETE FROM books WHERE book_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $book_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    return true;
                } else {
                    throw new Exception("Book not found");
                }
            } else {
                throw new Exception("Failed to remove book");
            }
        } catch (Exception $e) {
            error_log("Error removing book: " . $e->getMessage());
            return false;
        }
    }

    public function uploadImage($file) {
        try {
            $target_dir = "images/";
            $target_file = $target_dir . basename($file["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image
            $check = getimagesize($file["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($file["tmp_name"], $target_file)) {
                    return $target_file;
                } else {
                    throw new Exception("Failed to upload image");
                }
            } else {
                throw new Exception("File is not an image");
            }
        } catch (Exception $e) {
            error_log("Error uploading image: " . $e->getMessage());
            return "images/noimg.png";
        }
    }
}
?>
