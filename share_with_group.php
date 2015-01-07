<?php
require_once('header.php');

$id = $_POST['id'];

// Check permissions
if (!(get_owner($id) == get_my_uid())) {
	// We don't have permissions!	?>
	<p class="bg-danger center-block">ACCESS DENIED</p>
<?php	}
else {
	// Permissions are good
	$action = $_POST['action'];
	
	if ($action == 'add') {
		require_once('resources/share_with_group.php');
		}
	if ($action == 'remove') {
		require_once('resources/confirm_remove_group.php');
		}
	}
?>