<?php
require_once('resources/functions.php');
require_once('resources/config.php');

// Check login, redirect to login page if necessary
check_session_login();

if (isset($_GET['fid'])) {
	$fid = $_GET['fid'];
}
else {
	$fid = get_personal_folder_fid(get_my_uid());
}

$PAGE['title'] = get_folder_name($fid);
$PAGE['sidebar'] = true;
$PAGE['sidebar-page'] = "pages/folder_list.php";
$PAGE['content-page'] = "pages/password_list.php";

include('pages/render.php');
?>