<?php
include('resources/config.php');

// HTTPS Redirect if necessary
if ($require_https == true) {
	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == '') {
		$redir = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header("Location: $redir");
	}
}

// Process login if we've been given POST data
if ($_GET['action'] == 'do_login') {
	include("resources/functions.php");
	$login = $_POST["user"];
	$password = $_POST["password"];
	if (check_login($login, $password)) {
		successful_login($login);
		header("Location: index.php");
	}
	else {
		header("Location: login.php?login=unsuccessful&user=$login");
	}
}
?>

<!DOCTYPE HTML>
<html>
    <head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	<link rel="stylesheet" href="phpmyvault.css">
	
	<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	
	<title>Please Login</title>
	
    </head>
    <body>
		<br>
		<div class="container">
			<nav class="navbar navbar-inverse">
				<a class="navbar-brand" href="#">PhpMyVault</a>
			</nav>
			
			<div class="panel panel-primary">
				<div class="panel-heading">Please Login</div>
				<div class="panel-body">
					<?php if ($_GET['login'] == "unsuccessful") {
						$user = $_GET['user']; ?>
						<div class="alert alert-danger">Login for user '<?php echo $user; ?>' was unsuccessful. Username or password incorrect.</div>
					<?php } ?>
					<form method="POST" action="login.php?action=do_login" class="form-horizontal">
						<div class="form-group">
							<label for="user" class="col-sm-4 control-label">User:</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" name="user" placeholder="Username">
							</div>
						</div>
						<div class="form-group">
							<label for="password" class="col-sm-4 control-label">Password:</label>
							<div class="col-sm-4">
								<input type="password" class="form-control" name="password" placeholder="Password">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-4 col-sm-offset-4">
								<button type="submit" class="btn btn-primary">Login</input>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>