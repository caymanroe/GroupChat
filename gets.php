<?php

function listGroups($userId, $con) {
	$selectGroupId = mysqli_query($con, "SELECT groupid FROM groupmember WHERE userid=".$userId." LIMIT 10");

	while ($row = mysqli_fetch_array($selectGroupId)) {

		$selectGroups = mysqli_query($con, "SELECT name, icon FROM `group` WHERE groupId=".$row['groupid']." ");

		$groupName="";
		$groupIcon="";

		while ($groupRow = mysqli_fetch_array($selectGroups)) {
			$groupName = $groupRow['name'];
			$groupIcon = $groupRow['icon'];
		}

		if ($groupName == NULL && $groupIcon == NULL) {
			echo "<li><a id=\"\" class=\"listoption\" href=\"#\"><span>No Groups</span></a></li>";
		}
		else {
			echo "<li><a id=\"".$row['groupid']."\" class=\"listoption\" href=\"#".$groupName."\"><i class=\"".$groupIcon."\"></i><span>".$groupName."</span></a></li>";
		}

	}
}

function listFriends($uid, $con) {
	$selectRelationship = mysqli_query($con, "SELECT user1, user2 FROM `friend` WHERE (user1=".$uid." AND status=1) OR (user2=".$uid." AND status=1) ");
								
	while ($rowRelation = mysqli_fetch_array($selectRelationship)) {

		if ($rowRelation['user1'] != $uid ) {
										
			$selectFriend = mysqli_query($con, "SELECT fname, lname FROM `user` WHERE uid=".$rowRelation['user1']."");

			while ($rowFriend = mysqli_fetch_array($selectFriend)) {
				echo "<li><a class=\"listoption\" href=\"#Friend\"><i class=\"icon-profile\"></i><span>".$rowFriend['fname'].' '.$rowFriend['lname']."</span></a></li>";
			}
		}
		elseif ($rowRelation['user1'] == $uid ) {

			$selectFriend = mysqli_query($con, "SELECT fname, lname FROM `user` WHERE uid=".$rowRelation['user2']."");

			while ($rowFriend = mysqli_fetch_array($selectFriend)) {
				echo "<li><a class=\"listoption\" href=\"#Friend\"><i class=\"icon-profile\"></i><span>".$rowFriend['fname'].' '.$rowFriend['lname']."</span></a></li>";
			}
		}
	}
}

function getPosts($groupId, $con, $isFeed = 0, $postId=0) {

	if ($isFeed == 0 && $postId==0) {
		if ($_SESSION['image'] == "") {
			$userImage = "default.jpg";
		} else {
			$userImage = $_SESSION['image'];
		}
		echo "<div id=\"newPost\">";
		echo "<img src=\"css/images/profile/".$userImage."\">";
		echo "<textarea id=\"postNewBox\" name=\"post\" placeholder=\"What's up, ".$_SESSION['fName']."?\" maxlength=\"3000\" rows=\"1\" data-min-rows=\"1\"></textarea>";
		echo "<div id=\"send\"><i class=\"icon-send\"></i></div>";
		echo "</div>";
		echo "<div id=\"postlist\">";
	}

	if ($postId!=0) {
		$selectFeedPosts = mysqli_query($con, "SELECT * FROM `post` WHERE groupid in (".$groupId.") AND uid='".$_SESSION['uid']."' ORDER BY id DESC LIMIT 1");
	}
	else {
		$selectFeedPosts = mysqli_query($con, "SELECT * FROM `post` WHERE groupid in (".$groupId.") ORDER BY id DESC");
	}
						
	while ($rowPost = mysqli_fetch_array($selectFeedPosts)) {

		$selectPostUser = mysqli_query($con, "SELECT fname, lname, image FROM `user` WHERE uid=".$rowPost['uid']."");

		while ($rowPostUser = mysqli_fetch_array($selectPostUser)) {

			if ($rowPostUser['image']=="") {
				$image = "default.jpg";
			} else {
				$image = $rowPostUser['image'];
			}

			$postTime= strtotime($rowPost['datesubmitted']);

			echo "<div id=\"".$rowPost['id']."\" class=\"post\">";
			echo "<img class=\"postPic\" src=\"css/images/profile/".$image."\">";
			echo "<div class=\"postInfo\">";
			echo "<a class=\"postUser\">".$rowPostUser['fname']." ".$rowPostUser['lname']."</a>";

			if ($_SESSION['uid']==$rowPost['uid']) {
				
				echo "<div class=\"deletePost\"><i class=\"icon-remove\"></i></div>";

			}

			echo "<p class=\"postContent\">".$rowPost['content']."</p>";
			echo "<a class=\"postShowComments\">Show all comments |</a>";
			echo "<p class=\"postTime\">".humanTiming($postTime)."</p>";
			echo "<a class=\"postDelete\"></a>";
			echo "</div>";


			echo "<div class=\"commentList\">";
			getComments($rowPost['id'], $con);
			echo "</div>";

			echo "<div class=\"commentNew\">";
			echo "<textarea class=\"commentNewBox\" name=\"message\" placeholder=\"Add a comment...\" maxlength=\"1000\" rows=\"1\" data-min-rows=\"1\"></textarea>";
			echo "</div>";
			echo "</div>";
		}
	}
	echo "</div>";
}

function getComments($postId, $con, $commentId=0) {

	if ($commentId!=0) {
		$selectComments = mysqli_query($con, "SELECT * FROM `comment` WHERE postid =".$postId." AND uid='".$_SESSION['uid']."' ORDER BY id DESC LIMIT 1");
	}
	else {
		$selectComments = mysqli_query($con, "SELECT * FROM `comment` WHERE postid =".$postId." ");
	}

	while ($rowComment = mysqli_fetch_array($selectComments)) {

		$selectCommentUser = mysqli_query($con, "SELECT fname, lname FROM `user` WHERE uid=".$rowComment['uid']."");

		while ($rowCommentUser = mysqli_fetch_array($selectCommentUser)) {

			$commentTime= strtotime($rowComment['datesubmitted']);

			//echo "<div class=\"commentList\">";
			echo "<div id=\"".$rowComment['id']."\" class=\"comment\">";
			echo "<a class=\"commentUser\">".$rowCommentUser['fname']." ".$rowCommentUser['lname']."</a>";
			echo "<p class=\"commentTime\">".humanTiming($commentTime)."</p>";
			
			if ($_SESSION['uid']==$rowComment['uid']) {
				
				echo "<div class=\"deleteComment\"><i class=\"icon-remove\"></i></div>";

			}

			echo "<p class=\"commentContent\">".$rowComment['content']."</p>";
			echo "<a class=\"commentDelete\"></a>";
			echo "</div>";
			//echo "</div>";
		}
	};
}

function humanTiming($time)
{

    $time = time() - $time; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'').' ago';
    }
}



?>