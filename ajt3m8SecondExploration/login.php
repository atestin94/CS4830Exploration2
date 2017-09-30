<?php
  session_start();

  include '/var/www/alextestin/hidden/connect.php';
  include '/var/www/alextestin/hidden/rememberme.php';

  // Check if logged in
  if(isset($_SESSION['username'])) {
    header('location: http://alextest.in');
    exit;
  }

  // Verify user
  if(isset($_POST['login'])) {
    if(!empty($_POST['botblocker'])) {
      header('location: http://alextest.in/login.php');
      exit;
    }
    $username = mysqli_real_escape_string($link, $_POST['username']);
    $password = mysqli_real_escape_string($link, $_POST['password']);
    $password = sha1($password);
    $sql = 'SELECT * FROM users WHERE username = "' . $username . '" AND password = "' . $password . '"';
    $result = mysqli_query($link, $sql);
    if(mysqli_num_rows($result) >= 1) {
      $sql = 'SELECT * FROM users WHERE username = "' . $username . '" AND password = "' . $password . '" AND activated = "1"';
      $result = mysqli_query($link, $sql);
      if(mysqli_num_rows($result) >= 1) {
        $_SESSION['username'] = $username;
        if(isset($_POST['rememberMe'])) {
          setcookie('username', $username, strtotime( '+30 days' ), '/', '', '', TRUE);
    		  setcookie('password', $password, strtotime( '+30 days' ), '/', '', '', TRUE);
        }
        header('location: http://alextest.in');
        exit;
      } else {
        $_SESSION['error'] = 'Account Not Activated';
        header('location: login.php');
        exit;
      }
    } else {
      $_SESSION['error'] = 'Username/Password combination incorrect';
      header('location: login.php');
      exit;
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#1b252e">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sign in to your cloud storage account at alextest.in">
    <title>Alex Testin - Login</title>
    <link rel="shortcut icon" href="images/tabicon.png">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Electrolize">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Pacifico">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poiret+One">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Abel">
    <link rel="stylesheet" type="text/css" href="mini.css">
  </head>
  <body>

    <?php include 'header.php';?>

    <div class="topSpacer"></div>

    <?php
      if(isset($_SESSION['confirmation'])) {
        echo '<div id="confirm_msg">' . $_SESSION['confirmation'] . '</div>';
        unset($_SESSION['confirmation']);
      }
    	if (isset($_SESSION['error'])) {
    		echo "<div id='error_msg'>".$_SESSION['error']."</div>";
    		unset($_SESSION['error']);
    	}
    ?>

    <form class="content formContent" method="post" action="login.php">
      <div class="innerContent" id="altContent">
        <div class="sectionTitle">
          Sign In To Your Account
        </div>
        <input type="text" name="username" class="textInputAlt" placeholder=" Username" aria-label="username"><br><br>
        <input type="password" name="password" class="textInputAlt" placeholder=" Password" aria-label="password"><br>
        <div id="remMeCB">
          <input type="checkbox" name="rememberMe" value="remember" aria-label="remember me"> Remember Me<br>
        </div>
        <button class="button loginBtn" type="submit" name="login" value="Login">Login</button><br><br>
        <div>New user? Register <a href="register.php">Here</a></div>
      </div>
    </form>

  </body>
</html>
