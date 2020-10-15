<?php
  include('includes/db_connection.php');

  if(isset($_POST['createaccount'])) {
      $username = $_POST['username'];
      $fullname = $_POST['fullname'];
      $PWD = $_POST['password'];
    //   $email = $_POST['email'];

     DB::query('INSERT INTO users VALUES (:username, :fullname, :password)', array(':username'=>$username, ':fullname'=>$fullname, ':password'=>$PWD));
    // $stmt = $conn->prepare("INSERT INTO users(username, fullname, password, email) VALUES (?,?,?)");
    // $stmt->bind_param("sss", $username, $name, $PWD, $email);
      echo "Success!";
  }
  ?>

<h1>Register</h1>
<form action="registration.php" method="post">
<input type="text" name="username" value="" placeholder="Username ..."></p>
<input type="text" name="fullname" value="" placeholder="Full Name ..."></p>
<input type="password" name="password" value="" placeholder="Password ..."></p>
<input type="email" name="email" value="" placeholder="someone@somesite.com"></p>
<input type="submit" name="createaccount" value="Create Account">
</form>