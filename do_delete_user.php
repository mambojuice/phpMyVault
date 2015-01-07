<?php
include('resources/functions.php');

$uid = $_POST['uid'];

if (!(am_i_admin())) {
	header("location: show_permissions_error.php");
	}

delete_user($uid);

header("location: admin.php");
?>