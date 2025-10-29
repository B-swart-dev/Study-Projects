<?php
session_start();
include 'dblogin.php';
include 'function.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cart_item_id'])) {
    $cart_item_id = $_POST['delete_cart_item_id'];
    $stmt = $conn->prepare("DELETE FROM Cart WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_item_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT Cart.*, album.title, album.image_path FROM Cart JOIN album ON Cart.album_id = album.album_id WHERE Cart.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: large;
            font-weight: bold;
            background-color: #000000;
            background-image: url('prity/background.jpg');
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            padding: 0;
            margin-top: 70px;
            color: white;
        }

        .cart-container {
            margin: 20px;
            padding: 20px;
            background-color: rgba(0, 2, 161, 0.6);
            border-radius: 10px;
        }

        .cart-item {
            margin: 20px;
            padding: 20px;
            border-bottom: 1px solid #08FF07;
        }

        .cart-title {
            font-size: 2em;
            color: #08FF07;
            text-align: center;
            margin-bottom: 20px;
        }

        .cart-info {
            font-size: 1.2em;
            margin-top: 10px;
        }

        .checkout-button {
            margin-top: 20px;
            padding: 10px;
            cursor: pointer;
            border: none;
            color: white;
            background-color: #4CAF50;
            font-size: 1.2em;
            display: block;
            width: 100%;
            text-align: center;
        }

        .checkout-button:hover {
            background-color: #45a049;
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

        img {
            width: 100px;
            height: auto;
        }

        .delete-button {
            background-color: #b85c5c;
            color: white;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
            text-align: center;
        }

        .delete-button:hover {
            background-color: #d9534f;
        }
    </style>
</head>
<body>
<?php top_bar(); ?>

<div class="cart-container">
    <h1 class="cart-title">Your Cart</h1>
    <?php if (!empty($cart_items)): ?>
        <table>
            <thead>
                <tr>
                    <th>Album</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr class="cart-item">
                        <td>
                            <?php echo htmlspecialchars($item['title']); ?><br>
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        </td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>R<?php echo htmlspecialchars($item['price']); ?></td>
                        <td>R<?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></td>
                        <td>
                            <form method="POST" action="cart.php" onsubmit="return confirm('Are you sure you want to delete this item from your cart?');">
                                <input type="hidden" name="delete_cart_item_id" value="<?php echo htmlspecialchars($item['cart_id']); ?>">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="POST" action="checkout.php">
            <button type="submit" class="checkout-button">Proceed to Checkout</button>
        </form>
    <?php else: ?>
        <p class="cart-info">Your cart is empty.</p>
    <?php endif; ?>
</div>
</body>
</html>