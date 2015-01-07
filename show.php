<?php require_once('header.php'); ?>

<?php
$id = $_GET['id'];

// Check permissions
if (!(check_object_permissions($id, get_my_uid()))) {
	// We don't have permissions!	?>
	<div class="bg-danger center-block">ACCESS DENIED</div>
<?php	}
else {
	// Permissions are good
$pass_entry = get_sql_results("SELECT * FROM data WHERE id=$id");

$name = $pass_entry[0]['name'];
$login = decrypt_string($pass_entry[0]['login']);
$password = $pass_entry[0]['password'];
$note = $pass_entry[0]['note'];
$owner = $pass_entry[0]['owner'];
$i_am_owner = ($owner == get_my_uid());

$placeholder = "(click to show for 10 seconds)";

?>

<!-- Fancy javascript stuff -->
<script>
$(document).ready(function() {
	$('#thepassword').click(function() {
		$('#thepassword').load('get_password.php?id=<?php echo $id;?>');
		
		window.setTimeout(function () {
			$('#thepassword').html('<?php echo $placeholder;?>');
			}, 10000);
		});
	});
</script>

<h2><?php echo $name; ?></h2>

<?php if ($i_am_owner) { ?>
<p>
	<a href="edit.php?id=<?php echo $id; ?>"><button class="btn btn-lg btn-primary">Edit this object</button></a>
</p>
<?php } ?>

<table class="table table-hover">
	<tr>
		<td class="col-xs-3">ID:</td>
		<td class="col-xs-9"><?php echo $id;?></td>
	</tr>
	<tr>
		<td>Name:</td>
		<td><?php echo $name; ?></td>
	</tr>
	<tr>
		<td>Login:</td>
		<td><?php echo $login; ?></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td>
			<code id="thepassword"><?php echo $placeholder;?></code>
		</td>
	</tr>
	<tr>
		<td>Note:</td>
		<td><?php echo string2html($note); ?></td>
	</tr>
	<tr>
		<td>Owner:</td>
		<td><?php echo get_login($owner); ?></td>
	</tr>
	<tr>
		<td>Shared users:</td>
		<td>
			<?php
			$shared = get_shared_users($id);
			if ($shared == NULL) {
				echo "(nobody)";
				}
			else {
				echo "<div class=\"row\"><div class=\"col-sm-4\"><select class=\"form-control input-sm\" name=\"shared_uid\" multiple>\n";
				foreach ($shared as $row) {
					$shared_uid = $row['shared_uid'];
					$shared_username = $row['shared_login'];
					echo "<option value=\"$shared_uid\">$shared_username</option>\n"; 
					}
				echo "</select></div></div><br>\n";
			}	?>
		</td>
	</tr>
	<tr>
		<td>Shared groups:</td>
		<td>
			<?php
			$shared = get_shared_groups($id);
			if ($shared == NULL) {
				echo "(none)";
				}
			else {
				echo "<div class=\"row\"><div class=\"col-sm-4\"><select class=\"form-control input-sm\" name=\"shared_uid\" multiple>\n";
				foreach ($shared as $row) {
					$shared_gid = $row['shared_gid'];
					$shared_group = $row['shared_group'];
					echo "<option value=\"$shared_gid\">$shared_group</option>\n"; 
				}
				echo "</select></div></div><br>\n";
			} ?>
		</td>
	</tr>
</table>

<?php } ?>

<?php require_once('footer.php'); ?>