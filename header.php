<?php

// Redirect to install.php for initial configuration
$expl = explode('/', $_SERVER['SCRIPT_FILENAME']);
$expl[count($expl) - 1] = "install.php";
$install_php = implode('/', $expl);
if (file_exists($install_php)) {
	header('Location: install.php');
}
else {

	// Necessary includes
	include('resources/functions.php');
	include('resources/config.php');

	// Redirect to HTTPS if necessary
	if ($require_https == true) {
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == '') {
			$redir = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			header("Location: $redir");
		}
	}

	// Check login, redirect to login page if necessary
	check_session_login();

	// Do not render page if admin privileges are required
	if (isset($requireadmin)) {
		if ($requireadmin) {
			if (!am_i_admin()) {
				header('Location: show_permissions_error.php');
			}
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<title>phpMyVault</title>
		
		<!-- jQuery -->
		<script src="resources/jquery/jquery.min.js"></script>
		<script src="resources/jquery/jquery-ui.min.js"></script>
		<link rel="stylesheet" type="text/css" href="resources/jquery/jquery-ui.min.css"/>
		
		<!-- Bootstrap -->
		<link rel="stylesheet" type="text/css" href="resources/bootstrap/css/bootstrap.css"/>
		<link rel="stylesheet" type="text/css" href="resources/bootstrap/css/bootstrap-theme.css"/>
		<script src="resources/bootstrap/js/bootstrap.min.js"></script>
		

	</head>

	<body>
		
		<div class="bg-primary" style="background: url('images/stripe.png');">
			<div class="container">
				<div class="row">
					<div class="col-xs-6">
						<h1>phpMyVault</h1>
					</div>
					<div class="col-md-6">
						<?php if($doge) { ?>
						<div class="text-right">
							<br>Such security <img src="images/wow.gif">
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		
		<p/>
		
		<div class="container">	
			<div class="navbar navbar-default">
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav nav">
						<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Passwords <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="index.php">All</a></li>
								<li><a href="index.php?show=mine">Owned by me</a></li>
								<li><a href="index.php?show=shared">Shared with me</a></li>
							</ul>
						</li>
						<li><a href="add.php">Add</a></li>
					</ul>
					<ul class="navbar-nav nav navbar-right">
						<?php if (am_i_admin()) { ?>
						<li><a href="admin.php">Admin</a></li>
						<?php } ?>
						<li><a href="logout.php">Logout</a></li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="container">
			<p class="text-right">Logged in as: <?php echo get_login(get_my_uid()); ?></p>