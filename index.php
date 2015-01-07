<?php require_once('header.php');

if (isset($_GET['show'])) {
	if ($_GET['show'] == 'mine') {
		$mode = "mine";
		$title = "My Passwords";
	}
	elseif ($_GET['show'] == 'shared') {
		$mode = "shared";
		$title = "Passwords Shared With Me";
	}
}
else {
	$mode = "all";
	$title = "All Passwords Available To Me";
}
?>

				<h2><?php echo $title;?></h2>
				
<?php if (($mode == "mine") or ($mode == "all")) { ?>
			<!-- All passwords owned by me -->
			<h3>Passwords owned by me</h3>
				<table class="table table-hover">
					<thead>
						<tr>
							<th class="col-xs-9">Name</th>
							<th class="col-xs-3">Actions</th>
						</tr>
					</thead>
					<tbody>

<?php
$results = get_my_passwords();
foreach ($results as $entry) {
	$id =  $entry['id'];
	$name = $entry['name'];
	?>
						<tr>
							<td><?php echo $name; ?></td>
							<td>
								<a class="btn btn-primary btn-xs" href="show.php?id=<?php echo $id;?>">Show</a>
								<a class="btn btn-primary btn-xs" href="edit.php?id=<?php echo $id;?>">Edit</a>
								<a class="btn btn-danger btn-xs" href="delete.php?id=<?php echo $id;?>">Delete</a>
							</td>
						</tr>
<?php
	}
?>
					</tbody>
				</table>
			<!-- END all passwords owned by me -->
			<?php } ?>


				<br>


<?php if (($mode == "shared") or ($mode == "all")) { ?>
			<!-- All passwords shared with me -->
			<h3>Passwords shared with me</h3>
				<table class="table table-hover">
					<thead>
						<tr>
							<th class="col-xs-9">Name</th>
							<th class="col-xs-3">Actions</th>
						</tr>
					</thead>
					<tbody>

<?php
$results = get_passwords_shared_with_me();
if ($results != null) {

	foreach ($results as $entry) {
		$id =  $entry['id'];
		$name = $entry['name'];
		?>
							<tr>
								<td><?php echo $name; ?></td>
								<td>
									<a class="btn btn-primary btn-xs" href="show.php?id=<?php echo $id;?>">Show</a>
								</td>
							</tr>
	<?php
	}
}
	?>
						</tbody>
					</table>
				<!-- END all passwords shared with me -->
				<?php } ?>

				<br>

<?php if (($mode == "shared") or ($mode == "all")) { ?>
			<!-- All passwords shared with my groups -->
			<h3>Passwords shared with my groups</h3>
				<table class="table table-hover">
					<thead>
						<tr>
							<th class="col-xs-9">Name</th>
							<th class="col-xs-3">Actions</th>
						</tr>
					</thead>
					<tbody>

<?php
$results = get_passwords_shared_with_my_groups();
foreach ($results as $entry) {
	$id =  $entry['id'];
	$name = $entry['name'];
	?>
						<tr>
							<td><?php echo $name; ?></td>
							<td>
								<a class="btn btn-primary btn-xs" href="show.php?id=<?php echo $id;?>">Show</a>
							</td>
						</tr>
<?php
	}
?>
					</tbody>
				</table>
			<!-- END all passwords shared with my groups -->
		<?php } ?>

			</div>

<?php require_once('footer.php'); ?>