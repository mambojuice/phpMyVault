<?php
require_once('resources/functions.php');
require_once('resources/config.php');

// Check login, redirect to login page if necessary
check_session_login();

// No data? Display the form
$PAGE['title'] = "Administration";
$PAGE['sidebar'] = false;
$PAGE['content-page'] = "pages/admin_form.php";

include('pages/render.php');
?>
