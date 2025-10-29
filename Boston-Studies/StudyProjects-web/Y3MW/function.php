<?php
function top_bar() {
    $randstr = md5(rand());
    $currentPage = basename($_SERVER['PHP_SELF']);
    echo '<div id="topBar"> <div class="page-banner page-banner-black page-banner-center">';
    echo '<a class="Logo"><img onclick="location.href = \'main_page.php\'" alt="Internet Archive logo" src="prity/logo_smoll.png" width="60"></a>';
    if ($currentPage !== 'main_page.php' && $_SESSION['role'] === 'admin') {
        echo '<a class="Intro">Welcome ' . $_SESSION["name"] . ' ' . $_SESSION["surname"] . '! With great user privileges comes great responsibility</a>';
    } else {
        echo '<a class="Intro">Welcome ' . $_SESSION["name"] . ' ' . $_SESSION["surname"] . '! To Music Warehouse explore more, listen more and create more.</a>';  
    }
    if ($currentPage === 'cart.php' && $_SESSION['role'] === 'user') {
        echo '<a class="Intro">Welcome ' . $_SESSION["name"] . ' ' . $_SESSION["surname"] . '! Checking out? Cool Beans! Come and join us again and explore more next time.</a>';
    }
    echo '<form method="POST" action="logout.php" style="display:inline;">
                <a class="Logout"><button type="submit" name="btnlogout" id="btnlogout">LOGOUT</button></a>
            </form>';
    Menu();
    echo '  ';
    echo '</div> </div>';
}

function Menu() {
    $randstr = md5(rand());
    $currentPage = basename($_SERVER['PHP_SELF']);
    echo '<div class="hamburger-component header-dropdown">';
    echo '<details>';
    echo '<summary header="HeaderBar">';
    echo '<img class="hamburger__icon" src="prity/hamburger-icon.svg" alt="additional options menu">';
    echo '</summary>';
    echo '<div class="mask-menu"></div>';
    echo '<div class="app-drawer">';
    echo '<ul class="dropdown-menu hamburger-dropdown-menu">';
    if ($currentPage !== 'cart.php') {
        echo '<li>';
        echo '<a href="cart.php?r="'.$randstr.'>';
        echo 'Cart';
        echo '</a>';
        echo '</li>';
    }
    echo '<li class="subsection">';
    echo 'Navigation';
    echo '</li>';   
    if ($currentPage !== 'main_page.php') {
        echo '<li>';
        echo '<a href="main_page.php">';
        echo 'Home Page';
        echo '</a>';
        echo '</li>';
    }    
    if ($currentPage !== 'profile.php') {
        echo '<li>';
        echo '<a href="profile.php">';
        echo 'Profile Management';
        echo '</a>';
        echo '</li>';
    }               
    if ($_SESSION['role'] === 'admin' || $_SESSION['user_role'] === 'headoffice' || $_SESSION['user_role'] === 'store-manager') {
        echo '<li class="subsection">';
        echo 'Admin Options';
        echo '</li>';
        if ($currentPage !== 'album.php') {
            echo '<li>';
            echo '<a href="album.php">';
            echo 'Album Management';
            echo '</a>';
            echo '</li>';
        }
        if ($currentPage !== 'register.php') {
            echo '<li>';
            echo '<a href="register.php">';
            echo 'User Administration';
            echo '</a>';
            echo '</li>';
        }
        }
        if ($_SESSION['user_role'] === 'headoffice' || $_SESSION['user_role'] === 'store-manager' || $_SESSION['user_role'] === 'admin') {
            echo '<li class="subsection">';
            echo 'Invoices & Orders';
            echo '</li>';
            if ($currentPage !== 'invoices.php') {
                echo '<li>';
                echo '<a href="invoices.php">';
                echo 'Invoices';
                echo '</a>';
                echo '</li>';
            }          
            }
        if ($_SESSION['role'] === 'admin') {
            echo '<li class="subsection">';
            echo 'Management';
            echo '</li>';
            if ($currentPage !== 'stock&branch.php') {
                echo '<li>';
                echo '<a href="stock&branch.php">';
                echo 'Stock & Order management';
                echo '</a>';
                echo '</li>';
            }
        }
    echo '</ul>';
    echo '</div>';
    echo '</details>';
    echo '</div>';  
}
