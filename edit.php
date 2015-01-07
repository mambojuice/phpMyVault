<?php require_once('header.php'); ?>

<?php
$id = $_GET['id'];

// Check permissions
if (!(get_owner($id) == get_my_uid())) {
	// We don't have permissions!	?>
	<p class="bg-danger center-block">ACCESS DENIED</p>
<?php	}
else {
	// Permissions are good
	$pass_entry = get_password_object($id);

	$name = $pass_entry['name'];
	$login = decrypt_string($pass_entry['login']);
	$password = decrypt_string($pass_entry['password']);
	$note = $pass_entry['note'];
	$owner = $pass_entry['owner'];
	$i_am_owner = ($owner == get_my_uid());
	$shared_users = get_shared_users($id);
	$shared_groups = get_shared_groups($id);
?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading"><strong>Edit object:</strong> <?php echo $name;?></div>
			<div class="panel-body">

				<form action="do_edit.php" class="form-horizontal" method="POST">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<div class="form-group">
						<label class="col-sm-3 control-label">ID:</label>
						<div class="col-sm-9"><p class="form-control-static"><?php echo $id;?></p></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Owner:</label>
						<div class="col-sm-9"><p class="form-control-static"><?php echo get_login($owner); ?></p></div>
					</div>
					<div class="form-group">
						<label for="name" class="col-sm-3 control-label">Name:</label>
						<div class="col-sm-9"><input type="text" class="form-control" name="name" maxlength="256" value="<?php echo $name;?>"></div>
					</div>
					<div class="form-group">
						<label for="login" class="col-sm-3 control-label">Login:</label>
						<div class="col-sm-9"><input type="text" class="form-control" name="login" maxlength="256" value="<?php echo $login; ?>"></div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-3 control-label">Password:</label>
						<div class="col-sm-9"><input type="password" class="form-control" name="password" maxlength="256" value="<?php echo $password; ?>"></div>
					</div>
					<div class="form-group">
						<label for="note" class="col-sm-3 control-label">Note:</label>
						<div class="col-sm-9"><textarea class="form-control" name="note" rows="4"><?php echo $note; ?></textarea></div>
					</div>
					<div class="form-group">
						<div class="col-sm-3"></div>
						<div class="col-sm-9"><input type="submit" class="btn btn-sm btn-primary" value="Save Changes"></div>
					</div>
					<div class="col-sm-12">&nbsp;</div>
				</form>
				<form action="share_with_user.php" class="form-horizontal" method="POST">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<div class="form-group">
						<label for="shared_users" class="col-sm-3 control-label">Shared Users:</label>
						<div class="col-sm-9">
							<select class="form-control input-sm" name="shared_users[]" multiple>
							<?php foreach ($shared_users as $user) {
								$shared_uid = $user['shared_uid'];
								$shared_username = $user['shared_login']; ?>
								<option value="<?php echo $shared_uid;?>"><?php echo $shared_username;?></option>
								<?php } //End ForEach ?>
							</select>
							<button class="btn btn-xs btn-primary" name="action" value="add">Add User(s)</button>
							<button class="btn btn-xs btn-danger" name="action" value="remove">Remove Selected User(s)</button>
						</div>
					</div>
				</form>
				<form action="share_with_group.php" class="form-horizontal" method="POST">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<div class="form-group">
						<label for="shared_groups" class="col-sm-3 control-label">Shared Groups:</label>
						<div class="col-sm-9">
							<select class="form-control input-sm" name="shared_groups[]" multiple>
							<?php foreach ($shared_groups as $group) {
								$shared_gid = $group['shared_gid'];
								$shared_group = $group['shared_group']; ?>
								<option value="<?php echo $shared_gid;?>"><?php echo $shared_group;?></option>
								<?php } //End ForEach ?>
							</select>
							<button class="btn btn-xs btn-primary" name="action" value="add">Add Group(s)</button>
							<button class="btn btn-xs btn-danger" name="action" value="remove">Remove Selected Group(s)</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php } //End ownership check ?>

<?php require_once('footer.php'); ?>