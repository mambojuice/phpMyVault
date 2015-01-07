<!DOCTYPE html>
<html lang="en">
	<head>


<?php
include('resources/config.php');

if ($require_https == true) {
	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == '') {
		$redir = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header("Location: $redir");
	}
}
?>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<title>phpMyVault</title>
		<link rel="stylesheet" type="text/css" href="resources/bootstrap/css/bootstrap.css"/>
		<link rel="stylesheet" type="text/css" href="resources/bootstrap/css/bootstrap-theme.css"/>
		
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	
	<body>
		<div id="container">
			<!-- Some whitespace at the top -->
			<div>&nbsp;</div>
			
			<!-- Lefthand padding -->
			<div class="col-sm-2">&nbsp;</div>
			
			<!-- Installation page contents -->
			<div class="col-sm-8">
				<div class="panel panel-primary">
					<div class="panel-heading">Installation</div>
					<div class="panel-body">
						<div class="alert alert-info">If your database has already been configured, delete install.php to skip database initialization.</div>

						<div class="panel panel-default">
							<div class="panel-heading">The following MySQL settings are defined in config.php:</div>
							<div class="panel-body">
						
								<form class="form-horizontal">
									<div class="form-group">
										<label class="col-sm-5 control-label">MySQL User:</label>
										<div class="col-sm-7"><p class="form-control-static"><?php echo $db_user;?></p></div>
									</div>
									<div class="form-group">
										<label class="col-sm-5 control-label">MySQL Password:</label>
										<div class="col-sm-7"><p class="form-control-static">********</p></div>
									</div>
									<div class="form-group">
										<label class="col-sm-5 control-label">Database Name:</label>
										<div class="col-sm-7"><p class="form-control-static"><?php echo $db_name;?></p></div>
									</div>
									<div class="form-group">
										<label class="col-sm-5 control-label">Database Host:</label>
										<div class="col-sm-7"><p class="form-control-static"><?php echo $db_host;?></p></div>
									</div>
								</form>
							</div>
						</div>
						
						<form method="POST" action="setup/init.php" class="form-horizontal">
							<div class="panel panel-default">
								<div class="panel-heading">Enter MySQL administrative credentials to setup the above database and login:</div>
								<div class="panel-body">
									<div class="form-group">
										<label for="user" class="col-sm-5 control-label">User:</label>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="user" placeholder="Username">
										</div>
									</div>
									<div class="form-group">
										<label for="password" class="col-sm-5 control-label">Password:</label>
										<div class="col-sm-6">
											<input type="password" class="form-control" name="password" placeholder="Password">
										</div>
									</div>
								</div>
							</div>
							
							<button type="submit" class="btn btn-primary col-sm-12">Initialize!</button>

						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
