<?php
	session_start();

	include '/var/www/alextestin/hidden/log.php';

	// Delete cookies and sessions related to user
	if(isset($_COOKIE["username"]) && isset($_COOKIE["password"])) {
	  setcookie("username", '', strtotime( '-5 days' ), '/');
		setcookie("password", '', strtotime( '-5 days' ), '/');
	}
	session_destroy();
	unset($_SESSION['username']);
	header("location: http://alextest.in");
?>
