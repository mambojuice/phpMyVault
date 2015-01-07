<?php
$requireadmin = true;
require_once('header.php');

$gid = $_POST['gid'];

$users = $_POST['group_members'];
?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading"><strong>Remove Group Member(s):</strong> <?php echo get_group_name($gid);?></div>
			<div class="panel-body">

				<form action="do_remove_group_member.php" method="POST">
					<input type="hidden" name="gid" value="<?php echo $gid;?>">
					
					<div>Please confirm you would like to remove the following users from this group:</div>
					<ul>
					<?php foreach ($users as $uid) { ?>
						<li><?php echo get_login($uid);?></li>
						<input type="hidden" name="user[]" value="<?php echo $uid;?>">
						<?php } ?>
					</ul>
					<input type="submit" class="btn btn-sm btn-primary" value="Confirm">
				</form>
			</div>
		</div>
	</div>
</div>

<?php
require_once('footer.php');
?>