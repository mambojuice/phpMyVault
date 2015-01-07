<?php
$requireadmin = true;
require_once('header.php');
?>

<h2>Users:</h2>
<table class="table table-hover">
	<thead>
		<tr>
			<th>User</th>
			<th>Type</th>
			<th>Admin?</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	
<?php
$users = get_user_list();
foreach ($users as $user) {
?>

		<tr>
			<td><?php echo $user['login'];?></td>
			<td><?php echo $user['type'];?></td>
			<td><?php if ($user['admin']==1) { echo "Admin"; }?></td>
			<td><a href="edit_user.php?uid=<?php echo $user['uid'];?>" class="btn btn-xs btn-primary">Edit</a>
				<a href="delete_user.php?uid=<?php echo $user['uid'];?>" class="btn btn-xs btn-danger">Delete</a>
			</td>
		</tr>

<?php
	}
?>

	</tbody>
</table>
<div><a href="add_user.php" class="btn btn-sm btn-primary">Add New User</a></div>

<p>&nbsp;</p>

<h2>Groups:</h2>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Group Name</th>
			<th>Description</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>

<?php
$groups = get_group_list();
foreach ($groups as $group) {
?>
		<tr>
			<td><?php echo $group['name'];?></td>
			<td><?php echo $group['description'];?></td>
			<td><a href="edit_group.php?gid=<?php echo $group['gid']; ?>" class="btn btn-xs btn-primary">Edit</a>
				<a href="delete_group.php?gid=<?php echo $group['gid']; ?>" class="btn btn-xs btn-danger">Delete</a>
			</td>
		</tr>
<?php
	}
?>

	</tbody>
</table>
<div><a href="add_group.php" class="btn btn-sm btn-primary">Add New Group</a></div>

<?php
require_once('footer.php');
?>