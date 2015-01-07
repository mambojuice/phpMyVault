<?php require_once('header.php'); ?>

<?php
$gid = $_GET['gid'];

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

				<form action="do_delete_group.php" method="POST">
					<input type="hidden" name="gid" value="<?php echo $gid;?>">
					
					<div>Please confirm you would like to remove the following group:</div>
					<ul>
						<li><?php echo get_group_name($gid); ?>
					</ul>
					<p class="bg-warning"><small><em>This will also remove all group membership records and permissions.</em></small></p>
					<div><input type="submit" class="btn btn-sm btn-primary" value="Confirm"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php } ?>


<?php require_once('footer.php'); ?>