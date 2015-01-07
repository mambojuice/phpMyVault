<?php

include('resources/functions.php');

$id = $_POST['id'];

if (!(get_owner($id) == get_my_uid())) {
	header("location: show_permissions_error.php");
	}
else {

	$name = $_POST['name'];
	$login = encrypt_string(htmlspecialchars($_POST['login']));
	$password = encrypt_string(htmlspecialchars($_POST['password']));
	$note = sqlescape(htmlspecialchars($_POST['note']));

	edit_password_object($id, $name, $login, $password, $note);

	header("Location: show.php?id=$id");

	}


?>