<html lang="en">
	<head>
	</head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<title>phpMyVault</title>
		<link rel="stylesheet" type="text/css" href="../resources/bootstrap/css/bootstrap.css"/>
		
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		&nbsp;
		<div class="container">
			<div class="panel panel-default col-sm-6 col-sm-offset-3">
				<div class="panel-body">

<?php
include('../resources/functions.php');
include('../resources/config.php');

$user = $_POST['user'];
$password = $_POST['password'];

// Create the database...
echo "Creating database...";
$query = "CREATE DATABASE IF NOT EXISTS `$db_name`";
run_init_query($query, $user, $password);
echo " DONE<br>\n";

// Create MySQL login for database...
echo "Creating user...";
$query = "GRANT ALL ON `$db_name`.* TO '$db_user'@'localhost' identified by '$db_pass'";
run_init_query($query, $user, $password);
$query = "GRANT ALL ON `$db_name`.* TO '$db_user'@'%' identified by '$db_pass'";
run_init_query($query, $user, $password);
$query = "FLUSH PRIVILEGES";
run_init_query($query, $user, $password);
echo " DONE<br>\n";

// Create tables
echo "Creating tables...";
$query = "CREATE TABLE IF NOT EXISTS `data` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(256) NOT NULL, `login` varchar(128) NOT NULL, `password` varchar(1024) NOT NULL, `note` varchar(1024) NOT NULL, `owner` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1";
run_init_query($query, $db_user, $db_pass, $db_name);
$query = "CREATE TABLE IF NOT EXISTS `group_members` (`gid` int(11) NOT NULL,  `uid` int(11) NOT NULL,  PRIMARY KEY (`gid`,`uid`)) ENGINE=InnoDB DEFAULT CHARSET=latin1";
run_init_query($query, $db_user, $db_pass, $db_name);
$query = "CREATE TABLE IF NOT EXISTS `group_permissions` (`id` int(11) NOT NULL,  `gid` int(11) NOT NULL,  `mode` varchar(2) NOT NULL DEFAULT 'r',  PRIMARY KEY (`id`,`gid`)) ENGINE=InnoDB DEFAULT CHARSET=latin1";
run_init_query($query, $db_user, $db_pass, $db_name);
$query = "CREATE TABLE IF NOT EXISTS `groups` (`gid` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(128) DEFAULT NULL, `description` varchar(1024) DEFAULT NULL, PRIMARY KEY (`gid`),  UNIQUE KEY `gid_UNIQUE` (`gid`),  UNIQUE KEY `name_UNIQUE` (`name`)) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;";
run_init_query($query, $db_user, $db_pass, $db_name);
$query = "CREATE TABLE IF NOT EXISTS `sessions` (`session_id` varchar(64) NOT NULL,  `timestamp` datetime NOT NULL,  `uid` int(11) NOT NULL,  PRIMARY KEY (`session_id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
run_init_query($query, $db_user, $db_pass, $db_name);
$query = "CREATE TABLE IF NOT EXISTS `user_permissions` (`id` int(11) NOT NULL,  `uid` int(11) NOT NULL,  `mode` varchar(2) NOT NULL DEFAULT 'r',  PRIMARY KEY (`id`,`uid`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
run_init_query($query, $db_user, $db_pass, $db_name);
$query = "CREATE TABLE IF NOT EXISTS `users` (`uid` int(11) NOT NULL AUTO_INCREMENT,  `login` varchar(32) NOT NULL,  `password` varchar(128) DEFAULT NULL,  `type` varchar(5) NOT NULL,  `admin` int(11) NOT NULL DEFAULT '0',  PRIMARY KEY (`uid`),  UNIQUE KEY `login_UNIQUE` (`login`)) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;";
run_init_query($query, $db_user, $db_pass, $db_name);
echo " DONE<br>\n";

echo "Creating views...";
$query = "CREATE OR REPLACE VIEW `vPasswordSharedUsers` AS select `data`.`id` AS `id`,`data`.`name` AS `name`,`data`.`owner` AS `owner`,`user_permissions`.`uid` AS `shared_uid`,`users`.`login` AS `shared_login`,`user_permissions`.`mode` AS `shared_mode` from ((`user_permissions` join `data` on((`user_permissions`.`id` = `data`.`id`))) join `users` on((`user_permissions`.`uid` = `users`.`uid`)))";
run_init_query($query, $db_user, $db_pass, $db_name);
$query = "CREATE OR REPLACE VIEW `vPasswordSharedGroups` AS select `data`.`id` AS `id`,`data`.`name` AS `name`,`data`.`owner` AS `owner`,`group_permissions`.`gid` AS `shared_gid`,`groups`.`name` AS `shared_group` from ((`group_permissions` join `data` on((`group_permissions`.`id` = `data`.`id`))) join `groups` on((`group_permissions`.`gid` = `groups`.`gid`)))";
run_init_query($query, $db_user, $db_pass, $db_name);
$query = "CREATE OR REPLACE VIEW `vGroupMembers` AS select `group_members`.`gid` AS `gid`,`groups`.`name` AS `name`,`users`.`uid` AS `uid`,`users`.`login` AS `login` from ((`group_members` left join `users` on((`group_members`.`uid` = `users`.`uid`))) left join `groups` on((`group_members`.`gid` = `groups`.`gid`)))";
run_init_query($query, $db_user, $db_pass, $db_name);
echo " DONE<br>\n";
?>
					<p><div class="alert alert-info">If there are no errors listed above, you can delete install.php an begin using phpMyVault!</div></p>
				</div>
			</div>
		</div>
	</body>
</html>




<?php
// Main function for running SQL initialization commands
function run_init_query($query, $user, $password, $database = 'mysql') {
	// Include config.php for DB settings
	include("../resources/config.php");
	
	// Connect to MySQL
	$oMySQL = mysqli_connect($db_host, $user, $password, $database);
	if (mysqli_connect_errno()) {
		echo "DB CONNECTION ERROR: " . mysqli_connect_error();
		die();
		}
	
	// Run query
	mysqli_query($oMySQL, $query);
	
	// Query error handling
	if (mysqli_errno($oMySQL)) {
		echo "DB QUERY ERROR: " . mysqli_error($oMySQL);
		die();
		}
	
	// Close connection
	mysqli_close($oMySQL);
}

?>