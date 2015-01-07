<?php
include('resources/functions.php');

$group_name = sqlescape(strtolower($_POST['group_name']));
$description = sqlescape($_POST['description']);

add_group($group_name, $description);
	
header("Location: admin.php");

?>