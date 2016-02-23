<?php
	session_start();
	include 'feed.php';
	include 'gets.php';
	include 'db.php';
	
	//If no user ID in SESSION variable, send to login.
	if (!(isset($_SESSION['uid']) && $_SESSION['uid'] != '')) {
		header ("Location: login.php");
	}
	elseif (isset($_SESSION['uid'])) {
		$uid = $_SESSION['uid'];
		
		if ($_SESSION['image'] == "") {
			$userImage = "default.jpg";
		} else {
			$userImage = $_SESSION['image'];
		}
	}

	//Reset Error message
	$NameError = "";
	$InviteError = "";
	$OrgError = "";
	$DescError = "";

	if (isset($_POST['name'])) {

		//Create array for email invites.
		$InviteList = array();

		$Name = strip_tags($_POST['name']);
		$Org = strip_tags($_POST['org']);
		$Icon = strip_tags($_POST['icon']);
		$Desc = strip_tags($_POST['desc']);
		$Privacy = strip_tags($_POST['privacy']);

		$Name = mysqli_real_escape_string($con, $Name);
		$Org = mysqli_real_escape_string($con, $Org);
		$Icon = mysqli_real_escape_string($con, $Icon);
		$Desc = mysqli_real_escape_string($con, $Desc);
		$Privacy = mysqli_real_escape_string($con, $Privacy);

		$NameInvalid = 0;
		$InviteInvalid = 0;
		$OrgInvalid = 0;
		$DescInvalid = 0;

		//Check and validate all fields

		if ($Name != '') {
			if (strlen($Name) <= 30){
				//All good
			} else {
				$NameError="Please ensure your group name is less than 30 characters.";
				$NameInvalid = 1;
			}
		} else {
			$NameError="Please fill in a group name.";
			$NameInvalid = 1;
		}

		if ($Org != '') {
			if (strlen($Org) <= 40) {
				//All Good
			} else {
			$OrgError = "Please ensure your organisation name is less than 40 characters.";
			$OrgInvalid = 1;
			}
		}

		if ($Desc != '') {
			if (strlen($Desc) <= 300) {
				//All good
			} else {
			$DescError = "Please ensure your organisation name is less than 300 characters.";
			$DescInvalid = 1;
			}
		}

		//Check if first invite field has been filled out
		if (isset($_POST['invite0']) && $_POST['invite0'] != '') {
			$t=0;

			//Keep looping through and storing all invite fields until one is left empty.
			for ($i=0; $t==0; $i++) { 

				//Check that field is not empty, if it is, set t=1 (breaking the loop)
				if (isset($_POST['invite'.$i]) && $_POST['invite'.$i]!='') {

					//Strip and prepare email addresses
					$EmailInvite = strip_tags($_POST['invite'.$i]);
					$EmailInvite = mysqli_real_escape_string($con, $EmailInvite);

					//Check email is in correct format
					if (filter_var($EmailInvite, FILTER_VALIDATE_EMAIL)) {

						//Add entry to array
						$InviteList[$i] = $EmailInvite;
						//All Good

					} else {

						//If incorrect email format, set error message
						$InviteError = "One or more of your email invites is not formatted correctly.";
						$InviteInvalid = 1;
					}

				} else {
					//Set t=1, meaning there are no more inputs to read from, breaking the loop
					$t=1;
				}
			}
		}

	//If everything is valid.. Lets get to the nitty gritty...
	if ($NameInvalid == 0 && $InviteInvalid == 0 && $OrgInvalid == 0 && $DescInvalid == 0) {

		//Insert group details into group table as new entry
		$sql = "INSERT INTO `group` (name, description, adminId, icon, org, private)
		VALUES ('".$Name."','".$Desc."','".$_SESSION['uid']."','".$Icon."','".$Org."','".$Privacy."')";

		if (mysqli_query($con, $sql)) {

			//Get group ID from entry
			$id = mysqli_insert_id($con);

			//Insert member relationship for current user into database
			$relsql = "INSERT INTO `groupmember` (userid, groupid) VALUES ('".$_SESSION['uid']."','".$id."')";
			if (mysqli_query($con, $relsql)) {

				//Cycle through each invite entry
				for ($i=0; $i < count($InviteList); $i++) { 

					//Check if invitee already as an account
					$sql=mysqli_query($con, "SELECT email FROM `user` WHERE email='".$InviteList[$i]."'");
					if (mysqli_num_rows($sql) == 0) {

						//If user doesn't exist...
						//Generate random 11 character code for invitee to use to validate
						$randVerify = substr(md5(microtime()),rand(0,26),11);
	
						//Insert new users into database with 'half created accounts'
						$invitesql = "INSERT INTO `user` (email, activated, verify) VALUES ('".$InviteList[$i]."','0', '".$randVerify."')";
						if (mysqli_query($con, $invitesql)) {
	
							//Get new user (invitee) ID
							$NewUid = mysqli_insert_id($con);
	
							//Insert remlationship for new user to be a member of the new group
							$relsql2 = "INSERT INTO `groupmember` (userid, groupid) VALUES ('".$NewUid."','".$id."')";
							mysqli_query($con, $relsql2);
	
							//Set up email variables
							$to = $InviteList[$i];
							$subject = "Groupchat Invite";
							$url = "http://gchat.co/newinvite.php?verify=".$randVerify."&address=".$to;
							$GroupAdminName = $_SESSION['fName']." ".$_SESSION['lName'];
							
							//Email body
							$email_body = 	"<h2>You have been invited to Groupchat!</h2>
											<p>Hi there, you have been invited to '".$Name."' by ".$GroupAdminName.".</p>
											<p>To accept this invitation and complete your account creation, follow the link below.</p>
											<p>The link will expire in 24 hours.</p>
											<a rel=\"nofollow\" href=\"".$url."\">gchat.co/newinvite.php?[Masked]</a>";
							
							//Complete email
							$message = "<html><head></head><body><centre>".$email_body."</centre></body></html>";
	
							//Adding headers to set content type and from address
							$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
								'From: donotreply@gchat.co' . "\r\n" .
							   'Reply-To: donotreply@gchat.co' . "\r\n" .
							   'X-Mailer: PHP/' . phpversion();
							
							//Send email
							mail($to, $subject, $message, $headers);
	

	
						} else {
							//If could not insert new users
							$InviteError = "Your group was created, but there was an issue inviting users. Please try again, or ask them to join manually.";
						}
					} else {

						//Username already exists, so need to get that user ID
						$result=mysqli_query($con, "SELECT uid FROM `user` WHERE email='".$InviteList[$i]."'");
						while ($row = mysqli_fetch_array($result)) {
							
							//Check if relationship already exists between group and new invitee
							$sql=mysql_query("SELECT * FROM `groupmember` WHERE userid = '".$row['uid']."' AND groupid = '".$id."'");
							if (mysqli_num_rows($sql) == 0) {
								
								//If there is no relationship, we need to add one.
								//Insert remlationship for new user to be a member of the new group
								$relsql2 = "INSERT INTO `groupmember` (userid, groupid) VALUES ('".$row['uid']."','".$id."')	";
								mysqli_query($con, $relsql2);

							} else {
								//If relationship exists, do nothing. User is already a member.
							}

						}
					}
				}

			} else {
				//If could not assign user as member of database
				$InviteError = "There was an error processing your request. Please try again in a few minutes.";
			}

			//Redirect to new group
			header("Location: index.php?groupId=".$id."");
			
		} else {
			//If could not insert new group
			$InviteError = "There was an error processing your request. Please try again in a few minutes.";
		}
	}

	}



	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Dashboard</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/fontello.css">
		<link rel="stylesheet" type="text/css" href="css/roboto.css">
		<script src="js/jquery-2.1.4.js"></script>
		<script src="js/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		<script src="js/page.js"></script>
	</head>

	<body>

		<header>
			<ul id="nav">
				<li><a href="index.php"><i class="icon-dashboard"></i><span>Dashboard</span></a></li>
				<li><a href="#Profile"><i class="icon-profile"></i><span>Profile</span></a></li>
				<li><a href="#Messages"><i class="icon-messages"></i><span>Messages</span></a></li>
			</ul>

			<div id="logo"><i class="icon-logo"></i></div>

			<ul id="menu">
				<li id="search"><input type="text" name="search" placeholder="Group Search"><i class="icon-search"></i></li>
				<li id="notifications"><a href="#Notifications"><i class="icon-notifications"></i></a></li>
				<li id="name"><a href="#"><span><?php echo $_SESSION['fName'].' '. $_SESSION['lName']; ?></span><img id="ppic" src="css/images/profile/<?php echo $userImage; ?>"></a></li>
				<li id="logout"><a href="logout.php"><i class="icon-logout"></i><p>Logout</p></a></li>
			</ul>
		</header>

		<div id="NewGroup">
			<div id="NewGroupHead">
				<h2>Create a new group</h2>
				<p>You're just 3 steps away from creating your new online community!</p>
			</div>

			<form id="NewGroupForm" action="newgroup.php" method="post" accept-charset="UTF-8">
				<div class="step">
					<h3>Step 1 - Initial setup</h3>
					<input class="NewGroupTextInput" type="text" name="name" placeholder="Group Name" />
					<p id="NameError" class="GroupInvalid"><?php echo $NameError; ?></p>
					<input class="NewGroupTextInput" type="text" name="org" placeholder="Organisation (Optional)" />
					<p id="OrgError" class="GroupInvalid"><?php echo $OrgError; ?></p>
					<label id="StartIconLabel">Chose your group icon</label>
					<div id="icon-selector">

						<input id="icon-production" type="radio" name="icon" value="icon-production" checked="checked"/>
						<label class="icon-label" for="icon-production"><i class="icon-production"></i></label>	
										
						<input id="icon-glass" type="radio" name="icon" value="icon-glass" />
						<label class="icon-label" for="icon-glass"><i class="icon-glass"></i></label>

						<input id="icon-music" type="radio" name="icon" value="icon-music" />
						<label class="icon-label" for="icon-music"><i class="icon-music"></i></label>

						<input id="icon-human" type="radio" name="icon" value="icon-human" />
						<label class="icon-label" for="icon-human"><i class="icon-human"></i></label>

						<input id="icon-accounts" type="radio" name="icon" value="icon-accounts" />
						<label class="icon-label" for="icon-accounts"><i class="icon-accounts"></i></label>

						<input id="icon-administration" type="radio" name="icon" value="icon-administration" />
						<label class="icon-label" for="icon-administration"><i class="icon-administration"></i></label>

						<input id="icon-deployments" type="radio" name="icon" value="icon-deployments" />
						<label class="icon-label" for="icon-deployments"><i class="icon-deployments"></i></label>

						<input id="icon-marketing" type="radio" name="icon" value="icon-marketing" />
						<label class="icon-label" for="icon-marketing"><i class="icon-marketing"></i></label>

						<input id="icon-heart" type="radio" name="icon" value="icon-heart" />
						<label class="icon-label" for="icon-heart"><i class="icon-heart"></i></label>

						<input id="icon-gamepad" type="radio" name="icon" value="icon-gamepad" />
						<label class="icon-label" for="icon-gamepad"><i class="icon-gamepad"></i></label>

						<input id="icon-flight" type="radio" name="icon" value="icon-flight" />
						<label class="icon-label" for="icon-flight"><i class="icon-flight"></i></label>

						<input id="icon-award" type="radio" name="icon" value="icon-award" />
						<label class="icon-label" for="icon-award"><i class="icon-award"></i></label>

						<input id="icon-target" type="radio" name="icon" value="icon-target" />
						<label class="icon-label" for="icon-target"><i class="icon-target"></i></label>

						<input id="icon-leaf" type="radio" name="icon" value="icon-leaf" />
						<label class="icon-label" for="icon-leaf"><i class="icon-leaf"></i></label>

						<input id="icon-road" type="radio" name="icon" value="icon-road" />
						<label class="icon-label" for="icon-road"><i class="icon-road"></i></label>

						<input id="icon-briefcase" type="radio" name="icon" value="icon-briefcase" />
						<label class="icon-label" for="icon-briefcase"><i class="icon-briefcase"></i></label>

						<input id="icon-gift" type="radio" name="icon" value="icon-gift" />
						<label class="icon-label" for="icon-gift"><i class="icon-gift"></i></label>

						<input id="icon-chart-line" type="radio" name="icon" value="icon-chart-line" />
						<label class="icon-label" for="icon-chart-line"><i class="icon-chart-line"></i></label>

						<input id="icon-cab" type="radio" name="icon" value="icon-cab" />
						<label class="icon-label" for="icon-cab"><i class="icon-cab"></i></label>

						<input id="icon-beaker" type="radio" name="icon" value="icon-beaker" />
						<label class="icon-label" for="icon-beaker"><i class="icon-beaker"></i></label>

						<input id="icon-magic" type="radio" name="icon" value="icon-magic" />
						<label class="icon-label" for="icon-magic"><i class="icon-magic"></i></label>

						<input id="icon-coffee" type="radio" name="icon" value="icon-coffee" />
						<label class="icon-label" for="icon-coffee"><i class="icon-coffee"></i></label>

						<input id="icon-food" type="radio" name="icon" value="icon-food" />
						<label class="icon-label" for="icon-food"><i class="icon-food"></i></label>

						<input id="icon-diamond" type="radio" name="icon" value="icon-diamond" />
						<label class="icon-label" for="icon-diamond"><i class="icon-diamond"></i></label>
					</div>
					<textarea name="desc" placeholder="Group Description (Recommended)"></textarea>
					<p id="DescError" class="GroupInvalid"><?php echo $DescError; ?></p>
				</div>

				<div class="step">
					<h3>Step 2 - Is your group private? (Invite only)</h3>
					<fieldset id="privacy">

						<input id="public" type="radio" name="privacy" checked="checked" value="0">
						<label class="privacy-label" for="public"><span></span>Public</label>

						<input id="private" type="radio" name="privacy" value="1">
						<label class="privacy-label" for="private"><span></span>Private</label>

					</fieldset>
				</div>

				<div class="step">
					<h3>Step 3 - Invite Users</h3>
					<label id="invite-label">Invite users to this group by entering their email addresses below. The user doesn't have an account? No worries, we'll create their temporary account and invite them for you.</label>
					<div id="inviteList">
						<input class="userInvite" type="text" name="invite0" placeholder="Email Address" />
						<div id="addInvite"><i class="icon-add"></i></div>
					</div>
				</div>
				<p id="InviteError" class="GroupInvalid"><?php echo $InviteError; ?></p>
				<input type="submit" value="Create Group"/>
			</form>
		</div>


	</body>
</html>