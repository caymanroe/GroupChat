<?php

	//include db.php to use $con
	include 'db.php';

	session_start();

	//If user is already logged in with an active account, reset current session.
	if (isset($_SESSION['uid'])) {
		session_destroy();
		session_start();
	}

	$invalid = "";

	//If the username gets posted from logging in, run this. (When the form is submitted)
	if (isset($_POST['firstname'])) {


		//Strip html and php tags from username and password, and set.
		$newFirstname = strip_tags($_POST['firstname']);
		$newLastname = strip_tags($_POST['lastname']);
		$newEmail = strip_tags($_POST['email']);
		$newPassword = strip_tags($_POST['password']);
		$newPassword2 = strip_tags($_POST['password2']);

		//remove special characters for SQL query
		$newFirstname = mysqli_real_escape_string($con, $newFirstname);
		$newLastname = mysqli_real_escape_string($con, $newLastname);
		$newEmail = mysqli_real_escape_string($con, $newEmail);
		$newPassword = mysqli_real_escape_string($con, $newPassword);
		$newPassword2 = mysqli_real_escape_string($con, $newPassword2);

		//If passwords match, continue...
		if ($newPassword == $newPassword2) {
			
			//If any fields are blank, show error. Else, continue...
			if ($newFirstname == "" || $newLastname == "" || $newEmail == "" || $newPassword == "" || $newPassword2 == "") {
				$invalid = "Don't leave any blanks.";
			}

			else {

				//Check is email already exists by querying the database..
				$sql=mysqli_query($con, "SELECT uid FROM user WHERE email = '".$newEmail."'") or die(mysqli_error($con));
				if(mysqli_num_rows($sql) == 0) {
				    
				    //Convert names to uppercase
					$newFirstname = ucwords($newFirstname);
					$newLastname = ucwords($newLastname);

					//Hash password
					$options = array('cost' => 13);
					$hash = password_hash($newPassword, PASSWORD_BCRYPT, $options);

					//If password was hashed correctly, continue...
					if (password_verify($newPassword, $hash)) {

	    				$hash = mysqli_real_escape_string($con, $hash);

	    				//If profile picture was selected, do stuff... else, bypass..
						if(isset($_FILES['file']) && $_FILES['file']['name'] != ''){
							
							//Defining required variables
							$allowed =  array('jpeg','jpg');
							$filename = $_FILES['file']['name'];
							$ext = pathinfo($filename, PATHINFO_EXTENSION);
							
							//Check if file is a jpeg
							if(!in_array($ext,$allowed) ) {
    							$invalid = "Images must be in JPEG format.";
							}
							else {

								//Set profile picture name to random string of 20 characters
								$length = 20;
								$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
								$temp_filename = $randomString.".jpg";

								//Move uploaded picture to temp folder for compression and resizing to be performed
								$pathToImage = "css/images/temp/".$temp_filename;
								move_uploaded_file($_FILES['file']['tmp_name'], $pathToImage);
									
								//Setting source
								$imgSrc = $pathToImage;

								//Getting the image dimensions
								list($width, $height) = getimagesize($imgSrc);

								//Saving the image into memory (for manipulation with GD Library)
								$myImage = imagecreatefromjpeg($imgSrc);

								//Calculating the part of the image to use for thumbnail
								if ($width > $height) {
									$y = 0;
									$x = ($width - $height) / 2;
									$smallestSide = $height;
								} else {
									$x = 0;
									$y = ($height - $width) / 2;
									$smallestSide = $width;
								}

								//Copying the part into thumbnail
								$thumbSize = 50;
								$thumb = imagecreatetruecolor($thumbSize, $thumbSize);
								imagecopyresampled($thumb, $myImage, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);

								//Final output
								imagejpeg($thumb, 'css/images/profile/'.$temp_filename);
								unlink($pathToImage);
								
								//Insert user info including image and complete account creation.
								$sql = "INSERT INTO user (email, hash, fname, lname, image) VALUES ('".$newEmail."', '".$hash."', '".$newFirstname."', '".$newLastname."', '".$temp_filename."')";
								if (mysqli_query($con, $sql)) {
									
									//Verify user exists and set up session variables
									$result = mysqli_query($con, "SELECT uid FROM user WHERE email='".$newEmail."' LIMIT 1");
									$row = mysqli_fetch_assoc($result);
									$_SESSION['uid'] = $row['uid'];
									$_SESSION['fName'] = $newFirstname;
									$_SESSION['lName'] = $newLastname;
									$_SESSION['activated'] = 1;
									$_SESSION['lastLogin'] = "";
									$_SESSION['image'] = $temp_filename;
									header("Location: index.php");
								}
								else {
									//If the update query failed...
							    	$invalid = "A serious error has occured. Please try again later. Code: 12";
								}

							}

						}

						else {
							//If no profile picture has been selected...
							$sql = "INSERT INTO user (email, hash, fname, lname) VALUES ('".$newEmail."', '".$hash."', '".$newFirstname."', '".$newLastname."')";
							if (mysqli_query($con, $sql)) {
								
								//Verify user exists and set up session variables
								$result = mysqli_query($con, "SELECT uid FROM user WHERE email='".$newEmail."' LIMIT 1");
								$row = mysqli_fetch_assoc($result);
								$_SESSION['uid'] = $row['uid'];
								$_SESSION['fName'] = $newFirstname;
								$_SESSION['lName'] = $newLastname;
								$_SESSION['activated'] = 1;
								$_SESSION['lastLogin'] = "";
								header("Location: index.php");
							}
							else {
								//If the Update query failed...
						    	$invalid = "A serious error has occured. Please try again later. Code: 11";
							}
						    
						}


					
					}
					else {
						//If the password hashing failed...
					    $invalid = "A serious error has occured. Please try again later. Code: 10";
					}
				}
				
				else {
				    //If email already exists
					$invalid = "This email address is already registered.";
				} 	
				
			}

		}
		else {
			//If passwords do not match...
			$invalid = "Please ensure your passwords match and try again.";
		}
	}

