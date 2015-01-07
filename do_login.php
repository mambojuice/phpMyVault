<?php

include("resources/functions.php");

$login = $_POST["user"];
$password = $_POST["password"];

if (check_login($login, $password)) {
	successful_login($login);
	header("Location: index.php");
	}
else {
	echo "Fail!";
	header("Location: login.php?login=unsuccessful&user=$login");
	}
	
?>