<?php
$requireadmin = true;
require_once('header.php');

$gid = $_GET['gid'];
$users = get_user_list();

?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading"><strong>Add Group Member(s):</strong> <?php echo get_group_name($gid);?></div>
			<div class="panel-body">
				<form action="do_add_group_member.php" method="POST">
					<input type="hidden" name="gid" value="<?php echo $gid; ?>">
					Select a user:					
					<select class="form-control input-lg" name="uid[]" multiple>
						<?php foreach ($users as $user) { ?><option value="<?php echo $user['uid'];?>"><?php echo $user['login'];?></option><?php } ?>
					</select>
					<p><small><em>Use CTRL+Click to select multiple users.</em></small></p>	
					<input type="submit" class="btn btn-sm btn-primary" value="Add">
				</form>
			</div>
		</div>
	</div>
</div>

<?php
require_once('footer.php');
?>
