<?php
session_start();
include 'dblogin.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Begin transaction to ensure data integrity
$conn->begin_transaction();

try {
    // Insert into invoice first
    $stmt = $conn->prepare("INSERT INTO Invoice (user_id, branch_id, date) VALUES (?, 1, NOW())");
    if (!$stmt) {
        throw new Exception("Prepare statement for invoice failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $invoice_id = $conn->insert_id;
    $stmt->close();

    // Retrieve cart items from database
    $stmt = $conn->prepare("SELECT * FROM Cart WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement for cart items failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $albumId = $row['album_id'];
            $quantity = $row['quantity'];
            $price = $row['price'] * $quantity;

            // Fetch stock_item_id
            $stockStmt = $conn->prepare("SELECT stock_item_id FROM Stock_Item WHERE album_id = ?");
            if (!$stockStmt) {
                throw new Exception("Prepare statement for stock item ID failed: " . $conn->error);
            }
            $stockStmt->bind_param("i", $albumId);
            $stockStmt->execute();
            $stockResult = $stockStmt->get_result();
            $stockItem = $stockResult->fetch_assoc();
            if (!$stockItem) {
                throw new Exception("No stock item found for album ID: $albumId");
            }
            $stock_item_id = $stockItem['stock_item_id'];
            $stockStmt->close();

            // Insert each item into Invoice_Line
            $invoiceLineQuery = "INSERT INTO Invoice_Line (invoice_id, stock_item_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($invoiceLineQuery);
            if (!$stmt) {
                throw new Exception("Prepare statement for invoice line failed: " . $conn->error . " | Query: " . $invoiceLineQuery);
            }
            $stmt->bind_param("iiid", $invoice_id, $stock_item_id, $quantity, $price);
            $stmt->execute();
            $stmt->close();

            // Update stock quantity
            $updateStockStmt = $conn->prepare("UPDATE Stock_Item SET quantity = quantity - ? WHERE stock_item_id = ?");
            if (!$updateStockStmt) {
                throw new Exception("Prepare statement for stock update failed: " . $conn->error);
            }
            $updateStockStmt->bind_param("ii", $quantity, $stock_item_id);
            $updateStockStmt->execute();
            $updateStockStmt->close();
        }
        
        // Clear the cart after successful checkout
        $stmt = $conn->prepare("DELETE FROM Cart WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement for clearing cart failed: " . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        $conn->commit();
        echo "<script>alert('Checkout successful'); window.location.href='main_page.php';</script>";
    } else {
        throw new Exception("Cart is empty");
    }
} catch (Exception $e) {
    $conn->rollback();
    echo "<script>alert('Checkout failed: " . addslashes($e->getMessage()) . "'); window.location.href='cart.php';</script>";
}

$conn->close();