function scaleImage($source, $max_width, $max_height, $destination) {
    list($width, $height) = getimagesize($source);
    if ($width > 150 || $height > 150) {
        $ratioh = $max_height / $height;
        $ratiow = $max_width / $width;
        $ratio = min($ratioh, $ratiow);
        // New dimensions
        $newwidth = intval($ratio * $width);
        $newheight = intval($ratio * $height);

        $newImage = imagecreatetruecolor($newwidth, $newheight);

        $exts = array("gif", "jpg", "jpeg", "png");
        $pathInfo = pathinfo($source);
        $ext = trim(strtolower($pathInfo["extension"]));

        $sourceImage = null;

        // Generate source image depending on file type
        switch ($ext) {
        case "jpg":
        case "jpeg":
            $sourceImage = imagecreatefromjpeg($source);
            break;
        case "gif":
            $sourceImage = imagecreatefromgif($source);
            break;
        case "png":
            $sourceImage = imagecreatefrompng($source);
            break;
        }

        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        // Output file depending on type
        switch ($ext) {
        case "jpg":
        case "jpeg":
            imagejpeg($newImage, $destination);
            break;
        case "gif":
            imagegif($newImage, $destination);
            break;
        case "png":
            imagepng($newImage, $destination);
            break;
        }
    }
}

?>


<!DOCTYPE html>
<html style="background-color: rgb(42, 55, 73);">
	<head>
		<meta charset="UTF-8">
		<title>Groupchat</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/roboto.css">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
	</head>

	<body id="signupbody" style="background-color: rgb(42, 55, 73);">
		<div id="signup_wrapper">
			<div id="signup_box">
				<h1>Join Groupchat.</h1>
				<form name="signup" action="signup.php" method="post" enctype="multipart/form-data">
					<input type="text" name="firstname" id="firstname_signup" class="signup_input" placeholder="First name">
					<input type="text" name="lastname" id="lastname_signup" class="signup_input" placeholder="Last name">
					<input type="text" name="email" id="username_signup" class="signup_input" placeholder="Email">
					<input type="password" name="password" id="password_signup" class="signup_input" placeholder="Password">
					<input type="password" name="password2" id="password2_signup" class="signup_input" placeholder="Password">
					<div id="file_cover" onclick="getFile()">Profile Picture</div>
					<p id="pic_info">Optional. Make sure your image subject (your face!) fills most of the frame, as this picture will get compressed into a small thumbnail.</p>
					<div style='height: 0px;width:0px; overflow:hidden;'><input id="file" type="file" name="file"/></div>
					<input type="submit" value="Sign up" name="signup_submit" id="signup_submit">
					<p id="invalid"> <?php echo $invalid; ?></p>
				</form>
				<p id="login_link">Already have an account? <a href="login.php">Log in</a></p>
				<p id="terms">This website is currently under alpha status. By signing up you agree not to share sensitive data, as security and privacy may be compromised due to exploits in the application code. Groupchat cannot be held liable for loss of data or manipulation of content by outside entities. This site is in a testing state only.</p>
			</div>
		</div>

		<script type="text/javascript">
			function getFile(){
        		document.getElementById("file").click();
    		}
		</script>
		<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="js/jquery.backstretch.min.js"></script>

	</body>
</html>