<?php

require_once('resources/functions.php');

if ($name=$_GET['term']) {

	$search_results = ldap_user_search($name);
	$aUsers = array();
	
	foreach ($search_results as $user) {
		$username = $user["samaccountname"][0];
		if ($username != null) {
			array_push($aUsers, $username);
			}
		}
	
	echo json_encode($aUsers);
}

?>