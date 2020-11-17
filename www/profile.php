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
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="https://static.pingendo.com/bootstrap/bootstrap-4.3.1.css">
</head>

<body >
  <div class="py-2">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
        <nav class="navbar navbar-expand-md navbar-light" style="">
    <div class="container"> <a class="navbar-brand text-primary" href="#" >
        <i class="fa d-inline fa-lg fa-stop-circle"></i>
        <b> CoolVibes</b>
      </a> <button class="navbar-toggler navbar-toggler-right border-0" type="button" data-toggle="collapse" data-target="#navbar4">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbar4">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"> <a class="nav-link" href="/index.php">NEWS FEED</a> </li>
          <li class="nav-item"> <a class="nav-link" href="/my_messages.php">MESSAGES</a> </li>
          <li class="nav-item"> <a class="nav-link" href="/notify.php">NOTIFICATIONS</a> </li>
        </ul>
        <div class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user fa-fw"></i>User</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/profile.php?username=$username">My Profile</a>
            <a class="dropdown-item" href="/my_account.php">My Account</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="/logout.php">Logout</a>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="py-2" style="">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1 class="display-5"><?php echo $username; ?>'s Profile</h1>
        </div>
      </div>
    </div>
  </div>
  <div class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-2">
          <h1 class="">Bio</h1>
          <div class="row">
            <div class="col-md-12">
              <p class="text-monospace">Monospace. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
          </div>
        </div>
        <div class="col-md-8">
        <div>
                <h4>Your Posts...</h4>
        </div>
          <div class="card">

            <div class="card-body">
              <!-- <h5 class="card-title"><b>Title</b></h5> -->
              <!-- <h6 class="card-subtitle my-2 text-muted">Subtitle</h6> -->
              <div class= "posts">
                <?php echo $posts; ?>
        </div>
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


            </div>
          </div>
        </div>
        <div class="col-md-2">
        <div>
                <h4>New Post...</h4>
        </div>
        
        </form>
<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
<textarea name="postbody" row="4" cols="40"></textarea></br>
        </br> Upload a image:
        <input type="file" name="postimg"></br>
        
        <input class="btn btn-primary" type="submit" name="post" value="Post">
</form>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous" style=""></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous" style=""></script>
</body>

</html>