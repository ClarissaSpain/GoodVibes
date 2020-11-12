<?php

class Post {
    public static function createPost($postbody, $loggedInIdUsers, $profileUserId){
        
        //check to see if its the right length
        if (strlen($postbody) > 160 || strlen($postbody) <1){
            die('Incorrect length!');    
        }

        $topics = self::getTopics($postbody);
        //check to see if the user is posting to their profile only
        if ($loggedInIdUsers == $profileUserId){

            if (count(self::notify($postbody)) !=0){
                foreach (self::notify($postbody) as $key => $n) {
                    $s = $loggedInIdUsers;
                    $r = DB::query('SELECT idusers FROM goodvibes.users WHERE username=:username', array(':username'=>$key))[0]['idusers'];
                     DB::query('INSERT INTO goodvibes.notify VALUES (NULL, :typenotify, :receiver, :sender)', array(':typenotify'=>$n, ':receiver'=>$r, ':sender'=>$s));
                }
            }

        DB::query('INSERT INTO goodvibes.posts VALUES (Null, :postbody, NOW(), :idusers, 0, NULL, :topics)', array(':postbody'=>$postbody, ':idusers'=>$profileUserId, ':topics'=>$topics));
        }else{
                die('Incorrect User');
        }
    }

    public static function createImgPost($postbody, $loggedInIdUsers, $profileUserId){
        
        //check to see if its the right length
        if (strlen($postbody) > 160){
            die('Incorrect length!');    
        }

        $topics = self::getTopics($postbody);
        //check to see if the user is posting to their profile only
        if ($loggedInIdUsers == $profileUserId){

        DB::query('INSERT INTO goodvibes.posts VALUES (Null, :postbody, NOW(), :idusers, 0, NULL)', array(':postbody'=>$postbody, ':idusers'=>$profileUserId));
        $idposts = DB::query('SELECT idposts FROM goodvibes.posts WHERE idusers=:idusers ORDER BY idposts DESC LIMIT 1;', array(':idusers'=>$loggedInIdUsers))[0]['idposts'];
        }else{
                die('Incorrect User');
        }
    }

    public static function likePost($idposts, $likerId ) {
        if (!DB::query('SELECT idusers FROM goodvibes.posts_likes WHERE idposts=:idposts AND idusers=:idusers', array(':idposts'=>$idposts, ':idusers'=>$likerId))) {
            DB::query('UPDATE goodvibes.posts SET likes=likes+1 WHERE idposts=:idposts', array(':idposts'=>$idposts));
            DB::query('INSERT INTO goodvibes.posts_likes VALUES (NULL, :idposts, :idusers)', array(':idposts'=>$idposts, ':idusers'=>$likerId));
            } else {
            //delete a like
            DB::query('UPDATE goodvibes.posts SET likes=likes-1 WHERE idposts=:idposts', array(':idposts'=>$idposts));
            DB::query('DELETE FROM goodvibes.posts_likes WHERE idposts=:idposts AND idusers=:idusers', array(':idposts'=>$idposts, ':idusers'=>$likerId));
            }
    }
    //method to get topics from posts and put them in the database
    public static function getTopics($text) {

        $text = explode(" ", $text);

        $topics = "";

        foreach ($text as $word) {
                if (substr($word, 0, 1) == "#") {
                        $topics .= substr($word, 1).",";
                }
        }

        return $topics;
}
    
    public static function notify($text) {
        $text = explode(" ", $text);
        $notify = array();

        foreach ($text as $word) {
                if (substr($word, 0, 1) == "@") {
                    
                    // $esc_word = preg_replace("/[^a-zA-Z0-9]/", "", $word);
                    $notify[substr($word, 1)] = 1;
                        
                    }
                }
            return $notify;
        }
    //method for @mentions 
    public static function link_add($text) {

        $text = explode(" ", $text);
        $newstring = "";

        foreach ($text as $word) {
                if (substr($word, 0, 1) == "@") {
                    //TODO: I think I have an XSS issue here...?
                    $esc_word = preg_replace("/[^a-zA-Z0-9]/", "", $word);
                    // $formatted_content .= "<a href='profile.php?username=$esc_word>$word."</a>";
                        $newstring .= "<a href='profile.php?username=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
                    } else if (substr($word, 0, 1) == "#") {
                        $newstring .= "<a href='topics.php?topic=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
                } else {
                        $newstring .= htmlspecialchars($word)." ";
                }
        }

        return $newstring;
}

    public static function displayPosts($idusers, $username, $loggedInIdUsers){
        $dbposts = DB::query('SELECT * FROM goodvibes.posts WHERE idusers=:idusers ORDER BY idposts DESC', array(':idusers'=>$idusers));
        $posts = "";
        foreach($dbposts as $p){
   
           if(!DB::query('SELECT idposts FROM goodvibes.posts_likes WHERE idposts=:idposts AND idusers=:idusers', array(':idposts'=>$p['idposts'], ':idusers'=>$loggedInIdUsers))){
   
            $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                <form action='profile.php?username=$username&idposts=".$p['idposts']."' method='post'>
                    <input type='submit' name='like' value='Like'>
                    <span>".$p['likes']." likes</span>
                   ";
                   if ($idusers == $loggedInIdUsers) {
                        $posts .= "<input type='submit' name='deletepost' value='x' />";
                }
                   $posts .= "
                   </form><hr/></br>
                ";
   
           } else {
            $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                   <form action='profile.php?username=$username&idposts=".$p['idposts']."' method='post'>
                           <input type='submit' name='unlike' value='Unlike'>
                           <span>".$p['likes']." likes</span>
                           ";
                           if ($idusers == $loggedInIdUsers) {
                                $posts .= "<input type='submit' name='deletepost' value='x' />";
                        }
                           $posts .= "
                           </form><hr/></br>
                        ";
           }
        }
        return $posts;
    }
    
}


?>