<?php

	session_start();
	$wrong = "";
	if (isset($_POST['email'])) {
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

<!--<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Groupchat</title>
		<link rel="stylesheet" type="text/css" href="js/jquery-2.1.4.js">
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>

	<body>
			
	<div data-role="page">

		<div data-role="header">
			<h1>Login</h1>
		</div>
		
		<div role="main" class="ui-content">
				<form name="new" id="login" action="login.php" method="post" enctype="multipart/form-data">
					
					<div id="emaildiv">
						<input type="email" name="email" id="email" class="text-input" placeholder="Email">
					</div>

					<div id="passworddiv">
						<input type="password" name="password" id="password" class="text-input" placeholder="Password">
					</div>

					<input type="submit" value="Log in" name="Submit" id="submit_btn" class="button">

				</form>
		</div>
			
	</div>
		
		<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>


	</body>
</html>-->

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Groupchat</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/roboto.css">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
	</head>

	<body>
		<div id="login_wrap">
			<div id="welcome">
				<h1>Welcome to Groupchat.</h1>
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
					
					<p id="wrong"> <?php echo $wrong; ?> </p>
					<p id="signup_link">New here? <a href="signup.php">Create an account</a></p>

				</form>
			</div>
			
		</div>
		
		<script src="js/jquery-2.1.4.js" type="text/javascript"></script>
		<script type="text/javascript" src="js/jquery.backstretch.min.js"></script>

	</body>
	<script type="text/javascript">
		$.backstretch([
			"css/images/login2.jpg",
			"css/images/login1.jpg",
			"css/images/login4.jpg",
			"css/images/login3.jpg"
			], {duration: 5000, fade: 2000});    
	</script>
</html>