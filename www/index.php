<?php
include('includes/db_connection.php');
include('includes/Login.php');

if (Login::isLoggedIn()) {
        echo 'Logged In';
        echo Login::isLoggedIn();
} else {
        echo 'Not logged in';
}

?>