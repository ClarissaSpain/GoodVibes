<?php
include('includes/db_connection.php');
include('includes/Login.php');
if (Login::isLoggedIn()) {
        $idusers = Login::isLoggedIn();
} else {
        die('Not logged in');
}

if (isset($_POST['send'])) {

        if (DB::query('SELECT idusers FROM goodvibes.users WHERE idusers=:receiver', array(':receiver'=>$_GET['receiver']))) {

                DB::query("INSERT INTO goodvibes.messages VALUES (NULL, :body, :sender, :receiver, 0)", array(':body'=>$_POST['body'], ':sender'=>$idusers, ':receiver'=>htmlspecialchars($_GET['receiver'])));
                echo "Message Sent!";
        } else {
                die('Invalid ID!');
        }
}
?>
<h1>Send a Message</h1>
<form action="send_message.php?receiver=<?php echo htmlspecialchars($_GET['receiver']); ?>" method="post">
        <textarea name="body" rows="8" cols="80"></textarea>
        <input type="submit" name="send" value="Send Message">
</form>