<form action="do_remove_user.php" method="POST">
	<input type="hidden" name="id" value="<?php echo $id;?>">

	<div class="row">
		<!-- lefthand padding -->
		<div class="col-sm-3">&nbsp;</div>
		<div class="col-sm-6">
			<div class="panel panel-primary">
				<div class="panel-heading"><strong>Confirm Removal</strong></div>
				<div class="panel-body">
					<p>
						Please confirm you would like to remove the following users:
						<ul>
							<?php foreach ($_POST['shared_users'] as $uid) { ?>
							<li><?php echo get_login($uid);?></li>
							<input type="hidden" name="user[]" value="<?php echo $uid;?>">
							<?php } ?>
						</ul>
					</p>
					<p>Users will no longer be able to view the following object:
						<ul>
							<li><?php echo get_object_name($id); ?>
						</ul>
					</p>
					<p><input type="submit" class="btn btn-sm btn-primary" value="Confirm"></p>
				</div>
			</div>
		</div>
	</div>
</form>