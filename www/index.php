<?php
include('includes/db_connection.php');
include('includes/Login.php');
include('includes/Post.php');
include('includes/Comment.php');
$showTimeline = False;

if (Login::isLoggedIn()) {
        $idusers = Login::isLoggedIn();
        $showTimeline = True;
} else {
        echo 'Not logged in';
}

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
<form action="index.php" method="post">
        <input type="text" name="searchbox" value="">
        <input type="submit" name="search" value="Search">
</form>

<?php


$followingposts = DB::query('SELECT goodvibes.posts.idposts, goodvibes.posts.body, goodvibes.posts.likes, goodvibes.users.username FROM goodvibes.users, goodvibes.posts, goodvibes.followers
WHERE goodvibes.posts.idusers = goodvibes.followers.idusers
AND goodvibes.users.idusers = goodvibes.posts.idusers
AND follower_id = :idusers
ORDER BY goodvibes.posts.likes DESC;', array(':idusers'=>$idusers));
// array(':idusers'=>$idusers)
if(is_array($followingposts) || is_object($posts)) {
foreach($followingposts as $posts) {

        echo $posts['body']." ~ ".$posts['username'];
        echo "<form action='index.php?idposts=".$posts['idposts']."' method='post'>";

        if (!DB::query('SELECT idposts FROM goodvibes.posts_likes WHERE idposts=:idposts AND idusers=:idusers', array(':idposts'=>$posts['idposts'], ':idusers'=>$idusers))) {
      
        echo "<input type='submit' name='like' value='Like'>";
        } else {
        echo "<input type='submit' name='unlike' value='Unlike'>";
        }
        echo "<span>".$posts['likes']." likes</span>
        </form>
        <form action='index.php?idposts=".$posts['idposts']."' method='post'>
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