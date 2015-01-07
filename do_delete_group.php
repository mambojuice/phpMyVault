<?php
include('resources/functions.php');

$gid = $_POST['gid'];

if (!(am_i_admin())) {
	header("location: show_permissions_error.php");
	}

delete_group($gid);

header("location: admin.php");
?>