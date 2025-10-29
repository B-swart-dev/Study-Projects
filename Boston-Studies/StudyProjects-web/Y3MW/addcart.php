<?php
session_start();
include 'dblogin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['album_id']) && isset($_POST['price'])) {
    $albumId = $_POST['album_id'];
    $price = $_POST['price'];

    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $stmt = $conn->prepare("SELECT * FROM Cart WHERE user_id = ? AND album_id = ?");
        $stmt->bind_param("ii", $userId, $albumId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $newQuantity = $row['quantity'] + 1;
            $stmt = $conn->prepare("UPDATE Cart SET quantity = ? WHERE user_id = ? AND album_id = ?");
            $stmt->bind_param("iii", $newQuantity, $userId, $albumId);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO Cart (user_id, album_id, quantity, price) VALUES (?, ?, '1', ?)");
            $stmt->bind_param("iid", $userId, $albumId, $price);
            $stmt->execute();
        }
        echo "Cart updated successfully";
    } else {
        echo "User not logged in";
    }
} else {
    echo "Invalid request";
}
?>
<?php
session_start();
include 'dblogin.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['album_id']) && isset($_POST['price'])) {
    $albumId = $_POST['album_id'];
    $price = $_POST['price'];

    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        $stmt = $conn->prepare("SELECT * FROM Cart WHERE user_id = ? AND album_id = ?");
        $stmt->bind_param("ii", $userId, $albumId);
        $stmt->execute();
        $result = $stmt->get_result();
        $newQuantity = 0;
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $newQuantity = $row['quantity'] + 1;
            $stmt = $conn->prepare("UPDATE Cart SET quantity = ? WHERE user_id = ? AND album_id = ?");
            $stmt->bind_param("iii", $newQuantity, $userId, $albumId);
            $stmt->execute();
        } else {
            $newQuantity = 1;
            $stmt = $conn->prepare("INSERT INTO Cart (user_id, album_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iid", $userId, $albumId, $newQuantity, $price);
            $stmt->execute();
        }
        echo "Cart updated successfully";
    } else {
        echo "User not logged in";
    }
} else {
    echo "Invalid request";
}
?>
