<?php

$id = $_POST['id'];
$action = strtolower($_POST['action']);

if ($action == "edit") {
	header("Location: edit.php?id=$id");
	}
if ($action == "show") {
	header("Location: show.php?id=$id");
	}
if ($action == "delete") {
	header("Location: delete.php?id=$id");
	}

?>