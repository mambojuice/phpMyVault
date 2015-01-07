<?php
$users = get_user_list();
?>

<form method="POST" action="do_share_with_user.php">
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	
	<div class="row">
		<!-- lefthand padding -->
		<div class="col-sm-3">&nbsp;</div>
		<div class="col-sm-6">
			<div class="panel panel-primary">
				<div class="panel-heading"><strong>Share with user:</strong> <?php echo get_object_name($id); ?></div>
				<div class="panel-body">
					Select a user:					
						<select class="form-control input-lg" name="uid[]" multiple>
							<?php foreach($users as $user) {
								//Hide yo'self
								if (!($user['uid'] == get_my_uid())) { ?>
									<option value="<?php echo $user['uid'];?>"><?php echo $user['login'];?></option>
								<?php }
							} ?>
						</select>
						<p><small><em>Use CTRL+Click to select multiple users.</em></small></p>
					<input type="submit" class="btn btn-sm btn-primary" value="Share">
				</div>
			</div>
		</div>
	</div>
	
</form>