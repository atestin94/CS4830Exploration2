<?php
  session_start();

  include '/var/www/alextestin/alextestin/hidden/connect.php';
  include '/var/www/alextestin/hidden/rememberme.php';
  include '/var/www/alextestin/hidden/log.php';

  // Check if logged in
  if(isset($_SESSION['username'])) {
    header('location: http://alextest.in');
    exit;
  }

  // Make sure URL parameters are set
  if(isset($_GET['id']) && isset($_GET['user']) && isset($_GET['activation'])) {
    $id = preg_replace('#[^0-9]#i', '', $_GET['id']);
  	$user = preg_replace('#[^a-z0-9]#i', '', $_GET['user']);
    $activation = preg_replace('#[^a-z0-9]#i', '', $_GET['activation']);
    $sql = 'SELECT * FROM users WHERE id = "' . $id . '" AND username = "' . $user . '" AND activated = "1" LIMIT 1';
    $query = mysqli_query($link, $sql);
    $numrows = mysqli_num_rows($query);
    if($numrows == 1){
      $_SESSION['error'] = 'Account has already been activated';
      header('location: login.php');
      exit();
    }
  	$sql = 'SELECT * FROM users WHERE id = "' . $id . '" AND username = "' . $user . '" AND activated = "' . $activation . '" LIMIT 1';
    $query = mysqli_query($link, $sql);
  	$numrows = mysqli_num_rows($query);
  	if($numrows == 0){
      $_SESSION['error'] = 'Invalid Activation ID';
  		header('location: login.php');
      exit();
  	}
    // Sets activated field to 1 to activate account
  	$sql = 'UPDATE users SET activated = "1" WHERE id = "' . $id . '" AND username = "' . $user . '" LIMIT 1';
    if($query = mysqli_query($link, $sql)) {
      $_SESSION['confirmation'] = 'Account Activated! Please Sign In';
      header('location: login.php');
      exit();
    } else {
      $_SESSION['error'] = 'Activation Failed (Database Error)';
      header('location: login.php');
      exit();
    }
  } else {
    $_SESSION['error'] = 'Invalid Activation Link';
  	header('location: login.php');
    exit();
  }
?>
