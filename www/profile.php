<?php
include('includes/db_connection.php');
include('includes/Login.php');
//TODO: figure out why Verify account can't follow users??

//variables
// $username = $_GET["username"];
$username = "";
// $verified = False;
$isFollowing = False;


//methods
if(isset($_GET['username'])) {
    if (DB::query('SELECT username FROM goodvibes.users WHERE username=:username', array(':username'=>$_GET['username']))) {
        //more variables :3
        $username = DB::query('SELECT username FROM goodvibes.users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
        $idusers = DB::query('SELECT idusers FROM goodvibes.users WHERE username=:username', array(':username'=>$_GET['username']))[0]['idusers'];
        // $verified = DB::query('SELECT verified FROM goodvibes.users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
        $follower_id = Login::IsLoggedIn();
        
        if (isset($_POST['follow'])) {

            if ($idusers != $follower_id) {

                    if (!DB::query('SELECT follower_id FROM goodvibes.followers WHERE idusers=:idusers', array(':idusers'=>$idusers))) {
                            // if ($follower_id == 6) {
                                    // DB::query('UPDATE goodvibes.users SET verified=1 WHERE idusers=:idusers', array(':idusers'=>$idusers));
                            // }
                            //TODO:WHAT I DON'T UNDERSTAND
                            DB::query('INSERT INTO goodvibes.followers VALUES (:idusers, :follower_id)', array(':idusers'=>$idusers, ':follower_id'=>$follower_id));
                    } else {
                            echo 'Already following!';
                    }
                    $isFollowing = True;
            }
    }
    if (isset($_POST['unfollow'])) {

            if ($idusers != $follower_id) {

                    if (DB::query('SELECT follower_id FROM goodvibes.followers WHERE idusers=:idusers', array(':idusers'=>$idusers))) {
                            // if ($follower_id == 6) {
                                    // DB::query('UPDATE goodvibes.users SET verified=0 WHERE idusers=:idusers', array(':idusers'=>$idusers));
                            // }
                            DB::query('DELETE FROM goodvibes.followers WHERE idusers=:idusers AND follower_id=:follower_id', array(':idusers'=>$idusers, ':follower_id'=>$follower_id));
                    }
                    $isFollowing = False;
            }
    }
    if (DB::query('SELECT follower_id FROM goodvibes.followers WHERE idusers=:idusers', array(':idusers'=>$idusers))) {
            //echo 'Already following!';
            $isFollowing = True;
    }

     //post method
     if (isset($_POST['post'])) {
         $postbody = $_POST['postbody'];
         $idusers = Login::isLoggedIn();

         DB::query('INSERT INTO goodvibes.posts VALUES (Null, :postbody, NOW(), :idusers, 0)', array(':postbody'=>$postbody, ':idusers'=>$idusers));

     }





} else {
    die('User not found!');
}
}
//<?php if ($verified) {echo '- Verified';}
?>


<h1><?php echo $username; ?>'s Profile</h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
        <?php
        //TODO: undefined variable idusers && $follower_id
        if ($idusers != $follower_id) {
                if ($isFollowing) {
                        echo '<input type="submit" name="unfollow" value="Unfollow">';
                } else {
                        echo '<input type="submit" name="follow" value="Follow">';
                }
        }
        ?>
</form>

<form action="profile.php?username=<?php echo $username; ?>" method="post">
        <textarea name="postbody" row="8" cols="80"></textarea>
        <input type="submit" name="post" value="Post">
</form>