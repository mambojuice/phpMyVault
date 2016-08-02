<?php
include('resources/functions.php');
check_session_login();
$oid = $_GET['oid'];
$uid = get_my_uid();
echo get_decrypted_password($oid, $uid);
?>