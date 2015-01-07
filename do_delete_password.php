<?php
include('resources/functions.php');

$id = $_POST['id'];

if (!(get_owner($id) == get_my_uid())) {
	header("location: show_permissions_error.php");
	}

delete_password($id);

header("location: index.php");
?>