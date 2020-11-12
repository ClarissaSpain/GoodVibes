  
<?php
class Notify {
        public static function createNotify($text = "", $idposts = 0) {
                $text = explode(" ", $text);
                $notify = array();

                foreach ($text as $word) {
                        if (substr($word, 0, 1) == "@") {
                                $notify[substr($word, 1)] = array("typenotify"=>1, "extra"=>' { "postbody": "'.htmlentities(implode($text, " ")).'" } ');
                        }
                }

                if (count($text) == 1 && $idposts != 0) {
                        $temp = DB::query('SELECT goodvibes.posts.idusers AS receiver, goodvibes.posts_likes.idusers AS sender FROM goodvibes.posts, goodvibes.posts_likes
                        WHERE goodvibes.posts.idposts = goodvibes.posts_likes.idposts AND WHERE goodvibes.posts.idposts=:idposts', array(':idposts'=>$idposts));
                        $r = $temp[0]["receiver"];
                        $s = $temp[0]["sender"];
                        DB::query('INSERT INTO goodvibes.notify VALUES (NULL, :typenotify, :receiver, :sender, :extra)', array(':typenotify'=>2, ':receiver'=>$r, ':sender'=>$s, ':extra'=>""));
                }

                return $notify;
        }
}
?>