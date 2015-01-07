<?php
include('resources/functions.php');

$type = $_POST['type'];
$login = strtolower($_POST['login']);
$password = $_POST['password'];
$admin = $_POST['admin'];

//Create user (LDAP)
if ($type == "ldap") {
	add_ldap_user($login);
	}
	
//Create user (Local)
if ($type == "local") {
	add_local_user($login, $password);
	}
	
//Grant admin rights if applicable
if ($admin == 'on') {
	grant_user_admin_rights(get_uid($login));
	}
	
header("Location: admin.php");

?>