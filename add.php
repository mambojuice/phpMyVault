<?php require_once('header.php'); ?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading">Add Password</div>
			<div class="panel-body">
				<form action="do_add.php" class="form-horizontal" method="POST">

					<div class="form-group">
						<label for="name" class="col-sm-4 control-label">Name:</label>
						<div class="col-sm-8"><input type="text" class="form-control" name="name" maxlength="256"></div>
					</div>
					<div class="form-group">
						<label for="login" class="col-sm-4 control-label">Login:</label>
						<div class="col-sm-8"><input type="text" class="form-control" name="login" maxlength="128"></div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-4 control-label">Password:</label>
						<div class="col-sm-8"><input type="password" class="form-control" name="password" maxlength="128"></div>
					</div>
					<div class="form-group">
						<label for="notes" class="col-sm-4 control-label">Notes:</label>
						<div class="col-sm-8"><textarea name="note" class="form-control" rows="4" cols="40" maxlength="1024"></textarea></div>
					</div>
					<div class="form-group">
						<div class="col-sm-4">&nbsp;</div>
						<div class="col-sm-8"><input type="submit" class="btn btn-sm btn-primary" value="Add"></div>
					</div>

				</form>

<?php require_once('footer.php'); ?>