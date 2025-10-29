<?php
include 'dblogin.php'; 
session_start();
include 'function.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$stockInfo = '';
$album = null;
$branchId = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['albumId'])) {
    $albumId = $_POST['albumId'];
    $branchId = $_POST['branch_id'] ?? null;

    $stmt = $conn->prepare("SELECT * FROM album WHERE album_id = ?");
    $stmt->bind_param("i", $albumId);
    $stmt->execute();
    $result = $stmt->get_result();
    $album = $result->fetch_assoc();

    if ($album && $branchId) {
        $stmt = $conn->prepare("SELECT quantity FROM Stock_Item WHERE branch_id = ? AND album_id = ?");
        $stmt->bind_param("ii", $branchId, $albumId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stockInfo = "Stock Available: " . $row['quantity'];
        } else {
            $stockInfo = "No stock available.";
        }
    } else {
        $stockInfo = "Please select a branch.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($album['title']) ? "Viewing Album: " . htmlspecialchars($album['title']) : "Album Details"; ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
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

        .album-details {
            text-align: center;
            margin: 20px;
            padding: 20px;
            background-color: rgba(0, 2, 161, 0.6);
            border-radius: 10px;
        }

        .album img {
            width: 250px;
            height: 250px;
            margin-top: 20px;
        }

        .album-title {
            font-size: 2em;
            color: #08FF07;
        }

        .album-info {
            font-size: 1.2em;
            margin-top: 10px;
        }

        .album-button {
            margin-top: 10px;
            padding: 10px;
            cursor: pointer;
            border: none;
            color: white;
            width: 25%;
            box-shadow: 0 4px #999;
            background-color: #4CAF50;
            text-align: center;
            margin: 20px auto;
        }

        .nonpromo-button {
            background-color: #4CAF50;
        }

        .promo-button {
            background-color: #b85c5c;
        }

        .discount-button {
            background-color: #f39c12;
        }

        .album button:hover {
            background-color: #333;
        }
        /* Trying to use this to center my button */
        .center {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        /* Branch Selection box */
        select {
            margin-top: 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            text-align: center;
            background-color: #000000;
            color: white;
            font-size: large;
            font-weight: bold;
            width: 50%;
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
    <script>
        function addToCart(albumId, price) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "addcart.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert('Cart updated successfully');
                }
            };
            xhr.send("album_id=" + albumId + "&price=" + price);
        }
    </script>
</head>
<body>
<?php top_bar(); ?>
<div style='padding-left: 0px; padding-top: 20px;'>
    <a href='main_page.php?r=' style='text-decoration: none; text-align: left; color: white; background-color: #4CAF50; padding: 10px 20px; border-radius: 5px; font-weight: bold;'>
        Back to Main Page
    </a>
</div>
<form method="POST" action="viewalbum.php">
    <input type="hidden" name="albumId" value="<?php echo htmlspecialchars($albumId); ?>">
    <div class='album-details'>
        <?php if ($album): ?>
            <h1 class='album-title'><?php echo htmlspecialchars($album['title']); ?></h1>
            <img src='<?php echo htmlspecialchars($album['image_path']); ?>' alt='Image of <?php echo htmlspecialchars($album['title']); ?>'>
            <p class='album-info'>Creator: <?php echo htmlspecialchars($album['creator']); ?></p>
            <select id='branchSelector' name='branch_id' onchange='this.form.submit()'>
                <option value=''>Please Select a Branch</option>
                <?php
                $branches = $conn->query("SELECT branch_id, name FROM branch");
                while($branch = $branches->fetch_assoc()) {
                    $selected = ($branchId == $branch['branch_id']) ? 'selected' : '';
                    echo "<option value='{$branch['branch_id']}' {$selected}>{$branch['name']}</option>";
                }
                ?>
            </select>
            <?php if ($branchId): ?>
                <p><?php echo $stockInfo; ?></p>
                <?php
                $price = ($album['album_type'] === 'promotional') ? ($album['album_price'] / 2) : (($album['album_type'] === 'on-discount') ? ($album['album_price'] * 0.9) : $album['album_price']);
                ?>
                <button class='album-button center <?php echo ($album['album_type'] == 'non-promo') ? 'nonpromo-button' : (($album['album_type'] == 'promotional') ? 'promo-button' : 'discount-button');?> onclick="addToCart('<?php echo htmlspecialchars($album['album_id']); ?>', '<?php echo htmlspecialchars($price); ?>')">Add To Cart</button>
                <h2>Songs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Title</th>
                            <th>Preview</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetches and display songs corresponding to the selected album
                        $songsStmt = $conn->prepare("SELECT * FROM Songs WHERE creator = ?");
                        $songsStmt->bind_param("s", $album['creator']);
                        $songsStmt->execute();
                        $songsResult = $songsStmt->get_result();
                        $x = 0;
                        while ($song = $songsResult->fetch_assoc()) {
                            $x++;
                            echo "<tr>";
                            echo "<td>" . $x . "</td>";
                            echo "<td>" . htmlspecialchars($song['title']) . "</td>";
                            echo "<td><audio controls><source src='" . htmlspecialchars($song['preview_path']) . "' type='audio/mpeg'></audio></td>";
                            echo "</tr>";
                        }
                        $songsStmt->close();
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Please select a branch to see availability and options.</p>
            <?php endif; ?>
        <?php else: ?>
            <p class='album-info'>Error Displaying Album, please try again later.</p>
        <?php endif; ?>
    </div>
</form>

</body>
</html>
