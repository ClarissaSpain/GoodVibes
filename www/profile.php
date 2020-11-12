<?php
include('includes/db_connection.php');
include('includes/Login.php');
include('includes/Post.php');
include('includes/Notify.php');


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

                    if (!DB::query('SELECT follower_id FROM goodvibes.followers WHERE idusers=:idusers AND follower_id=:follower_id', array(':idusers'=>$idusers, ':follower_id'=>$follower_id))) {
                             if ($follower_id == 6) {
                                     DB::query('UPDATE goodvibes.users SET verified=1 WHERE idusers=:idusers', array(':idusers'=>$idusers));
                             }
                            DB::query('INSERT INTO goodvibes.followers VALUES (NULL, :idusers, :follower_id)', array(':idusers'=>$idusers, ':follower_id'=>$follower_id));
                    } else {
                            echo 'Already following!';
                    }
                    $isFollowing = True;
            }
    }
    if (isset($_POST['unfollow'])) {

            if ($idusers != $follower_id) {

                    if (DB::query('SELECT follower_id FROM goodvibes.followers WHERE idusers=:idusers AND follower_id=:follower_id', array(':idusers'=>$idusers, ':follower_id'=>$follower_id))) {
                             if ($follower_id == 6) {
                                     DB::query('UPDATE goodvibes.users SET verified=0 WHERE idusers=:idusers', array(':idusers'=>$idusers));
                             }
                            DB::query('DELETE FROM goodvibes.followers WHERE idusers=:idusers AND follower_id=:follower_id', array(':idusers'=>$idusers, ':follower_id'=>$follower_id));
                    }
                    $isFollowing = False;
            }
    }
    if (DB::query('SELECT follower_id FROM goodvibes.followers WHERE idusers=:idusers AND follower_id=:follower_id', array(':idusers'=>$idusers, ':follower_id'=>$follower_id))) {
            //echo 'Already following!';
            $isFollowing = True;
    }

    if (isset($_POST['deletepost'])) {
            if (DB::query('SELECT idposts FROM goodvibes.posts WHERE idposts=:idposts AND idusers=:idusers', array(':idposts'=>$_GET['idposts'], ':idusers'=>$follower_id))) {
                DB::query('DELETE FROM goodvibes.posts_likes WHERE idposts=:idposts', array(':idposts'=>$_GET['idposts']));    
                DB::query('DELETE FROM goodvibes.comments WHERE idposts=:idposts', array(':idposts'=>$_GET['idposts']));
                DB::query('DELETE FROM goodvibes.posts WHERE idposts=:idposts AND idusers=:idusers', array(':idposts'=>$_GET['idposts'], ':idusers'=>$follower_id));
                    
                    echo 'Post Deleted!';
            }
    }

     //post method
     if (isset($_POST['post'])) {
        if ($_FILES['postimg']['size']==0) {
                Post::createPost($_POST['postbody'], Login::isLoggedIn(), $idusers);
        }else{
               $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $idusers);
              
                if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                $file = $_FILES['postimg'];
                $fileName = $_FILES['postimg']['name'];
                $fileTmpName = $_FILES['postimg']['tmp_name'];
                $fileSize = $_FILES['postimg']['size'];
                $fileError = $_FILES['postimg']['error'];
                $fileType = $_FILES['postimg']['type'];
                    
                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $loggedInIdUsers =  Login::isLoggedIn();
            
                $allowed = array('jpg', 'jpeg', 'png');
                 $idposts = DB::query('SELECT idposts FROM goodvibes.posts WHERE idusers=:idusers ORDER BY idposts DESC LIMIT 1;', array(':idusers'=>$loggedInIdUsers))[0]['idposts'];
            
                if (in_array($fileActualExt, $allowed)) {
                    if ($fileError === 0) {
                        // I increased the size so that an image could get uploaded that was bigger than 50K
                        // See I don't know how to do math
                        if ($fileSize < 500000) {
                            $fileNameNew = uniqid('', true).".".$fileActualExt;
                            $fileDestination = './uploads/'.$fileNameNew;
                            move_uploaded_file($fileTmpName, $fileDestination);
                            // header("Location: my_account.php?uploadsuccess");
                            
                            //inserts the file path of my image from my directory into the database.
                            $InsertImage = DB::query('UPDATE goodvibes.posts SET postimg=:postimg WHERE idposts=:idposts', array(':postimg'=>$fileDestination,':idposts'=>$idposts));
            }
                        }else{
                            echo "Your file is too big!";
                        }
                    }else{
                        echo "There was an error uploading your file!";
                    }
        }
        
        }
        

     }

     //incremented like
     if(isset($_GET['idposts']) && !isset($_POST['deletepost'])) {
             Post::likePost($_GET['idposts'], $follower_id);

     }

     $posts = Post::displayPosts($idusers, $username, $follower_id);



}else{
        die('User not found!');
}
}
//<?php if ($verified) {echo '- Verified';}
?>


<h1><?php echo $username; ?>'s Profile</h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
        <?php
        if ($idusers != $follower_id) {
                if ($isFollowing) {
                        echo '<input type="submit" name="unfollow" value="Unfollow">';
                } else {
                        echo '<input type="submit" name="follow" value="Follow">';
                }
        }
        ?>
</form>
<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
<textarea name="postbody" row="8" cols="80"></textarea>
        </br> Upload a image:
        <input type="file" name="postimg">
        <input type="submit" name="post" value="Post">
</form>
<div class= "posts">
<?php echo $posts; ?>
</div>