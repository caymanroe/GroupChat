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
		<script src="js/page.js"></script>

	</head>

	<body>

		<header>
			<ul id="nav">
				<li><a href="#Dashboard"><i class="icon-dashboard"></i><span>Dashboard</span></a></li>
				<li><a href="#Profile"><i class="icon-profile"></i><span>Profile</span></a></li>
				<li><a href="#Messages"><i class="icon-messages"></i><span>Messages</span></a></li>
			</ul>

			<div id="logo"><i class="icon-logo"></i></div>

			<ul id="menu">
				<li id="notifications"><a href="#Notifications"><i class="icon-notifications"></i></a></li>
				<li id="name"><a href="#"><span><?php echo $_SESSION['fName'].' '. $_SESSION['lName']; ?></span><img id="ppic" src="css/images/profile/<?php echo $userImage; ?>"></a></li>
				<li id="logout"><a href="logout.php"><i class="icon-logout"></i><p>Logout</p></a></li>
			</ul>
		</header>
		
		<div id="wrapper">
			
			<aside>
				<ul>
					<li><a id="feedbutton" class="listoption" href="#Feed"><i class="icon-feed"></i><span>Feed</span></a></li>
					<li><a class="listoption" href="#NewGroup"><i class="icon-add"></i><span>New Group</span></a></li>
					
					<li>
						<ul id="groupbar" class="listcontainer">
							<p class="sidebar-heading">My Groups |</p>
							<a class="list-seemore" href="#more">See all</a>
							<?php listGroups($_SESSION['uid'], $con); ?>	
						</ul>

						<ul id="friendbar" class="listcontainer">
							<p class="sidebar-heading">My Friends |</p>
							<a class="list-seemore" href="#more">See all</a>
							<?php listFriends($_SESSION['uid'], $con); ?>
						</ul>
					</li>
				</ul>
			</aside>

			<div id="display" data-g="0"></div>

		</div>
		<script type="text/javascript">
			
		</script>
	</body>
</html>