<?php
include('resources/functions.php');

$gid = $_POST['gid'];
$group_name = strtolower($_POST['group_name']);
$description = $_POST['description'];

edit_group($gid, $group_name, $description);
	
header("Location: admin.php");

?>