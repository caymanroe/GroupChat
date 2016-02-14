<?php
	session_start();
	include 'db.php';
	include 'gets.php';

	$groupList = mysqli_real_escape_string($con, $_POST['group']);
	$access = "denied";
	$selectgroup = mysqli_query($con, "SELECT groupid FROM groupmember WHERE userid=".$_SESSION['uid']."");
	
	while ($row = mysqli_fetch_array($selectgroup)) {
		if ($row['groupid'] == $groupList) {
			$access = "granted";
			$_SESSION['activeGroup'] = $groupList;
		}
	}

	if ($access == "denied") {
		echo "You don't have permission to view this.";
	}
	elseif ($access == "granted") {
		getPosts($groupList, $con, 0);
	}
	
?>