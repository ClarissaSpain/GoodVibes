<?php
 include('includes/db_connection.php');
//checking to see if the form has been submitted...
 if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (DB::query('SELECT username FROM goodvibes.users WHERE username=:username', array(':username'=>$username))) {
            //verify password to the username entered via database
            if(password_verify($password, DB::query('SELECT password FROM goodvibes.users WHERE username=:username', array(':username'=>$username))[0]['password'])) {
                echo 'Logged in';
                //set cookie for login token
                $cstrong = True;
                $token = bin2hex(openssl_random_pseudo_bytes( 64 , $cstrong));
                echo $token;
                 $idusers = DB::query('SELECT idusers FROM goodvibes.users WHERE username=:username', array(':username'=>$username))[0]['idusers'];
                DB::query('INSERT INTO goodvibes.login_tokens VALUES (NULL, :token, :idusers)', array(':token'=>sha1($token), ':idusers'=>$idusers));

                 setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
                 header("Location: profile.php?username=$username");
                 



            } else {
                echo 'Wrong password';
            }

        } else {
            echo 'User not registered';
        }
 }
 

?>



<h1>Login to your account</h1>


<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="https://static.pingendo.com/bootstrap/bootstrap-4.3.1.css">
  <link rel="stylesheet" href="../css/style.scss">
</head>

<body >
  <div class="py-5 text-center" style="background-image: url('https://static.pingendo.com/cover-bubble-dark.svg');background-size:cover;">
    <div class="container">
      <div class="row">
        <div class="mx-auto col-md-6 col-10 bg-white p-5">
          <h1 class="mb-4">Log in</h1>
          <form action="login.php" method="post">
            <input type="text" class="form-control" name="username" value="" placeholder="Username ..." id="username"></p>
            <input type="password" class="form-control" name="password" value="" placeholder="Password ..." id="password"></p>
            <small class="form-text text-muted text-right">
                <a href="/forgot_password.php"> Recover password</a>
              </small>
            <input type="submit" name="login" value="Login" id="but_submit" class="btn btn-primary" >
          </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script>
  $(document).ready(function(){
    $("#but_submit").click(function(){
        var username = $("#usernamee").val().trim();
        var password = $("#password").val().trim();

        if( username != "" && password != "" ){
            $.ajax({
                url:'login.php',
                type:'post',
                data:{username:username,password:password},
                success:function(response){
                    var msg = "";
                    if(response == 1){
                        window.location = "profile.php";
                    }else{
                        msg = "Invalid username and password!";
                    }
                    $("#message").html(msg);
                }
            });
        }
    });
});
  </script>
</body>

</html>