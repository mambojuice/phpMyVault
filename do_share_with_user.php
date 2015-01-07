<?php
include('resources/functions.php');

$id = $_POST['id'];

if (!(get_owner($id) == get_my_uid())) {
	header("location: show_permissions_error.php");
	}

foreach ($_POST['uid'] as $uid) {
	share_with_user($id, $uid);
	}

header("location: edit.php?id=$id");
?>