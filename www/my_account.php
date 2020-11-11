<?php
include('upload.php');
//make sure user is logged in
include('includes/db_connection.php');
include('includes/Login.php');
if (Login::isLoggedIn()) {
        $idusers = Login::isLoggedIn();
} else {
        die('Not Logged in');
}
?>

<h1>My Account</h1>
<!-- I changed the action to go to upload.php -->
<form action="upload.php" method="post" enctype="multipart/form-data">
        Upload a profile image:
        <input type="file" name="file">
        <input type="submit" name="uploadprofileimg" value="Upload Image">
</form>