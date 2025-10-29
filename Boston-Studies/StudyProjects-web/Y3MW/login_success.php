<!DOCTYPE html>
<html>
<head>
    <title>Logged in success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000000;
            background-image: url('prity/logout.jpg');
            object-fit: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
            padding: 0;
            color: white;
        }
        .center {
            text-align: center;
            margin-top: 200px;
            padding: 20px;
            background-color: rgba(47, 196, 163, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 50%;
            margin-left: auto;
            margin-right: auto;
        }
        a {
            color: #FF0004;
            font-weight: bold;
            text-decoration: none;
        }
        a:hover {
            color: #FF000494;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php
    session_start();
        if (isset($_SESSION['user_id']))
        {
            $name = $_SESSION['name'];
            $surname = $_SESSION['surname'];
            $randstr = md5(rand()); // Generate a random string for the URL
            echo "<div class='center'>You have $name $surname. have been logged in succesfully <a data-transition='slide' href='main_page.php?r=$randstr'>click here</a>
            to continue.</div>";
        }
        else
        {
            $randstr = md5(rand());
            echo "<div class='center'>You have not logged in yet please log in first: <a data-transition='slide' href='login_index.html?r=$randstr'>click here</a>
            go to login screen.</div>";
        }
    ?>
</body>
</html>