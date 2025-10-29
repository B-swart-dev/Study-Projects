<?php 
session_start();
include 'dblogin.php';
include 'function.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetches the invoices to keep track of shopping along with user details and time of purchase
$stmt = $conn->prepare("
    SELECT Invoice.*, Branch.name AS branch_name, users.name AS user_name, users.surname AS user_surname FROM Invoice 
    JOIN Branch ON Invoice.branch_id = Branch.branch_id JOIN users ON Invoice.user_id = users.user_id WHERE Invoice.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$invoices = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Invoices</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000000;
            background-image: url('prity/background.jpg');
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            padding: 0;
            margin-top: 70px;
            color: white;
        }
        .invoice-container {
            margin: 20px;
            padding: 20px;
            background-color: rgba(0, 2, 161, 0.6);
            border-radius: 10px;
        }
        .invoice-title {
            font-size: 2em;
            color: #08FF07;
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-details {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #08FF07;
        }
        th {
            background-color: #0002A169;
        }
    </style>
</head>
<body>
<?php top_bar(); ?>

<div class="invoice-container">
    <h1 class="invoice-title">Your Invoices</h1>
    <?php while ($invoice = $invoices->fetch_assoc()): ?>
        <div class="invoice-details">
            <h2>Invoice #<?php echo htmlspecialchars($invoice['invoice_id']); ?></h2>
            <p>Date: <?php echo htmlspecialchars($invoice['date']); ?></p>
            <p>Branch: <?php echo htmlspecialchars($invoice['branch_name']); ?></p>
            <p>User: <?php echo htmlspecialchars($invoice['user_name'] . ' ' . $invoice['user_surname']); ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Album</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("
                        SELECT Invoice_Line.*, album.title, album.image_path 
                        FROM Invoice_Line 
                        JOIN Stock_Item ON Invoice_Line.stock_item_id = Stock_Item.stock_item_id 
                        JOIN album ON Stock_Item.album_id = album.album_id 
                        WHERE Invoice_Line.invoice_id = ?");
                    $stmt->bind_param("i", $invoice['invoice_id']);
                    $stmt->execute();
                    $items = $stmt->get_result();
                    while ($item = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['title']); ?><br>
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" width="50">
                            </td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>R<?php echo htmlspecialchars($item['price']); ?></td>
                            <td>R<?php echo htmlspecialchars($item['quantity'] * $item['price']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php $stmt->close(); ?>
                </tbody>
            </table>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
<?php $conn->close(); ?>
