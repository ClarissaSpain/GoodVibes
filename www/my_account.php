<?php
include('upload.php');
?>

<h1>My Account</h1>
<form action="my_account.php" method="post" enctype="multipart/form-data">
        Upload a profile image:
        <input type="file" name="file">
        <input type="submit" name="uploadprofileimg" value="Upload Image">
</form>