<?php
  session_start();

  include '/var/www/alextestin/hidden/connect.php';
  include '/var/www/alextestin/hidden/rememberme.php';

  // Check if logged in
  if(isset($_SESSION['username'])) {
    header('location: http://alextest.in');
    exit;
  }

  // Check if username is valid and available
  if(isset($_POST['usernamecheck'])) {
    $username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
    $sql = 'SELECT id FROM users WHERE username = "' . $username . '" LIMIT 1';
    $query = mysqli_query($link, $sql);
    $uname_check = mysqli_num_rows($query);
    if(strlen($username) < 3 || strlen($username) > 16) {
	    echo '<div class="signUpError">Must be 3 - 16 characters</div>';
	    exit();
    }
    if(is_numeric($username[0])) {
	    echo '<div class="signUpError">Must begin with a letter</div>';
	    exit();
    }
    if($uname_check < 1) {
	    echo 'ok';
	    exit();
    } else {
	    echo '<div class="signUpError">This username is already taken!</div>';
	    exit();
    }
  }

  // Check completion of all fields
  if(isset($_POST['user'])) {
    if(!empty($_POST['botblocker2'])) {
      header('location: http://alextest.in/register.php');
      exit;
    }
    $user = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
	  $email = mysqli_real_escape_string($link, $_POST['email']);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $pass = mysqli_real_escape_string($link, $_POST['pass']);
	  $fname = preg_replace('#[^a-z]#i', '', $_POST['fname']);
	  $lname = preg_replace('#[^a-z]#i', '', $_POST['lname']);

    $sql = 'SELECT id FROM users WHERE username="' . $user . '" LIMIT 1';
    $query = mysqli_query($link, $sql);
	  $u_check = mysqli_num_rows($query);

    $sql = 'SELECT id FROM users WHERE email="' . $email . '" LIMIT 1';
    $query = mysqli_query($link, $sql);
	  $e_check = mysqli_num_rows($query);

    if($user == '' || $email == '' || $pass == '' || $fname == '' || $lname == '') {
  		echo 'The form submission is missing values.';
      exit();
    } else if(is_numeric($user[0])) {
      echo 'Username cannot begin with a number';
      exit();
  	} else if($u_check > 0) {
      echo 'The username you entered is alreay taken';
      exit();
  	} else if($e_check > 0) {
      echo 'That email address is already in use in the system';
      exit();
  	} else if(strlen($user) < 3 || strlen($user) > 16) {
      echo 'Username must be between 3 and 16 characters';
      exit();
    } else if(strlen($pass) < 8 || strlen($pass) > 32) {
      echo 'Password must be between 8 and 32 characters';
      exit();
    } else {
      $pass = sha1($pass);
      include_once '/var/www/alextestin/hidden/rand.php';
      $activation = randStrGen(32);
      $sql = 'INSERT INTO users(username, password, email, fname, lname, activated) VALUES ("' . $user . '", "' . $pass . '", "' . $email . '", "' . $fname . '", "' . $lname . '", "' . $activation . '")';
      mysqli_query($link, $sql);
      $dir_path = "/var/www/alextestin/storage/" . strtolower($user) . "/";
      if (!file_exists($dir_path)) {
        mkdir($dir_path);
        chmod($dir_path, 0777);
      }
      $id = mysqli_insert_id($link);
// Begin Email
      $to = $email;
      $from = 'no-reply@alextest.in';
      $subject = $user . ' Account Activation';
      $message = '
        <!DOCTYPE html><html><head><meta charset="UTF-8"><title>Alex-Test.in Message</title></head><body><div style="padding:10px;font-size:24px;">AlexTest.in Account Activation</div><div style="padding:24px; font-size:17px;">Hello ' . $fname . ' ' . $lname . ',<br><br>Thank you for registering at alextest.in!<br>Click the link below to log in and activate your account:<br><br><a href="http://alextest.in/activation.php?id=' . $id . '&user=' . $user . '&activation=' . $activation . '">http://alextest.in/activation.php?id=' . $id . '&user=' . $user . '&activation=' . $activation . '</a></div></body></html>
      ';
      $headers = "From: " . $from . "\r\n";
      $headers .= "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      if(mail($to, $subject, $message, $headers)) {
        echo 'signup_success';
        exit;
      }
      echo 'Failed to send email';
      exit;
// End Email
    }
    exit;
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#1b252e">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Create a cloud account at alextest.in">
    <title>Alex Testin - Register</title>
    <link rel="shortcut icon" href="images/tabicon.png">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Electrolize">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Pacifico">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poiret+One">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Abel">
    <link rel="stylesheet" type="text/css" href="mini.css">
    <script>
      function ajaxObj(method, url) {
        var x = new XMLHttpRequest();
        x.open( method, url, true );
        x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        return x;
      }

      function ajaxReturn(x) {
        if(x.readyState == 4 && x.status == 200) {
          return true;
        }
      }

      // Prevent certain characters for certain fields
      function restrict(elem) {
      	var tf = document.getElementById(elem);
      	var rx = new RegExp;
      	if(elem == 'email') {
          rx = /[^a-z0-9_\!\@\#\$\%\^\&\*\+\-\=\?\`\~\{\}\|\.\[\]]/gi;
      	} else if(elem == 'username') {
      		rx = /[^a-z0-9]/gi;
      	} else if(elem == 'fname' || elem == 'lname') {
      		rx = /[^a-z]/gi;
      	}
      	tf.value = tf.value.replace(rx, '');
      }

      // Check if username is valid and available
      function checkusername() {
      	var user = document.getElementById('username').value;
      	if(user != '') {
      		document.getElementById('unamestatus').innerHTML = 'checking ...';
      		var ajax = ajaxObj('POST', 'register.php');
          ajax.onreadystatechange = function() {
      	    if(ajaxReturn(ajax) == true) {
              if(ajax.responseText == 'ok') {
                document.getElementById('unamestatus').innerHTML = '<div class="signUpSuccess">' + user + ' is available!</div>';
                document.getElementById('username').classList.remove('errorFocus');
              } else {
      	        document.getElementById('unamestatus').innerHTML = ajax.responseText;
                document.getElementById('username').classList.add('errorFocus');
              }
      	    }
          }
          ajax.send('usernamecheck=' + user);
      	}
        else {
          document.getElementById('unamestatus').innerHTML = '<br>';
        }
      }

      // Sends sign up info to php
      function signup() {
        var user = document.getElementById('username').value;
        var pass = document.getElementById('password').value;
        var passc = document.getElementById('passconfirm').value;
        var email = document.getElementById('email').value;
        var fname = document.getElementById('fname').value;
        var lname = document.getElementById('lname').value;
        var status = document.getElementById('status');
        if(user == '' || pass == '' || passc == '' || email == '' || fname == '' || lname == '') {
          status.innerHTML = 'Fill out all of the form info';
        } else if(pass != passc) {
          status.innerHTML = 'Your passwords do not match';
        } else {
          document.getElementById('signupbtn').style.display = 'none';
          status.innerHTML = '<span style="color:black">please wait ... </span>';
          var ajax = ajaxObj('POST', 'register.php');
          ajax.onreadystatechange = function() {
            if(ajaxReturn(ajax) == true) {
              if(ajax.responseText != 'signup_success') {
                status.innerHTML = ajax.responseText;
                document.getElementById('signupbtn').style.display = 'inline';
              } else {
                document.getElementById('altContent').innerHTML = 'OK ' + fname + ', check your email inbox and junk mail box at <u>' + email + '</u> in a moment to complete the sign up process by activating your account. You will not be able to do anything on the site until you successfully activate your account.';
              }
            }
          }
          ajax.send('user=' + user + '&pass=' + pass + '&email=' + email + '&fname=' + fname + '&lname=' + lname);
        }
      }

      // Hides honeypot field
      document.getElementById('botblocker2').style.display = 'none';
    </script>
  </head>
  <body>

    <?php include 'header.php';?>

    <div class="topSpacer"></div>

    <h1 class="hide">Alex Testin resume website IT information technology CS computer science engineering mizzou univerity of missouri cloud storage student</h1>
    <h2 class="hide">Alex Testin resume website IT information technology CS computer science engineering mizzou univerity of missouri cloud storage student</h2>
    <h3 class="hide">Alex Testin resume website IT information technology CS computer science engineering mizzou univerity of missouri cloud storage student</h3>

    <form class="content formContent" name="signupform" onsubmit="return false;">
      <div class="innerContent" id="altContent">
        <div class="sectionTitle">
          Create Your Account
        </div>
        <!-- Honeypot field to reduce spam -->
        <input id="botblocker2" class="hide" type="text" name="botblocker2" value="" placeholder="Ignore this field" aria-label="ignore">
        <input id="username" type="text" class="textInputAlt" placeholder=" Username" onblur="checkusername()" onkeyup="restrict('username')" maxlength="16" aria-label="username">
          <span class="infoIcon">&#9432;
            <span class="infoBox">
              - 3 to 16 characters<br>
              - Letters and numbers only<br>
              - Must begin with a letter
            </span>
          </span><br>
        <div id="unamestatus"><br></div>
        <input id="password" type="password" class="textInputAlt" placeholder=" Password" aria-label="password">
          <span class="infoIcon">&#9432;
            <span class="infoBox">
              - 8 to 32 characters<br>
            </span>
          </span><br><br>
        <input id="passconfirm" type="password" class="textInputAlt" placeholder=" Confirm Password" aria-label="confirm password"><br><br>
        <input id="email" type="email" class="textInputAlt" placeholder=" Email" onkeyup="restrict('email')" aria-label="email"><br><br>
        <input id="fname" type="text" class="textInputAlt" placeholder=" First Name" onkeyup="restrict('fname')" aria-label="first name"><br><br>
        <input id="lname" type="text" class="textInputAlt" placeholder=" Last Name" onkeyup="restrict('lname')" aria-label="last name"><br><br>
        <button id="signupbtn" class="button" type="submit" value="Register" onclick="signup()">Register</button><br>
        <div id="status" class="signUpError"><br></div>
        Already a user? <a href="login.php">Sign In</a>
      </div>
    </form>
  </body>
</html>
