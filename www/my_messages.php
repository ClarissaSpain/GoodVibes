<?php
include('includes/db_connection.php');
include('includes/Login.php');
if (Login::isLoggedIn()) {
        $idusers = Login::isLoggedIn();
} else {
        die('Not logged in');
}

if (isset($_GET['mid'])) {
        $message = DB::query('SELECT * FROM goodvibes.messages WHERE idmessages=:mid AND receiver=:receiver OR sender=:sender', array(':mid'=>$_GET['mid'], ':receiver'=>$idusers, ':sender'=>$idusers))[0];
        echo '<h1>View Message</h1>';
        echo htmlspecialchars($message['body']);
        echo '<hr />';

        if ($message['sender'] == $idusers) {
                $id = $message['receiver'];
        } else {
                $id = $message['sender'];
        }
        DB::query('UPDATE goodvibes.messages SET `read`=1 WHERE idmessages=:mid', array (':mid'=>$_GET['mid']));
        ?>
        <form action="send_message.php?receiver=<?php echo $id; ?>" method="post">
                <textarea name="body" rows="8" cols="80"></textarea>
                <input type="submit" name="send" value="Send Message">
        </form>
        <?php
} else {

?>
<h1>My Messages</h1>
<?php
$messages = DB::query('SELECT messages.*, users.username FROM goodvibes.messages, goodvibes.users WHERE (receiver=:receiver OR sender=:sender) AND users.idusers = messages.sender', array(':receiver'=>$idusers, ':sender'=>$idusers));
foreach ($messages as $message) {

        if (strlen($message['body']) > 10) {
                $m = substr($message['body'], 0, 10)." ...";
        } else {
                $m = $message['body'];
        }

        if ($message['read'] == 0) {
                echo "<a href='my_messages.php?mid=".$message['idmessages']."'><strong>".$m."</strong></a> sent by ".$message['username'].'<hr />';
        } else {
                echo "<a href='my_messages.php?mid=".$message['idmessages']."'>".$m."</a> sent by ".$message['username'].'<hr />';
        }

}
}
?>