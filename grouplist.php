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

		$searchTermStrip = strip_tags($_POST['groupSearch']);
		$searchTerm = mysqli_real_escape_string($con, $searchTermStrip);
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Dashboard</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/fontello.css">
		<link rel="stylesheet" type="text/css" href="css/roboto.css">
		<link rel="stylesheet" type="text/css" href="css/animation.css">
		<link rel="stylesheet" type="text/css" href="css/fontello-codes.css">
		<link rel="stylesheet" type="text/css" href="css/fontello-embedded.css">
		<link rel="stylesheet" type="text/css" href="css/fontello-ie7.css">
		<link rel="stylesheet" type="text/css" href="css/fontello-ie7-codes.css">
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

		<div id="groupList-head">
			<h2>Showing matching results for <span>'<?php echo $searchTerm?>'</span>.</h2>
		</div>

		<div id="groupList">
			<?php

				$selectGroupResult = mysqli_query($con, "SELECT * FROM `group` WHERE name LIKE '%".$searchTerm."%'");

				while ($rowGroup = mysqli_fetch_array($selectGroupResult)) {
					echo "<div id=\"".$rowGroup['groupId']."\" class=\"group\">";
					echo "<i class=\"".$rowGroup['icon']." icon\"></i>";
					echo "<h2>".$rowGroup['name']."</h2>";
					echo "<h3>".substr($rowGroup['datecreated'], 0, 4)."</h3>";
					if (strlen($rowGroup['description'])>70) {
						echo "<p>".substr($rowGroup['description'], 0, 75)."...</p>";
					} else {
						echo "<p>".$rowGroup['description']."</p>";
					}
					
					
					$ifJoinedResult = mysqli_query($con, "SELECT groupid FROM `groupmember` WHERE userid=".$uid."");
					$isJoined = 0;
					while ($rowIfJoined = mysqli_fetch_array($ifJoinedResult)) {
						if ($rowGroup['groupId']==$rowIfJoined['groupid']) {
							$isJoined=1;
						}
					}
					if ($isJoined==1) {
						echo "<div class=\"joinedIcon joined\"><i class=\"icon-ok\"></i></div>";
					} else {
						echo "<div class=\"joinedIcon canjoin\"><i class=\"icon-add\"></i></div>";
						echo "<div class=\"joinedIconLoading\"><i style=\"color: #C5C5C5;\" class=\"icon-spin animate-spin\"></i></div>";
						echo "<div class=\"joinedIcon loaded\"><i class=\"icon-ok\"></i></div>";
					}	
					echo "</div>";
				}

			?>
		</div>
	</body>
</html>