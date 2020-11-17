<?php
  include('includes/db_connection.php');
//   require_once('PHPMailer/PHPMailerAutoload.php');

  if(isset($_POST['createaccount'])) {
      $username = $_POST['username'];
      $fullname = $_POST['fullname'];
      $password = $_POST['password'];
      $email = $_POST['email'];

      //check to see if username matches other usernames in the database:

      if(!DB::query('SELECT username FROM goodvibes.users WHERE username=:username', array(':username'=>$username))){

        //check to see the length of the username
        if (preg_match('/[a-zA-Z0-9_]+/', $username)){
          //check for username expression
          if (preg_match(' /^[A-Za-z][A-Za-z0-9]{5,31}$/', $username)){
            //check for length of password
              if (strlen($password)>= 6 && strlen($password)<= 60){
            //check email through php also using PHP hash to hash the password to keep it secure
              if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                //check if email is already used
                if (!DB::query('SELECT email FROM goodvibes.users WHERE email=:email', array(':email'=>$email))) {
          DB::query('INSERT INTO goodvibes.users VALUES (NULL, :username, :fullname, :password, :email, NULL)', array(':username'=>$username, ':fullname'=>$fullname, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));  
    
          header("Location: login.php");
                }else {
                  echo 'Email in use!';
                }
            } else {
                                        echo 'Invalid email!';
                                }
                        } else {
                                echo 'Invalid password!';
                        }
                        } else {
                                echo 'Invalid username';
                        }
                } else {
                        echo 'Invalid username';
                }

        } else {
                echo 'User already exists!';
        }
}
  ?>


<!-- <input type="text" name="username" value="" placeholder="Username ..."></p>
<input type="text" name="fullname" value="" placeholder="Full Name ..."></p>
<input type="password" name="password" value="" placeholder="Password ..."></p>
<input type="email" name="email" value="" placeholder="someone@somesite.com"></p>
<input type="submit" name="createaccount" value="Create Account">
</form> -->
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="https://static.pingendo.com/bootstrap/bootstrap-4.3.1.css">
</head>

<body >
  <div class="py-5 text-center" style="background-image: url('https://static.pingendo.com/cover-bubble-dark.svg');background-size:cover;">
    <div class="container">
      <div class="row">
        <div class="mx-auto col-md-6 col-10 bg-white p-5">
          <h1 class="mb-4">Register</h1>
          <form action="registration.php" method="post">
            <div class="form-group"> <input type="text" name="username" class="form-control" id="username" placeholder="Username ...">
              <div class="form-group"><label></label><input type="text" name="fullname" class="form-control" placeholder="Full Name ..." id="fullname">
                <div class="form-group"><label></label><input type="email" name="email" class="form-control" placeholder="Enter email ..." id="email"></div>
              </div>
            </div>
            <div class="form-group mb-3"> <input type="password" name="password" class="form-control" placeholder="Password ..." id="password"> <small class="form-text text-muted text-right">
                <p>Already have an Account? <a href="login.php">Click Here!</a></p>
                <a href="forgot_password.php"> Recover password</a>
              </small> </div> <button type="submit" name="createaccount" value="Create Account" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous" style=""></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous" style=""></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous" style=""></script>
</body>

</html>