<?php
function Logout() {
    session_start();
    session_destroy();
    header("Location: login_index.html");
    exit();
}

function topbar() {
echo '<div id="topBar">
        <div class="page-banner page-banner-black page-banner-center">
        <a class="Logo"><img alt="Internet Archive logo" src="BostonLogo.png" width="60"></a>
        <a class="Intro">Welcome ' . $_SESSION["name"] . ' ' . $_SESSION['surname'] . '! To our Online Library Management System, Explore our diverse range of books</a>
        <a class="Logout"><button id="btnlogout" type="button">LOGOUT</button></a>';
       AdminMenu();  
    echo '</div>
</div>';
        }
function AdminMenu() {
    if ($_SESSION['role'] === 'admin') {
        $currentPage = basename($_SERVER['PHP_SELF']);
        echo '<div class="hamburger-component header-dropdown">';
        echo '<details>';
        echo '<summary header="HeaderBar">';
        echo '<img class="hamburger__icon" src="hamburger-icon.svg" alt="additional options menu">';
        echo '</summary>';
        echo '<div class="mask-menu"></div>';
        echo '<div class="app-drawer">';
        echo '<ul class="dropdown-menu hamburger-dropdown-menu">';
        echo '<li class="subsection">Navigation</li>';
            if ($currentPage !== 'main_page.php') {
                echo '<li>';
                echo '<a href="main_page.php?r=' . $_SESSION['random_code'] . '">Home Page</a>';
                echo '</li>';
                }    
            if ($currentPage !== 'user_creation.php') {
                echo '<li>';
                echo '<a href="user_creation.php?r=' . $_SESSION['random_code'] . '">User Creation</a>';
                echo '</li>';
                }               
            if ($currentPage !== 'librarian_screen.php') {
                echo '<li>';
                echo '<a href="librarian_screen.php?r=' . $_SESSION['random_code'] . '">Library Management</a>';
                echo '</li>';
                }
        echo '</ul>';
        echo '</div>';
        echo '</details>';
        echo '</div>';
    }
}             
