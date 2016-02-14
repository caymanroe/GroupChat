<?php
	session_start();
	include 'db.php';
	include 'gets.php';

	$selectGroupId = mysqli_query($con, "SELECT groupid FROM groupmember WHERE userid=".$_SESSION['uid']." LIMIT 1");
						
	$row = mysqli_fetch_array($selectGroupId);
						
	$groupList = $row['groupid'];
	$_SESSION['activeGroup'] = NULL;

	$selectGroupId = mysqli_query($con, "SELECT groupid FROM groupmember WHERE userid=".$_SESSION['uid']." LIMIT 1, 18446744073709551615");

	while ($row = mysqli_fetch_array($selectGroupId)) {
		$groupList = $groupList.", ".$row['groupid'];
	}

	if ($groupList != '') {
		getPosts($groupList, $con, 1);
	} else {
		echo "Nothing to show";
	}
?>