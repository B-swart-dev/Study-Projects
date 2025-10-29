<?php
session_start();
require_once 'function.php';
include 'dblogin.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['headoffice', 'store-manager', 'admin'])) {
    header("Location: login_index.html");
    exit();
}

function validate($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$isUpdatingAlbum = false;
$isUpdatingSong = false;
$updateAlbumId = null;
$updateSongId = null;

$valid_categories = ['not specified', 'country', 'rock', 'jazz', 'hip hop', 'classical music', 'Electro Pop', 'pop', 'blues'];
$valid_types = ['non-promo', 'promotional', 'on-discount'];

if (isset($_GET['update_album_id'])) {
    $isUpdatingAlbum = true;
    $updateAlbumId = validate($_GET['update_album_id']);

    // Fetch album data to prefill the form
    $stmt = $conn->prepare("SELECT * FROM album WHERE album_id = ?");
    $stmt->bind_param("i", $updateAlbumId);
    $stmt->execute();
    $albumResult = $stmt->get_result();
    if ($albumResult->num_rows > 0) {
        $albumData = $albumResult->fetch_assoc();
    }
    $stmt->close();
}

if (isset($_GET['update_song_id'])) {
    $isUpdatingSong = true;
    $updateSongId = validate($_GET['update_song_id']);

    // Fetch song data to prefill the form
    $stmt = $conn->prepare("SELECT * FROM Songs WHERE song_id = ?");
    $stmt->bind_param("i", $updateSongId);
    $stmt->execute();
    $songResult = $stmt->get_result();
    if ($songResult->num_rows > 0) {
        $songData = $songResult->fetch_assoc();
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_album'])) {
        // Delete album
        $deleteAlbumId = validate($_POST['delete_album_id']);
        $deleteQuery = "DELETE FROM album WHERE album_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $deleteAlbumId);
        if ($stmt->execute()) {
            echo "<script>alert('Album deleted successfully');</script>";
        } else {
            echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    } else if (isset($_POST['delete_song'])) {
        // Delete song
        $deleteSongId = validate($_POST['delete_song_id']);
        $deleteQuery = "DELETE FROM Songs WHERE song_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $deleteSongId);
        if ($stmt->execute()) {
            echo "<script>alert('Song deleted successfully');</script>";
        } else {
            echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    } else if (isset($_POST['action'])) {
        if ($_POST['action'] == 'addalbum' || $_POST['action'] == 'update_album') {
            $title = validate($_POST['title']);
            $creator = validate($_POST['creator']);
            $price = validate($_POST['price']);
            $category = validate($_POST['category']);
            $type = validate($_POST['type']);
            $image_path = isset($_POST['existing_image']) ? validate($_POST['existing_image']) : 'images/noimg.png';

            // Validate category and type
            if (!in_array($category, $valid_categories)) {
                echo "<script>alert('Invalid album category');</script>";
                exit();
            }
            if (!in_array($type, $valid_types)) {
                echo "<script>alert('Invalid album type');</script>";
                exit();
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = "images/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if file already exists
                if (!file_exists($target_file)) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image_path = $target_file;
                    } else {
                        echo "<script>alert('Error uploading image.');</script>";
                    }
                } else {
                    echo "<script>alert('Image already exists. Using existing image.');</script>";
                    $image_path = $target_file;
                }
            }

            if ($isUpdatingAlbum) {
                // Updates an existing album
                $updateQuery = "UPDATE album SET title = ?, creator = ?, album_price = ?, album_category = ?, album_type = ?, image_path = ? WHERE album_id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("sssssss", $title, $creator, $price, $category, $type, $image_path, $updateAlbumId);
                if ($stmt->execute()) {
                    echo "<script>alert('Album updated successfully');</script>";
                    header("Location: album.php");
                    exit();
                } else {
                    echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
                }
            } else {
                // Insert new album into database
                $insertQuery = "INSERT INTO album (title, creator, album_price, album_category, album_type, image_path) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("ssssss", $title, $creator, $price, $category, $type, $image_path);
                if ($stmt->execute()) {
                    echo "<script>alert('New album added successfully');</script>";
                } else {
                    echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
                }
            }
            $stmt->close();
        } else if ($_POST['action'] == 'addsong' || $_POST['action'] == 'update_song') {
            $title = validate($_POST['title']);
            $song_category = validate($_POST['song_category']);
            $creator = validate($_POST['creator']);
            $preview_path = isset($_POST['existing_preview']) ? validate($_POST['existing_preview']) : 'preview/nopreview.mp3';

            // Validate category
            if (!in_array($song_category, $valid_categories)) {
                echo "<script>alert('Invalid song category');</script>";
                exit();
            }

            if (isset($_FILES['preview']) && $_FILES['preview']['error'] == 0) {
                $target_dir = "preview/";
                $target_file = $target_dir . basename($_FILES["preview"]["name"]);
                $previewFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if file already exists
                if (!file_exists($target_file)) {
                    if (move_uploaded_file($_FILES["preview"]["tmp_name"], $target_file)) {
                        $preview_path = $target_file;
                    } else {
                        echo "<script>alert('Error uploading preview.');</script>";
                    }
                } else {
                    echo "<script>alert('Preview already exists. Using existing preview.');</script>";
                    $preview_path = $target_file;
                }
            }

            if ($isUpdatingSong) {
                // Updates an existing song
                $updateQuery = "UPDATE Songs SET title = ?, song_category = ?, preview_path = ?, creator = ? WHERE song_id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("ssssi", $title, $song_category, $preview_path, $creator, $updateSongId);
                if ($stmt->execute()) {
                    echo "<script>alert('Song updated successfully');</script>";
                    header("Location: album.php");
                    exit();
                } else {
                    echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
                }
            } else {
                // Insert new song into database
                $insertQuery = "INSERT INTO Songs (title, song_category, preview_path, creator) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("ssss", $title, $song_category, $preview_path, $creator);
                if ($stmt->execute()) {
                    echo "<script>alert('New song added successfully');</script>";
                } else {
                    echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Album Management</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000000;
            background-image: url('prity/login_background.jpg');
            background-position-x: center;
            margin: 0;
            padding: 0;
            color: white;
        }
        .album-form, .song-form {
            width: 80%;
            margin: 100px auto;
            padding: 20px;
            background-color: #2fc4a388;
            border: 1px solid #c4c0c044;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        table.form-table {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(47, 196, 163, 0.8);
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table.form-table th, table.form-table td {
            padding: 10px;
            color: white;
        }
        table.form-table input, table.form-table select {
            background-color: rgba(0, 0, 0, 0.7);
            color: rgba(255, 255, 255, 0.9);
            font-weight: bold;
            margin-bottom: 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            width: 100%;
        }
        table.form-table button {
            width: 100%;
            padding: 10px;
            background-color: #c9df0494;
            border: none;
            color: white;
            cursor: pointer;
            font-size: medium;
            font-weight: bold;
        }
        table.form-table button:hover {
            background-color: #00d300a9;
            color: white;
            font-weight: bold;
        }
        .table-container {
            width: 80%;
            margin: 50px auto;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #2fc4a388;
            color: white;
            text-align: left;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #c4c0c044;
        }
        table th {
            background-color: #8C2D38;
            color: white;
            font-weight: bold;
        }
        table td {
            background-color: #0000007a;
        }
        .delete-button, .update-button {
            background-color: #8C2D38;
            border: none;
            color: white;
            padding: 5px 10px;
            cursor: pointer;
            font-size: small;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .delete-button:hover, .update-button:hover {
            background-color: #ff0000a9;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php top_bar()?>

    <!-- Album Form -->
    <div class="album-form">
        <form id="album" method="POST" action="album.php" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th colspan="2"><?php echo $isUpdatingAlbum ? 'Update Album' : 'Add Album'; ?></th>
                </tr>
                <tr>
                    <td>Album Title:</td>
                    <td><input type="text" id="title" name="title" value="<?php echo $isUpdatingAlbum ? $albumData['title'] : ''; ?>" required></td>
                </tr>
                <tr>
                    <td>Creator:</td>
                    <td><input type="text" id="creator" name="creator" value="<?php echo $isUpdatingAlbum ? $albumData['creator'] : ''; ?>" required></td>
                </tr>
                <tr>
                    <td>Price:</td>
                    <td><input type="number" step="0.01" id="price" name="price" value="<?php echo $isUpdatingAlbum ? $albumData['album_price'] : ''; ?>" required></td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>
                        <select id="category" name="category">
                            <option value="not specified" <?php echo $isUpdatingAlbum && $albumData['album_category'] === 'not specified' ? 'selected' : ''; ?>>not specified</option>
                            <option value="country" <?php echo $isUpdatingAlbum && $albumData['album_category'] === 'country' ? 'selected' : ''; ?>>country</option>
                            <option value="rock" <?php echo $isUpdatingAlbum && $albumData['album_category'] === 'rock' ? 'selected' : ''; ?>>rock</option>
                            <option value="jazz" <?php echo $isUpdatingAlbum && $albumData['album_category'] === 'jazz' ? 'selected' : ''; ?>>jazz</option>
                            <option value="hip hop" <?php echo $isUpdatingAlbum && $albumData['album_category'] === 'hip hop' ? 'selected' : ''; ?>>hip hop</option>
                            <option value="classical music" <?php echo $isUpdatingAlbum && $albumData['album_category'] === 'classical music' ? 'selected' : ''; ?>>classical music</option>
                            <option value="Electro Pop" <?php echo $isUpdatingAlbum && $albumData['album_category'] === 'Electro Pop' ? 'selected' : ''; ?>>Electro Pop</option>
                            <option value="pop" <?php echo $isUpdatingAlbum && $albumData['album_category'] === 'pop' ? 'selected' : ''; ?>>pop</option>
                            <option value="blues" <?php echo $isUpdatingAlbum && $albumData['album_category'] === 'blues' ? 'selected' : ''; ?>>blues</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Type:</td>
                    <td>
                        <select id="type" name="type" required>
                            <option value="non-promo" <?php echo $isUpdatingAlbum && $albumData['album_type'] === 'non-promo' ? 'selected' : ''; ?>>Non-Promo</option>
                            <option value="promotional" <?php echo $isUpdatingAlbum && $albumData['album_type'] === 'promotional' ? 'selected' : ''; ?>>Promotional</option>
                            <option value="on-discount" <?php echo $isUpdatingAlbum && $albumData['album_type'] === 'on-discount' ? 'selected' : ''; ?>>On-Discount</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Album Image:</td>
                    <td><input type="file" id="image" name="image"></td>
                </tr>
                <input type="hidden" name="existing_image" value="<?php echo $isUpdatingAlbum ? $albumData['image_path'] : 'images/noimg.png'; ?>">
                <tr>
                    <td colspan="2">
                        <button type="submit" name="action" value="<?php echo $isUpdatingAlbum ? 'update_album' : 'addalbum'; ?>">
                            <?php echo $isUpdatingAlbum ? 'Update Album' : 'Add Album'; ?>
                        </button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- Song Form -->
    <div class="song-form">
        <form id="song" method="POST" action="album.php" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th colspan="2"><?php echo $isUpdatingSong ? 'Update Song' : 'Add Song'; ?></th>
                </tr>
                <tr>
                    <td>Song Title:</td>
                    <td><input type="text" id="title" name="title" value="<?php echo $isUpdatingSong ? $songData['title'] : ''; ?>" required></td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>
                        <select id="song_category" name="song_category">
                            <option value="not specified" <?php echo $isUpdatingSong && $songData['song_category'] === 'not specified' ? 'selected' : ''; ?>>not specified</option>
                            <option value="country" <?php echo $isUpdatingSong && $songData['song_category'] === 'country' ? 'selected' : ''; ?>>country</option>
                            <option value="rock" <?php echo $isUpdatingSong && $songData['song_category'] === 'rock' ? 'selected' : ''; ?>>rock</option>
                            <option value="jazz" <?php echo $isUpdatingSong && $songData['song_category'] === 'jazz' ? 'selected' : ''; ?>>jazz</option>
                            <option value="hip hop" <?php echo $isUpdatingSong && $songData['song_category'] === 'hip hop' ? 'selected' : ''; ?>>hip hop</option>
                            <option value="classical music" <?php echo $isUpdatingSong && $songData['song_category'] === 'classical music' ? 'selected' : ''; ?>>classical music</option>
                            <option value="Electro Pop" <?php echo $isUpdatingSong && $songData['song_category'] === 'Electro Pop' ? 'selected' : ''; ?>>Electro Pop</option>
                            <option value="pop" <?php echo $isUpdatingSong && $songData['song_category'] === 'pop' ? 'selected' : ''; ?>>pop</option>
                            <option value="blues" <?php echo $isUpdatingSong && $songData['song_category'] === 'blues' ? 'selected' : ''; ?>>blues</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Creator:</td>
                    <td>
                        <select id="creator" name="creator" required>
                            <option value="">Select Creator</option>
                            <?php
                            $albums = $conn->query("SELECT DISTINCT creator FROM album");
                            while ($album = $albums->fetch_assoc()) {
                                echo "<option value='{$album['creator']}'" . ($isUpdatingSong && $songData['creator'] == $album['creator'] ? 'selected' : '') . ">{$album['creator']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Preview Path:</td>
                    <td><input type="file" id="preview" name="preview"></td>
                </tr>
                <input type="hidden" name="existing_preview" value="<?php echo $isUpdatingSong ? $songData['preview_path'] : 'preview/nopreview.mp3'; ?>">
                <tr>
                    <td colspan="2">
                        <button type="submit" name="action" value="<?php echo $isUpdatingSong ? 'update_song' : 'addsong'; ?>">
                            <?php echo $isUpdatingSong ? 'Update Song' : 'Add Song'; ?>
                        </button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Creator</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM album");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['album_id']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['creator']}</td>
                            <td>{$row['album_price']}</td>
                            <td>{$row['album_category']}</td>
                            <td>{$row['album_type']}</td>
                            <td><img src='{$row['image_path']}' alt='{$row['title']}' width='50'></td>
                            <td>
                                <form method='GET' action='album.php' style='display:inline;'>
                                    <input type='hidden' name='update_album_id' value='{$row['album_id']}'>
                                    <button type='submit' class='update-button'>Update</button>
                                </form>
                                <form method='POST' action='album.php' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this album?\");'>
                                    <input type='hidden' name='delete_album_id' value='{$row['album_id']}'>
                                    <button type='submit' name='delete_album' class='delete-button'>Delete</button>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Song Title</th>
                    <th>Preview</th>
                    <th>Creator</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM Songs");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['song_id']}</td>
                            <td>{$row['title']}</td>
                            <td><audio controls src='{$row['preview_path']}'></audio></td>
                            <td>{$row['creator']}</td>
                            <td>
                                <form method='GET' action='album.php' style='display:inline;'>
                                    <input type='hidden' name='update_song_id' value='{$row['song_id']}'>
                                    <button type='submit' class='update-button'>Update</button>
                                </form>
                                <form method='POST' action='album.php' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this song?\");'>
                                    <input type='hidden' name='delete_song_id' value='{$row['song_id']}'>
                                    <button type='submit' name='delete_song' class='delete-button'>Delete</button>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>