<?php

session_start();

if (isset($_SESSION['uid'])) {
	session_destroy();
	header("location: login.php");
}

elseif (!isset($_SESSION['uid'])) {
	echo 'You have not logged in yet.';
}

?>