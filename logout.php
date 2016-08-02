<?php
require_once('resources/functions.php');
require_once('resources/config.php');

// Check login, redirect to login page if necessary
check_session_login();

kill_my_session();

header("Location: index.php");

?>