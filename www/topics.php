<?php
include('includes/db_connection.php');
include('includes/Login.php');
include('includes/Post.php');
// include('./includes/Image.php');

if (isset($_GET['topic'])) {

        if (DB::query("SELECT topics FROM goodvibes.posts WHERE FIND_IN_SET(:topic, topics)", array(':topic'=>$_GET['topic']))) {

                $posts = DB::query("SELECT * FROM goodvibes.posts WHERE FIND_IN_SET(:topic, topics)", array(':topic'=>$_GET['topic']));

                foreach($posts as $post) {
                        // echo "<pre>";
                        // print_r($post);
                        // echo "</pre>";
                        echo $post['body']."<br />";
                }

        }

}
?>