<?php
require_once('resources/functions.php');
require_once('resources/config.php');

// Check login, redirect to login page if necessary
check_session_login();

// Add a user
if ($_GET['action'] == 'add') {
	$login = $_POST['login'];
	$password = $_POST['password'];
	$confirm = $_POST['confirm'];
	$type = $_POST['type'];
	$admin = $_POST['admin'];
	
	// Check password
	if (!($password == $confirm)) {
		header("Location: add_user.php?error=password");
	}
	else {
		// Add local user
		if ($type == 'local') {
			add_local_user($login, $password);
		}
		
		// Add LDAP user
		if ($type == 'ldap') {
			add_ldap_user($login);
		}
		
		// Grant admin rights
		if ($admin == 'true') {
			grant_user_admin_rights(get_uid($login));
		}
		
		// Redirect back to admin page
		header("Location: admin.php");
	}
}

// No data? Display the form
$PAGE['title'] = "Add New User";
$PAGE['sidebar'] = false;
$PAGE['content-page'] = "pages/add_user_form.php";

include('pages/render.php');
?>
