<?php
  include '/var/www/alextestin/hidden/rememberme.php';
  include '/var/www/alextestin/hidden/log.php';
?>

<div id="headerSpacer"></div>
<div class="header">
  <div class="navbar">
    <div id="headerTitle">
      <div class="homeicon">
        <a class="blueAccent" href="http://alextest.in">
          Alex<span id="orangeAccent">.</span>
        </a>
      </div>
      <div class="menuimg" onclick="showmenu()"></div>
    </div>
    <div id="nav" class="menuicon"><ul>
      <li><a class="head menuTab" href="http://alextest.in">Home</a></li>
      <li><a class="head menuTab" href="files.php">My Files</a></li>
      <li><a class="head menuTab" href="upload.php">Upload</a></li>
      <li><a class="head menuTab" href="resume.pdf">Resume</a></li>
      <li><a class="head menuTab" href="contact.php">Contact</a></li>
      <li class="loginMobile">

        <?php
          if (isset($_SESSION['username'])) {
            echo '<a class="head menuTab" href="account.php">Account</a></li>';
            echo '<li class="loginMobile"><a class="head menuTab" href="logout.php">Logout</a>';
          } else {
            echo '<a class="head menuTab" href="login.php">Sign In</a>';
          }
        ?>

      </li>
      <li class="loginDesktop">

        <?php
          // Check if logged in
          if (isset($_SESSION['username'])) {
            $username = preg_replace('#[^a-z0-9]#i', '', $_SESSION['username']);
            echo '
              <div class="dropdown">
                <div onclick="dropdownLogin()">
                  <span class="menuTab">Hello ' . $username . '</span>
                </div>
                <div id="loginMenu" class="dropdown-content userOptions">
                  <div class="accBtnPad">
                    <a href="account.php" class="head button accBtn">
                      My Account
                    </a>
                  </div>
                  <form method="post" action="logout.php">
                    <button class="button">Logout</button>
                  </form>
                </div>
              </div>
            </li>
            ';
          } else {
            echo '
              <div class="dropdown">
                <div onclick="dropdownLogin()">
                  <span class="menuTab">Sign In</span>
                </div>
                <div id="loginMenu" class="dropdown-content">
                  <form method="post" action="login.php">
                    <div class="dropdownInput">
                      <input id="botblocker" class="hide" type="text" name="botblocker" value="" placeholder="Ignore this field" aria-label="ignore">
                      <input type="text" name="username" placeholder=" Username" class="textInput" id="firstinput" aria-label="username">
                    </div>
                    <div class="dropdownInput">
                      <input type="password" name="password" placeholder=" Password" class="textInput" aria-label="password">
                    </div>
                    <div class="remCB">
                      <input type="checkbox" name="rememberMe" value="remember" aria-label="remember me"> Remember Me<br>
                    </div>
                    <button class="button" type="submit" name="login">Sign In</button>
                  </form>
                  <br>
                  New User? Register <a href="register.php" id="altLink">Here</a>
                </div>
              </div>
            </li>
            ';
          }
        ?>

        <script>
          function dropdownLogin() {
            document.getElementById('loginMenu').classList.toggle('show');
            <?php
              if(!isset($_SESSION['username'])) {
                echo 'document.getElementById("firstinput").focus();';
                // Hides honeypot field
                echo 'document.getElementById("botblocker").style.display = "none";';
              }
            ?>
          }

          function showmenu() {
            document.getElementById('nav').classList.toggle('show');
          }
        </script>

    </ul></div>
  </div>
</div>
