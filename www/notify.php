<?php
include('includes/db_connection.php');
include('includes/Login.php');

if (Login::isLoggedIn()) {
        $idusers = Login::isLoggedIn();

} else {
        echo 'Not logged in';
}
echo "<h1>Notifications</h1>";
if (DB::query('SELECT * FROM goodvibes.notify WHERE receiver=:idusers', array(':idusers'=>$idusers))) {

    $notify = DB::query('SELECT * FROM goodvibes.notify WHERE receiver=:idusers', array(':idusers'=>$idusers));

    foreach($notify as $n) {
           
        if ($n['typenotify'] == 1) {
                $senderName = DB::query('SELECT username FROM goodvibes.users WHERE idusers=:senderid', array(':senderid'=>$n['sender']))[0]['username'];

                if ($n['extra'] == "") {
                        echo "You got a notification!<hr />";
                } else {

                        $extra = json_decode($n['extra']);

                        echo $senderName." mentioned you in a post!"."<hr />";
                        // $extra->postbody.
                }

        }else if ($n['typenotify']==2){
                $senderName = DB::query('SELECT username FROM goodvibes.users WHERE idusers=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                echo $senderName."Liked your post"."<hr />";
        }

}

}


?>