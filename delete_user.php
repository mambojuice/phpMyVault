<?php require_once('header.php'); ?>

<?php
$uid = $_GET['uid'];

// Check permissions
if (!(am_i_admin())) {
	// We don't have permissions!	?>
	<p class="bg-danger center-block">ACCESS DENIED</p>
<?php	}
else {
?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading"><strong>Confirm Deletion</strong></div>
			<div class="panel-body">

				<form action="do_delete_user.php" method="POST">
					<input type="hidden" name="uid" value="<?php echo $uid;?>">
					
					<div>Please confirm you would like to remove the following user:</div>
					<ul>
						<li><?php echo get_login($uid); ?>
					</ul>
					<p class="bg-warning"><small><em>This will NOT remove password entires owned by this user.</em></small></p>
					<div><input type="submit" class="btn btn-sm btn-primary" value="Confirm"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php } ?>


<?php require_once('footer.php'); ?>