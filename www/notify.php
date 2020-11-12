<?php
include('includes/db_connection.php');
include('includes/Login.php');

if (Login::isLoggedIn()) {
        $idusers = Login::isLoggedIn();

} else {
        echo 'Not logged in';
}

if (DB::query('SELECT * FROM goodvibes.notify WHERE receiver=:idusers', array(':idusers'=>$idusers))) {

    $notify = DB::query('SELECT * FROM goodvibes.notify WHERE receiver=:idusers', array(':idusers'=>$idusers));

    foreach($notify as $n) {
            echo $n['typenotify'];
    }

}

?>