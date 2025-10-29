<?php
session_start();
include 'function.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login_index.html");
    exit();
}

include 'dblogin.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchTerm = "";
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $sql = "SELECT * FROM album WHERE title LIKE ? OR creator LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%" . $searchTerm . "%";
    $stmt->bind_param("ss", $searchParam,  $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM album";
    $result = $conn->query($sql);
}

// Initialize arrays to hold albums by category
$albumsByCategory = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $albumsByCategory[$row['album_category']][] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Music Warehouse Main Page for HSYD301-1</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* https://developer.mozilla.org/en-US/docs/Web/CSS/border */
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: large;
            font-weight: bold;
            background-color: #000000;
            background-image: url('prity/background.jpg');
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            margin-top: 70px;
            padding: 0;
        }
        /* Albums Customization */
        .genre-container {
            display: flex;
            overflow-x: scroll;
            text-align: center;
            font: bold;
            color: #08FF07;
        }
        .genre-section {
            margin: 20px;
            padding: 20px;
            border-radius: 10px;
            background-color: #0002A169;
        }
        .genre-title {
            text-align: center;
            font-size: 1.5em;
            color: #08FF07;
            margin-bottom: 20px;
        }
        .album {
            margin: 20px;
            text-align: center;
        }
        .album img {
            width: 250px;
            height: 250px;
        }
        .album button {
            margin-top: 10px;
            padding: 10px;
            cursor: pointer;
            border: none;
            color: white;
        }
        .non-promo-button {
            background-color: #4CAF50;
        }
        .promotional-button {
            background-color: #b85c5c;
        }
        .on-discount-button {
            background-color: #f39c12;
        }
        /* Search Bar */
        .search-bar {
            margin: 30px;
            text-align: center;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 50%;
            font-size: 1.2em;
            color: whitesmoke;
            background-color: #FF98DB69;
        }
        .search-bar input[type="text"]::placeholder {
            color: whitesmoke;
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
        .search-bar button:hover{
            padding: 10px;
            font-size: 1.2em;
            cursor: pointer;
            margin-top: 10px;
            padding: 10px;
            cursor: pointer;
            border: none;
            color: white;
            background-color: #32cd32;
        }
    </style>
</head>
<body>
    <?php top_bar(); ?>
    
    <div class="search-bar" style="margin-top: 70px; background-color: #FF98DB69; color : whitesmoke;">
        <form method="POST" action="main_page.php">
            <input type="text" name="search" placeholder="Search Albums or Creators..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    
    <?php foreach ($albumsByCategory as $category => $albums): ?>
    <div class="genre-section">
        <div class="genre-title"><?php echo htmlspecialchars($category); ?></div>
        <div class="genre-container">
        <?php foreach ($albums as $album): ?>
            <div class="album">
                <h2><?php echo htmlspecialchars($album['title']); ?></h2>
                <img src="<?php echo htmlspecialchars($album['image_path']); ?>" alt="<?php echo htmlspecialchars($album['title']); ?>">
                <p>Creator: <?php echo htmlspecialchars($album['creator']); ?></p>     
                <?php 
                    switch ($album['album_type']): 
                        case 'non-promo': 
                            $price = $album['album_price'];
                            ?>
                            <p>Price: R<?php echo htmlspecialchars($price); ?></p>
                            <form action="viewalbum.php" method="post">
                            <input type="hidden" name="albumId" value="<?php echo htmlspecialchars($album['album_id']); ?>">
                            <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">
                            <button type="submit" class="non-promo-button">Open</button>
                            </form>
                            <?php break;
                        case 'promotional': 
                            $promo = $album['album_price'] / 2;
                            ?>
                            <p>Was: R<?php echo htmlspecialchars($album['album_price']); ?><br>Now: R<?php echo htmlspecialchars($promo); ?> PROMOTION 50% OFF</p>
                            <form action="viewalbum.php" method="post">
                            <input type="hidden" name="albumId" value="<?php echo htmlspecialchars($album['album_id']); ?>">
                            <input type="hidden" name="price" value="<?php echo htmlspecialchars($promo); ?>">
                            <button type="submit" class="promotional-button">Open</button>
                            </form>
                            <?php break;
                        case 'on-discount': 
                            $discount = $album['album_price'] * 0.9;
                            ?>
                            <p>Was: R<?php echo htmlspecialchars($album['album_price']); ?><br>Now: R<?php echo htmlspecialchars($discount); ?> 10% OFF NOW</p>
                            <form action="viewalbum.php" method="post">
                            <input type="hidden" name="albumId" value="<?php echo htmlspecialchars($album['album_id']); ?>">
                            <input type="hidden" name="price" value="<?php echo htmlspecialchars($discount); ?>">
                            <button type="submit" class="on-discount-button">Open</button>
                            </form>
                        <?php break;
                    endswitch; 
                ?>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
</body>
</html>
