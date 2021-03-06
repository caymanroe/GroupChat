<?php

	session_start();
	include 'db.php';
	include 'gets.php';

if(isset($_POST['action']) && !empty($_POST['action'])) {
	$action = $_POST['action'];
	switch($action) {
		case 'newpost' : newPost($con, $_POST['text']);break;
		case 'newcomment' : newComment($con, $_POST['text'], $_POST['postId']);break;
		case 'removepost' : removePost($con, $_POST['postId']);break;
		case 'removecomment' : removeComment($con, $_POST['commentId']);break;
		case 'joingroup' : joinGroup($con, $_POST['groupId']);break;
		case 'checknotifications' : checknotifications($con);break;
		case 'changeseen' : changeseen($con, $_POST['relid']);break;
		case 'clearnotif' : clearnotif($con);break;
		
	}
}	

function newPost($con, $text_not_escaped) {

	$text = mysqli_real_escape_string($con, $text_not_escaped);

	if (!empty($_POST['text'])) {
		$date = date('Y-m-d H:i:s');
		$sql = "INSERT INTO post (uid, content, datesubmitted, groupid)
				VALUES ('".$_SESSION['uid']."', '".$text."', '".$date."', '".$_SESSION['activeGroup']."')";
		if (mysqli_query($con, $sql)) {
			
			$selectNewPost = mysqli_query($con, "SELECT id FROM `post` WHERE groupid='".$_SESSION['activeGroup']."' AND uid='".$_SESSION['uid']."' LIMIT 1");
			while ($rowNewPost = mysqli_fetch_array($selectNewPost)) {
				getPosts($_SESSION['activeGroup'], $con, 0, $rowNewPost['id']);
			}
		} else {
		echo "Error submitting post.";
		}
	}
}

function newComment($con, $text_not_escaped, $postId) {

	$text = mysqli_real_escape_string($con, $text_not_escaped);
	//$post = mysqli_real_escape_string($con, $text_not_escaped);
	$post = $postId;

	if (!empty($_POST['text'])) {
		$date = date('Y-m-d H:i:s');
		$sql = "INSERT INTO comment (uid, content, datesubmitted, postid)
				VALUES ('".$_SESSION['uid']."', '".$text."', '".$date."', '".$post."')";
		if (mysqli_query($con, $sql)) {
			
			$selectNewComment = mysqli_query($con, "SELECT id FROM `comment` WHERE postid='".$post."' AND uid='".$_SESSION['uid']."' LIMIT 1");
			while ($rowNewComment = mysqli_fetch_array($selectNewComment)) {
				getComments($post, $con, $rowNewComment['id']);
			}
		} else {
		echo "Error submitting Comment.";
		}
	}
}	

function removePost($con, $post) {
	$result = mysqli_query($con, "SELECT uid FROM `post` WHERE id=".$post."");
	while ($row = mysqli_fetch_array($result)) {
		if ($row['uid']==$_SESSION['uid']) {
			
			$sql = "DELETE FROM `post` WHERE id=".$post."";
			
			if (mysqli_query($con, $sql)) {
			    //Successfully deleted
			} else {
			    echo "Error deleting record: " . mysqli_error($con);
			}

		} else {
			echo "You do not have permission to delete this post.";
		}
	}
}

function removeComment($con, $comment) {
	$result = mysqli_query($con, "SELECT uid FROM `comment` WHERE id=".$comment."");
	while ($row = mysqli_fetch_array($result)) {
		if ($row['uid']==$_SESSION['uid']) {
			
			$sql = "DELETE FROM `comment` WHERE id=".$comment."";
			
			if (mysqli_query($con, $sql)) {
			    //Successfully deleted
			} else {
			    echo "Error deleting record: " . mysqli_error($con);
			}

		} else {
			echo "You do not have permission to delete this comment.";
		}
	}
}

function joingroup($con, $group) {
	if (!empty($_POST['groupId'])) {
		$sql = "INSERT INTO `groupmember` (userid, groupid) VALUES ('".$_SESSION['uid']."','".$group."')";

		if (mysqli_query($con, $sql)) {
			echo "1";
		} else {
			echo "Error: " . $sql . "<br>" . mysqli_error($con);
		}
	}
}

function checknotifications($con) {

	//Find all unseen group memberships
	$notifResult = mysqli_query($con, "SELECT groupid, id FROM `groupmember` WHERE userid='".$_SESSION['uid']."' AND seen='0'");
	if (mysqli_num_rows($notifResult) == 0) {
		echo "<p id=\"NoNotifications\">You have no notifications!</p>";
	} else {

		while ($rowNotif = mysqli_fetch_array($notifResult)) {

			//Get the group name and admin ID for each group
			$groupResult = mysqli_query($con, "SELECT adminId, name FROM `group` WHERE groupId='".$rowNotif['groupid']."' LIMIT 1");
			while ($rowGroup = mysqli_fetch_array($groupResult)) {
				
				//Get the admin name for the invite
				$adminResult = mysqli_query($con, "SELECT fname, lname FROM `user` WHERE uid='".$rowGroup['adminId']."' LIMIT 1");
				while ($rowAdmin = mysqli_fetch_array($adminResult)) {
					if ($_SESSION['uid'] == $rowGroup['adminId']) {
						echo "<a class=\"notify\" id=\"".$rowNotif['id']."\" href=\"index.php?groupId=".$rowNotif['groupid']."\">You have created the group <span>".$rowGroup['name']."</span></a>";
					} else {
						echo "<a class=\"notify\" id=\"".$rowNotif['id']."\" href=\"index.php?groupId=".$rowNotif['groupid']."\">".$rowAdmin['fname']." ".$rowAdmin['lname']." added you to <span>".$rowGroup['name']."</span></a>";
					}
				}
			}
		}
		echo "<a id=\"clearNotifications\" href=\"#\">Clear all</a>";
	}
}

function changeseen($con, $relid) {
	$sql = "UPDATE groupmember SET seen = '1' WHERE id = '".$relid."'";
	mysqli_query($con, $sql);
}

function clearnotif($con) {
	//echo "Test";
	$seenResult = mysqli_query($con, "SELECT * FROM `groupmember` WHERE userid='".$_SESSION['uid']."' AND seen='0'");
	while ($rowSeen = mysqli_fetch_array($seenResult)) {
		$sqlSeen = "UPDATE `groupmember` SET seen = '1' WHERE id = '".$rowSeen['id']."'";
		mysqli_query($con, $sqlSeen);
	}
}

?>