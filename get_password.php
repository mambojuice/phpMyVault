<?php
include('resources/functions.php');
check_session_login();

$id = $_GET['id'];
$uid = get_my_uid();

echo get_decrypted_password($id, $uid);
?>