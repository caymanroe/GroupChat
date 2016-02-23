<?php
	session_start();

	if (session_status() != PHP_SESSION_NONE) {
	    session_destroy();
	    session_start();
	}
	
	$wrong = "";
	if (isset($_POST['email']) && isset($_POST['password']) && $_POST['password'] != "") {
		include 'db.php';

		$email = strip_tags($_POST['email']);
		$pass = strip_tags($_POST['password']);

		$email = mysqli_real_escape_string($con, $email);
		$pass = mysqli_real_escape_string($con, $pass);

		$sql = "SELECT * FROM user WHERE email = '$email' LIMIT 1";

		$query = mysqli_query($con, $sql);
		$row = mysqli_fetch_row($query);

		$dbemail = $row[0];
		$dbhash = $row[3];

		if (password_verify($pass, $dbhash)) {
			$_SESSION['uid'] = $row[6];
			$_SESSION['fName'] = $row[1];
			$_SESSION['lName'] = $row[2];
			$_SESSION['activated'] = $row[5];
			$_SESSION['lastLogin'] = $row[12];
			$_SESSION['image'] = $row[14];

			header("Location: index.php");
		}

		else {
			$wrong = "Incorrect Username/Password";
		}


	}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Groupchat</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/mobile.css">
		<link rel="stylesheet" type="text/css" href="css/roboto.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	</head>

	<body>
		<div id="login_wrap">
			<div id="welcome">
				<h1><span>Welcome to </span>Groupchat.</h1>
				<p>Connect with friends, colleagues, communities and share your ideas today.</p>
			</div>
			
			<div id="forms">
				<form name="new" id="login" action="login.php" method="post" enctype="multipart/form-data">
					
					<div id="usernamediv">
						<input type="text" name="email" id="username" class="text-input" placeholder="Email">
					</div>

					<div id="passworddiv">
						<input type="password" name="password" id="password" class="text-input" placeholder="Password">
					</div>

					<input type="submit" value="Log in" name="Submit" id="submit_btn" class="button">
					
					<p id="wrong"><?php echo $wrong; ?></p>
					<p id="signup_link">New here? <a href="signup.php">Create an account</a></p>

				</form>
			</div>
			
		</div>

		<script src="js/jquery-2.1.4.js" type="text/javascript"></script>
		<script type="text/javascript" src="js/jquery.backstretch.min.js"></script>
		<script type="text/javascript">

			var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ? true : false;

			jQuery(document).ready(function($){
    			if(!isMobile) {
					$.backstretch([
					"css/images/login1b.jpg",
					"css/images/login2b.jpg",
					"css/images/login3b.jpg",
					"css/images/login4b.jpg",
					"css/images/login5b.jpg",
					"css/images/login6b.jpg",
					"css/images/login7b.jpg",
					"css/images/login8b.jpg",
					"css/images/login9b.jpg",
					"css/images/login10b.jpg",
					"css/images/login11b.jpg",
					"css/images/login12b.jpg"
					], {duration: 5000, fade: 2000});
    			}
			});


		</script>
	</body>

</html>