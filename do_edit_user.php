<?php
include('resources/functions.php');

$uid = $_POST['uid'];

if (!am_i_admin()) {
	header("location: show_permissions_error.php");
	}
else {
	
	$type = get_user_type($uid);
	$new_password = $_POST['password'];
	$confirm_new_password = $_POST['confirm_password'];
	$admin = $_POST['admin'];
	
	if ($type == 'local') {
		if ($new_password != '') {
			if ($new_password == $confirm_new_password) {
				update_user_password($uid, $new_password);
			}
			else {
				header("location: edit_user.php?uid=$uid&message=\"Passwords do not match\"");
			}
		}
	}
	
	//Grant admin rights if applicable
	if ($admin == 'on') {
		grant_user_admin_rights($uid);
	}
	
	if ($admin != 'on') {
		remove_user_admin_rights($uid);
		}
	
	}
	
header("Location: admin.php");

?>