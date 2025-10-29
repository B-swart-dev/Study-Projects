<!DOCTYPE html>
<html>
<head>
    <title>Logout Window</title>
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

    // Function to destroy the session
    function destroySession()
    {
        $_SESSION = array();

        if (session_id() != "" || isset($_COOKIE[session_name()]))
            setcookie(session_name(), '', time() - 2592000, '/');

        session_destroy();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnlogout']))
    {
        if (isset($_SESSION['user_id']))
        {
            $name = $_SESSION['name'];
            $surname = $_SESSION['surname'];
            destroySession();
            $randstr = md5(rand()); // Generate a random string for the URL
            echo "<div class='center'>You have been logged out, $name $surname. Please
            <a data-transition='slide' href='login_index.html?r=$randstr'>click here</a>
            to refresh the screen.</div>";
        }
        else
        {
            $randstr = md5(rand());
            echo "<div class='center'>You cannot log out because you are not logged in, 
            Please <a data-transition='slide' href='login_index.html?r=$randstr'>click here</a> to login.</div>";
        }
    }
    ?>
</body>
</html>