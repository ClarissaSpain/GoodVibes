<?php
include('includes/db_connection.php');
include('includes/Login.php');
include('includes/Post.php');
include('includes/Comment.php');
include('includes/Notify.php');
$showTimeline = False;




if (Login::isLoggedIn()) {
        $idusers = Login::isLoggedIn();
        $showTimeline = True;
} else {
        echo 'Not logged in';
}


$timeline_address = '/profile.php?username='.$idusers;

if(isset($_GET['idposts'])) {
        Post::likePost($_GET['idposts'], $idusers);

}

if(isset($_POST['comment'])){
        Comment::createComment($_POST['commentbody'], $_GET['idposts'], $idusers);
}

if(isset($_POST['searchbox'])){
        $tosearch = explode(" ", $_POST['searchbox']);
        if(count($tosearch) == 1){
               $tosearch = str_split($tosearch[0], 2); 
        }
        $whereclause = "";
        $paramsarray = array(':username'=>'%'.$_POST['searchbox'].'%');
        for($i = 0; $i < count($tosearch); $i++){
                $whereclause .= " OR username LIKE :u$i ";
                $paramsarray[":u$i"] = $tosearch[$i];
        }
       $users = DB::query('SELECT users.username FROM goodvibes.users WHERE users.username LIKE :username '.$whereclause.'', $paramsarray); 
       print_r($users);

       $whereclause = "";
        $paramsarray = array(':body'=>'%'.$_POST['searchbox'].'%');
        for($i = 0; $i < count($tosearch); $i++){
                if ($i % 2) {
                $whereclause .= " OR body LIKE :p$i ";
                $paramsarray[":p$i"] = $tosearch[$i];
                }
        }
       $posts = DB::query('SELECT posts.body FROM goodvibes.posts WHERE posts.body LIKE :body '.$whereclause.'', $paramsarray); 
       print_r($posts);
}

?>
<!-- relational database search engine -->
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="https://static.pingendo.com/bootstrap/bootstrap-4.3.1.css">
</head>

<body>
  <nav class="navbar navbar-expand-md navbar-light" style="">
    <div class="container"> <a class="navbar-brand text-primary" href="#" >
        <i class="fa d-inline fa-lg fa-stop-circle"></i>
        <b> CoolVibes</b>
      </a> <button class="navbar-toggler navbar-toggler-right border-0" type="button" data-toggle="collapse" data-target="#navbar4">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbar4">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"> <a class="nav-link" href="<?php echo $timeline_address;?>">TIMELINE</a> </li>
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
  </nav>
  <div class="py-2">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <form class="form-inline">
            <div class="input-group">
                <form action="index.php">
              <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Search" name="searchbox">
              <input type="submit" name="search" value="Search">
              <div class="input-group-append"><button class="btn btn-primary" type="button"><i class="fa fa-search"></i></button></div>
              </form>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
   
  <div class="py-2">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1 class="display-5">Timeline</h1>
        </div>
      </div>
    </div>
  </div>
  <div class="py-0" style="">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">

<?php
$followingposts = DB::query('SELECT goodvibes.posts.idposts, goodvibes.posts.body, goodvibes.posts.likes, goodvibes.users.username FROM goodvibes.users, goodvibes.posts, goodvibes.followers
WHERE goodvibes.posts.idusers = goodvibes.followers.idusers
AND goodvibes.users.idusers = goodvibes.posts.idusers
AND follower_id = :idusers
ORDER BY goodvibes.posts.likes DESC;', array(':idusers'=>$idusers));
// array(':idusers'=>$idusers)
if(is_array($followingposts) || is_object($posts)) {
foreach($followingposts as $posts) {

        echo $posts['body'].'<h6 class="card-subtitle my-2 text-muted">'.$posts['username'].'</h6> ';
        echo "<form action='index.php?idposts=".$posts['idposts']."' method='post'>";

        if (!DB::query('SELECT idposts FROM goodvibes.posts_likes WHERE idposts=:idposts AND idusers=:idusers', array(':idposts'=>$posts['idposts'], ':idusers'=>$idusers))) {
      
        
        echo "<br><input type='submit' name='like' value='Like'>";
        } else {
        echo "<br><input type='submit' name='unlike' value='Unlike'>";
        }
        echo "&nbsp;<span>".$posts['likes']." likes</span>
        </form>
        <form action='index.php?idposts=".$posts['idposts']."' method='post'>
        <br>
        <textarea name='commentbody' rows='3' cols='50'></textarea>
        <input type='submit' name='comment' value='Comment'>
        </form>
        ";
        Comment::displayComments($posts['idposts']);
        echo "
        <hr /></br />";

}
}

?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>