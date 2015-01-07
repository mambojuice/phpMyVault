<?php
include('resources/functions.php');

$id = $_POST['id'];

if (!(get_owner($id) == get_my_uid())) {
	header("location: show_permissions_error.php");
	}

foreach ($_POST['gid'] as $gid) {
	share_with_group($id, $gid);
	}

header("location: edit.php?id=$id");
?>