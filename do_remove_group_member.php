<?php
include('resources/functions.php');

$gid = $_POST['gid'];

if (!(am_i_admin())) {
	header("location: show_permissions_error.php");
	}

foreach ($_POST['user'] as $uid) {
	remove_user_from_group($gid, $uid);
	}

header("location: edit_group.php?gid=$gid");
?>