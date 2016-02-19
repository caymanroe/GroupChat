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

			<form id="NewGroupForm" action="submit.php" method="post" accept-charset="UTF-8">
				<div class="step">
					<h3>Step 1 - Initial setup</h3>
					<input class="NewGroupTextInput" type="text" name="name" placeholder="Group Name" />
					<input class="NewGroupTextInput" type="text" name="org" placeholder="Organisation (Optional)" />
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
					<textarea name="desc" placeholder="Group Description"></textarea>
				</div>

				<div class="step">
					<h3>Step 2 - Is your group private? (Invite only)</h3>
					<fieldset id="privacy">

						<input id="public" type="radio" name="privacy" checked="checked" value="public">
						<label class="privacy-label" for="public"><span></span>Public</label>

						<input id="private" type="radio" name="privacy" value="private">
						<label class="privacy-label" for="private"><span></span>Private</label>

					</fieldset>
				</div>

				<div class="step">
					<h3>Step 3 - Invite Users</h3>
					<label id="invite-label">Invite users to this group by entering their email addresses below. The user doesn't have an account? No worries, we'll create their temporary account and invite them for you.</label>
					<div id="inviteList">
						<input class="userInvite" type="text" name="invite1" placeholder="Email Address" />
						<div id="addInvite"><i class="icon-add"></i></div>
					</div>
				</div>
				<input
				<input type="submit" value="Create Group"/>
			</form>
		</div>


	</body>
</html>