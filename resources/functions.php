<?php



// *****************************************
// FUNCTION: ldap_enabled()
// *****************************************
// Is LDAP enabled in config.php?
function ldap_enabled() {
	include('resources/config.php');
	if ($ldap == true) {
		return true;
		}
	else {
		return false;
		}
	}
//End Function



// *****************************************
// FUNCTION: check_login($user, $password)
// *****************************************
// Check for valid credentials
function check_login($login, $password) {
	include("resources/config.php");

	// Are we using the superuser account?
	if ($login == $superuser) {
		// We are superuser. Check password from config file
		if ($password == $superuser_password) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		// Not superuser, check database for login

		// Get login type
		$type = get_sql_value("SELECT type FROM users WHERE login = '$login'");

		// If type is null, user doesn't exist in DB
		if ($type == null) {
			return false;
			}

		// LDAP Login
		if ($type == "ldap") {
			return check_ldap_credentials($login, $password);
			}
		// Local login
		if ($type == "local") {
			$password_md5 = md5($password);
			if ($password_md5 == get_sql_value("SELECT password FROM users WHERE login = '$login'")) {
				return true;
			}
			else {
				return false;
			}
		}
	}
}
//End Function



// *****************************************
// FUNCTION: successful_login($user)
// *****************************************
// Setup session after successfully logging in
function successful_login($user) {
	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}

	$uid = get_uid($user);

	// Add session to sessions table
	run_sql_command("INSERT INTO sessions(sid,timestamp,uid) VALUES('". session_id() . "',now(), $uid)");
}
//End function



// *****************************************
// FUNCTION: check_session_login()
// *****************************************
// Check if our session is valid
function check_session_login() {
	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}

	// Kill any expired sessions
	cleanup_old_sessions();

	// Query sessions table to get session ID timestamp
	$SessionTimestamp = get_sql_value("SELECT timestamp FROM sessions WHERE sid='" . session_id() . "'");

	// Redirect to login page if timestamp is null
	if ($SessionTimestamp == NULL) {
		header('Location: login.php');
	}
	else {
		update_session_timestamp();
	}
}
//END FUNCTION



// *****************************************
// FUNCTION: kill_my_session()
// *****************************************
// Remove any entries with our session ID from the sessions table
function kill_my_session() {
	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}

	run_sql_command("DELETE FROM sessions WHERE sid='" . session_id() . "'");
	session_destroy();

}
// END FUNCTION



// *****************************************
// FUNCTION: update_session_timestamp()
// *****************************************
// Update session timestamp for login keepalive
function update_session_timestamp() {
	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}

	run_sql_command("UPDATE sessions SET timestamp = now() WHERE sid = '" . session_id() . "'");
}
//End function



// *****************************************
// FUNCTION: cleanup_old_sessions()
// *****************************************
// Cleanup old sessions that have expired
function cleanup_old_sessions() {
	include("resources/config.php");

	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}

	// Query for all sessions
	$all_sessions = get_sql_results('select * from sessions');

	// Check each session timestamp
	foreach ($all_sessions as $session) {
		$diff = time() - strtotime($session['timestamp']);

		if ($diff > $session_timeout) {
			// Delete if time diff is beyond threshold
			run_sql_command("DELETE FROM sessions WHERE sid = '" . $session['session_id'] . "'");
		}
	}
}
//End function



// *****************************************
// FUNCTION: add_group($group_name, $description)
// *****************************************
function add_group($group_name, $description) {
	include("resources/config.php");

	$encoded_description = string2html($description);

	run_sql_command("INSERT INTO groups (name, description) VALUES ('$group_name', '$encoded_description')");
}



// *****************************************
// FUNCTION: edit_group($gid, $group_name, $description)
// *****************************************
// Update the name or description of group GID
function edit_group($gid, $group_name, $description) {
	run_sql_command("UPDATE groups SET name='$group_name' WHERE gid=$gid");
	run_sql_command("UPDATE groups SET description='$description' WHERE gid=$gid");
	}



// *****************************************
// FUNCTION: add_ldap_user($login)
// *****************************************
function add_ldap_user($login) {
	include("resources/config.php");

	run_sql_command("INSERT INTO users (login, type) VALUES ('$login', 'ldap')");
	$uid = get_uid($login);
	run_sql_command("INSERT INTO folders (name, isPersonal, uid) VALUES ('My Passwords', '1', '$uid')");
}



// *****************************************
// FUNCTION: add_local_user($login, $password)
// *****************************************
function add_local_user($login, $password) {
	include("resources/config.php");
	
	if (user_exists($login)) {
		die("ERROR! User already exists!");
	}
	else {
		$pass_enc = md5($password);
		run_sql_command("INSERT INTO users (login, password, type) VALUES ('$login', '$pass_enc', 'local')");
		$uid = get_uid($login);
		run_sql_command("INSERT INTO folders (name, isPersonal, uid) VALUES ('My Passwords', '1', '$uid')");
	}
}


// *****************************************
// FUNCTION: user_exists($login)
// *****************************************
// Returns 'true' of a login is already in the user database
function user_exists($login) {
	$val = get_sql_results("SELECT * FROM users WHERE login='$login'");
	
	if ($val == null) {
		return false;
	}

	else {
		return true;
	}
}


// *****************************************
// FUNCTION: update_user_password($uid, $password)
// *****************************************
function update_user_password($uid, $password) {
	include("resources/config.php");

	$pass_enc = md5($password);
	echo "DEBUG: uid $uid / Endrypted password $pass_enc";
	run_sql_command("UPDATE users SET password='$pass_enc' WHERE uid=$uid");
}



// *****************************************
// FUNCTION: grant_user_admin_rights($uid)
// *****************************************
function grant_user_admin_rights($uid) {
	include("resources/config.php");
	run_sql_command("UPDATE users SET admin=1 WHERE uid='$uid'");
}



// *****************************************
// FUNCTION: remove_user_admin_rights($uid)
// *****************************************
function remove_user_admin_rights($uid) {
	include("resources/config.php");
	// Can't remove admin rights for superuser
	if ($uid != -1) {
		run_sql_command("UPDATE users SET admin=0 WHERE uid='$uid'");
	}
}



// *****************************************
// FUNCTION: get_uid($login)
// *****************************************
// Returns -1 if user is the superuser defined in config.php
// Otherwise returns UID in users table
function get_uid($login) {
	include("resources/config.php");

	// If superuser, UID is -1
	if ($login == $superuser) {
		$uid = -1;
	}
	else {
		$uid = get_sql_value("SELECT uid FROM users WHERE login='$login'");
	}

	return $uid;
}



// *****************************************
// FUNCTION: get_login($uid)
// *****************************************
// Returns the login of UID
function get_login($uid) {
	include("resources/config.php");

	// If UID is -1, login is superuser
	if ($uid == -1) {
		$login = $superuser;
	}
	else {
		$login = get_sql_value("SELECT login FROM users WHERE uid=$uid");
	}

	return $login;
}



// *****************************************
// FUNCTION: get_user_type($uid)
// *****************************************
// Returns the user type of UID
function get_user_type($uid) {
	include("resources/config.php");

	// If UID is -1, login is superuser
	if ($uid == -1) {
		return "superuser";
	}
	else {
		return get_sql_value("SELECT type FROM users WHERE uid=$uid");
	}
}



// *****************************************
// FUNCTION: get_user_permission($uid)
// *****************************************
// Returns 1 if the user is an admin, 0 if not
function get_user_permission($uid) {
	include("resources/config.php");

	// If UID is -1, login is superuser
	if ($uid == -1) {
		return 1;
	}
	else {
		return get_sql_value("SELECT admin FROM users WHERE uid=$uid");
	}
}



// *****************************************
// FUNCTION: get_all_users()
// *****************************************
// Returns array of all users
function get_all_users() {
	return get_sql_results("SELECT * FROM users");
}



// *****************************************
// FUNCTION: get_all_groups()
// *****************************************
// Returns array of all groups
function get_all_groups() {
	return get_sql_results("SELECT * FROM groups");
}



// *****************************************
// FUNCTION: get_my_uid()
// *****************************************
// Returns the UID of the active logged in session
function get_my_uid() {
	include("resources/config.php");

	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}
	return get_sql_value("SELECT uid FROM sessions WHERE sid='" . session_id() . "'");
}



// *****************************************
// FUNCTION: am_i_admin();
// *****************************************
// Do I have admin rights?
function am_i_admin() {
	$uid = get_my_uid();

	// Are we superuser?
	if ($uid == -1) {
		return true;
		}

	// Check users table
	$admin = get_sql_value("SELECT admin FROM users WHERE uid=$uid");

	if ($admin == 1) {
		return true;
		}
	else {
		return false;
		}
	}
//End function



// *****************************************
// FUNCTION: check_ldap_credentials($user, $password)
// *****************************************
// Check credentials against LDAP server
function check_ldap_credentials($user, $password) {
	//Include config.php for LDAP server info
	include('resources/config.php');

	//Make sure a password was provided
	if ($password == '') {
		return false;
		}

	//Connect to LDAP server
	define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);
	$ldapconn = ldap_connect($ldap_server, $ldap_port) or die("Could not connect to LDAP server!");

	//Set LDAP options
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

	if ($ldapconn) {
		//Verify credentials
		echo("Connected to LDAP server. Verifying credentials for '$user'...");
		$ldapbind = ldap_bind($ldapconn, $ldap_domain . "\\" . $user, $password);

		if($ldapbind) {
			return true;
		}
		else {
			return false;
		}
	}

	return false;
}
//End function



// *****************************************
// FUNCTION: ldap_user_search($username)
// *****************************************
// Searches for LDAP user
function ldap_user_search($username) {
	include('resources/config.php');

	$oLDAP = ldap_connect($ldap_server,389);
	ldap_set_option($oLDAP, LDAP_OPT_REFERRALS, 0);
	ldap_set_option($oLDAP, LDAP_OPT_PROTOCOL_VERSION, 3);
	$oDIR = ldap_bind($oLDAP, $ldap_user, $ldap_password);
	$sQuery = '(samaccountname=' . $username . '*)';
	$oSearch = ldap_search($oLDAP, $ldap_base_dn, $sQuery, array('samaccountname'));
	return ldap_get_entries($oLDAP, $oSearch);
}



// *****************************************
// FUNCTION: get_group_list()
// *****************************************
// Returns array of all groups
function get_group_list() {
	return get_sql_results("SELECT * FROM groups ORDER BY name");
	}



// *****************************************
// FUNCTION: get_user_list()
// *****************************************
// Returns array of all groups
function get_user_list() {
	return get_sql_results("SELECT * FROM users ORDER BY login");
	}



// *****************************************
// FUNCTION: get_user_name($uid)
// *****************************************
// Returns name of user for given UID
function get_user_name($uid) {
	if ($uid == -1) {
		// Superuser!
		include('resources/config.php');
		return $superuser;
		}
	else {
		return get_sql_value("SELECT name FROM users WHERE uid=$uid");
		}
	}



// *****************************************
// FUNCTION: get_shared_users($fid)
// *****************************************
// Returns array of user info for all users shared with folder having provided FID
// Returns NULL if object has not been shared with any users
function get_shared_users($fid) {
	return get_sql_results("SELECT * FROM vFoldersSharedWithUsers WHERE fid=$fid");
	}



// *****************************************
// FUNCTION: get_shared_groups($fid)
// *****************************************
// Returns array of group info for all groups shared with folder having provided FID
// Returns NULL if object has not been shared with any groups
function get_shared_groups($fid) {
	return get_sql_results("SELECT * FROM vFoldersSharedWithGroups WHERE fid=$fid");
	}

// *****************************************
// FUNCTION: add_user_to_folder($uid, $fid)
// *****************************************
// Adds user with given UID to folder with given FID
function add_user_to_folder($uid, $fid) {
	if (get_sql_value("SELECT COUNT(id) FROM permissions WHERE uid=$uid AND fid=$fid") == 0) {
		run_sql_command("INSERT INTO permissions (uid, fid) VALUES ('$uid', '$fid')");
	}
	else {
		die("User already has permissions!");
	}

	return true;
	}


// *****************************************
// FUNCTION: add_group_to_folder($gid, $fid)
// *****************************************
// Adds group with given GID to folder with given FID
function add_group_to_folder($gid, $fid) {
	if (get_sql_value("SELECT COUNT(id) FROM permissions WHERE gid=$gid AND fid=$fid") == 0) {
		run_sql_command("INSERT INTO permissions (gid, fid) VALUES ('$gid', '$fid')");
	}
	else {
		die("Group already has permissions!");
	}

	return true;
}



// *****************************************
// FUNCTION: get_group_name($gid)
// *****************************************
// Returns name of group with provided gid
function get_group_name($gid) {
	return get_sql_value("SELECT name FROM groups WHERE gid=$gid");
	}



// *****************************************
// FUNCTION: get_group_description($gid)
// *****************************************
// Returns description of group with provided GID
function get_group_description($gid) {
	return get_sql_value("SELECT description FROM groups WHERE gid=$gid");
	}



// *****************************************
// FUNCTION: get_group_members($gid)
// *****************************************
// Returns array of members of group with provided GID
function get_group_members($gid) {
	return get_sql_results("SELECT uid, login FROM vGroupMembers WHERE gid=$gid");
	}
// *****************************************
// FUNCTION: get_group_membership($uid)
// *****************************************
// Returns array of groups that the provided UID belongs to
function get_group_membership($uid) {
	return get_sql_results("SELECT gid, name FROM vGroupMembers WHERE uid=$uid");
	}



// User account functions

// *****************************************
// FUNCTION: delete_user($uid)
// *****************************************
// Delete user UID and remove shared permissions
function delete_user($uid) {
	// Delete shared user permissions
	run_sql_command("DELETE FROM user_permissions WHERE uid=$uid");

	// Delete group membership
	run_sql_command("DELETE FROM group_members WHERE uid=$uid");

	// Delete the user entry
	run_sql_command("DELETE FROM users WHERE uid=$uid");

	// Delete personal folder and passwords
	delete_personal_folder($uid);
	
	// Re-assign shared passwords
	// !!!!STILL MORE TO DO HERE!!!!
	}


	
// *****************************************
// FUNCTION: delete_personal_folder($uid)
// *****************************************
// Delete personal folder for given UID
function delete_personal_folder($uid) {
	$folder = get_personal_folder($uid);
	$fid = $folder['fid'];

	// Delete folder data
	run_sql_command("DELETE FROM data WHERE folder_fid = '$fid'");
	
	// Remove folder
	run_sql_command("DELETE FROM folders WHERE fid = '$fid'");
}

// *****************************************
// FUNCTION: delete_group($gid)
// *****************************************
// Delete user UID and remove shared permissions
function delete_group($gid) {
	// Delete shared group permissions
	run_sql_command("DELETE FROM group_permissions WHERE gid=$gid");

	// Delete group membership
	run_sql_command("DELETE FROM group_members WHERE gid=$gid");

	// Delete the group entry
	run_sql_command("DELETE FROM groups WHERE gid=$gid");
	}



// *****************************************
// FUNCTION: remove_user_from_group($gid, $uid)
// *****************************************
// Remove user UID from group GID
function remove_user_from_group($gid, $uid) {
	run_sql_command("DELETE FROM group_members WHERE gid=$gid AND uid=$uid");
	}



// *****************************************
// FUNCTION: add_user_to_group($gid, $uid)
// *****************************************
// Add user UID to group GID
function add_user_to_group($gid, $uid) {
	// Check that user isn't already a member
	if (get_sql_value("SELECT uid FROM group_members WHERE gid=$gid AND uid=$uid") == NULL) {
		run_sql_command("INSERT INTO group_members (gid, uid) VALUES ('$gid', '$uid')");
		}
	}



// DATABASE FUNCTIONS *********************

// *****************************************
// FUNCTION: get_sql_value($query)
// *****************************************
// Return a single value from a single row
function get_sql_value($query) {
	// Include config.php for DB settings
	include("resources/config.php");

	// Connect to MySQL
	$oMySQL = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
	if (mysqli_connect_errno()) {
		echo("DB ERROR: " . mysqli_connect_error());
		die();
	}

	// Run query
	$result = mysqli_query($oMySQL, $query);

	// Return NULL if no rows returned
	if (mysqli_num_rows($result) == 0) {
		$sResult = NULL;
	}
	else {
		$row = mysqli_fetch_row($result);
		$sResult = $row[0];
	}

	// Close connection
	mysqli_close($oMySQL);

	return $sResult;
}



// *****************************************
// FUNCTION: get_sql_results($query)
// *****************************************
// Get array of results from a SQL query
function get_sql_results($query) {
	// Include config.php for DB settings
	include("resources/config.php");

	// Connect to MySQL
	$oMySQL = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
	if (mysqli_connect_errno()) {
		echo("DB ERROR: " . mysqli_connect_error());
		die();
	}

	// Run query
	$result = mysqli_query($oMySQL, $query);

	// Were results returned?
	if (mysqli_affected_rows($oMySQL) > 0) {
		// At least one row was returned
		// Build array of all rows
		while ($row = mysqli_fetch_array($result)) {
			$ret[] = $row;
		}
	}

	// No results, return null
	else {
		$ret = null;
		}

	// Close connection
	mysqli_close($oMySQL);

	return $ret;
}



// *****************************************
// FUNCTION: run_sql_command($query)
// *****************************************
// Run a raw SQL command
// Does not check output or return value
function run_sql_command($query) {
	// Include config.php for DB settings
	include("resources/config.php");

	// Connect to MySQL
	$oMySQL = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
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



// *****************************************
// FUNCTION: sqlescape($string)
// *****************************************
// Convert string into SQL string
function sqlescape($string) {
	// Include config.php for DB settings
	include("resources/config.php");

	// Connect to MySQL
	$oMySQL = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
	if (mysqli_connect_errno()) {
		echo "DB CONNECTION ERROR: " . mysqli_connect_error();
		die();
	}

	$escaped_string = mysqli_real_escape_string($oMySQL, $string);

	mysqli_close($oMySQL);

	return $escaped_string;
}



// STRING FUNCTIONS **********************

// *****************************************
// FUNCTION: string2html($string)
// *****************************************
// Make a string display nicely in HTML
function string2html($string) {
	// Line break
	$string = str_replace("\n","<br>",$string);

	return $string;
}



// *****************************************
// FUNCTION: html2string($string)
// *****************************************
// Make HTML display as a string in forms
function html2string($string) {
	// Line break
	$string = str_replace("<br>","\n",$string);

	return $string;
}



// ENCRYPTION FUNCTIONS **********************

// *****************************************
// FUNCTION: encrypt_string($string)
// *****************************************
// Encrypt a string using the secret key
function encrypt_string($string) {
	// Include config.php for secret key
	include("resources/config.php");

	// http://blog.justin.kelly.org.au/simple-mcrypt-encrypt-decrypt-functions-for-p/
	return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secret_key, $string, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
}



// *****************************************
// FUNCTION: decrypt_string($string)
// *****************************************
// Decrypt a string using the secret key
function decrypt_string($string) {
	// Include config.php for secret key
	include("resources/config.php");

	// http://blog.justin.kelly.org.au/simple-mcrypt-encrypt-decrypt-functions-for-p/
	return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secret_key, base64_decode($string), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
}



// *****************************************
// FUNCTION: get_decrypted_password($id, $uid)
// *****************************************
// Returns a decrypted password only if the user ID can access it
function get_decrypted_password($oid, $uid) {
	// Check if I have permissions
	if (check_object_permissions($oid, $uid)) {
		$encrypted_pass = get_sql_value("SELECT password FROM data WHERE oid=$oid");
		return decrypt_string($encrypted_pass);
	}
	else {
		return "ACCESS DENIED";
	}
}



// OBJECT FUNCTIONS **********************

// *****************************************
// FUNCTION: get_password($oid)
// *****************************************
// Returns array of data for the provided OID
function get_password($oid) {
	$results = get_sql_results("SELECT * FROM data WHERE oid=$oid");
	return $results[0];
	}


// *****************************************
// FUNCTION: update_password_name($oid, $newname)
// *****************************************
// Changes the name of a password with a given oid
function update_password_name($oid, $newname) {
	run_sql_command("UPDATE data SET name='$newname' WHERE oid=$oid");
}



// *****************************************
// FUNCTION: update_password_login($oid, $newlogin)
// *****************************************
// Changes the login of a password with a given oid
function update_password_login($oid, $newlogin) {
	run_sql_command("UPDATE data SET login='$newlogin' WHERE oid=$oid");
}



// *****************************************
// FUNCTION: update_password_fid($oid, $newfid)
// *****************************************
// Changes the fid of a password with a given oid
function update_password_fid($oid, $newfid) {
	run_sql_command("UPDATE data SET folder_fid='$newfid' WHERE oid=$oid");
}



// *****************************************
// FUNCTION: update_password_password($oid, $newpassword)
// *****************************************
// Changes the password value of a password with a given oid
// Password value passed to function should already be encrypted
function update_password_password($oid, $newpassword) {
	run_sql_command("UPDATE data SET password='$newpassword' WHERE oid=$oid");
}



// *****************************************
// FUNCTION: update_password_note($oid, $newnote)
// *****************************************
// Changes the note value of a password with a given oid
// Note value passed to function should already be converted to HTML
function update_password_note($oid, $newnote) {
	run_sql_command("UPDATE data SET note='$newnote' WHERE oid=$oid");
}



// *****************************************
// FUNCTION: add_password($name, $login, $password, $note, $fid)
// *****************************************
// Adds new password to the database, returns PID of new object
// Password value passed to function should already be encrypted
// Note value passed to function should already be converted to HTML
function add_password($name, $login, $password, $note, $fid) {
	$my_uid = get_my_uid();
	run_sql_command("INSERT INTO data (name, login, password, note, owner_uid, folder_fid) VALUES ('$name', '$login', '$password', '$note', '$my_uid', '$fid')");
	return get_sql_value("SELECT LAST_INSERT_ID()");
	}



// *****************************************
// FUNCTION: delete_password($id)
// *****************************************
// Delete password ID and all defined permissions associated
function delete_password($id) {
	// Delete shared user permissions
	run_sql_command("DELETE FROM user_permissions WHERE id=$id");

	// Delete shared group permissions
	run_sql_command("DELETE FROM group_permissions WHERE id=$id");

	// Delete the password entry
	run_sql_command("DELETE FROM data WHERE id=$id");
	}



// *****************************************
// FUNCTION: get_password_name($oid)
// *****************************************
// Returns the name of given OID
function get_password_name($oid) {
	return get_sql_value("SELECT name FROM data WHERE oid=$oid");
	}



// *****************************************
// FUNCTION: check_object_permissions($oid, $uid)
// *****************************************
// Tests if a given UID has access to an object OID
function check_object_permissions($oid, $uid) {
	// Check if UID is owner
	$owner = get_sql_value("SELECT owner_uid FROM data WHERE oid=$oid");
	if (get_my_uid() == $owner) {
		return true;
	}

	// Check if OID is in a folder shared with the UID
	// TO DO

	// Deny otherwise
	return false;
}



// *****************************************
// FUNCTION: get_owner($oid)
// *****************************************
// Returns UID of owner for the provided OID
function get_owner($oid) {
	return get_sql_value("SELECT owner_uid FROM data WHERE oid=$oid");
	}



// *****************************************
// FUNCTION: get_folder_list()
// *****************************************
// Returns all folders that are NOT personal folders
function get_folder_list() {
	return get_sql_results("SELECT * FROM folders where isPersonal = false ORDER BY name");
}

// *****************************************
// FUNCTION: get_passwords_in_folder($fid)
// *****************************************
// Returns all passwords stored in a given FID
function get_passwords_in_folder($fid) {
	return get_sql_results("SELECT * FROM data where folder_fid = $fid");
}



// *****************************************
// FUNCTION: get_personal_folder($uid)
// *****************************************
// Returns personal folder for given user ID
function get_personal_folder($uid) {
	$res = get_sql_results("SELECT * FROM folders where isPersonal = true AND uid = $uid");
	return $res[0];
}



// *****************************************
// FUNCTION: get_personal_folder_fid($uid)
// *****************************************
// Returns personal folder fid for given user ID
function get_personal_folder_fid($uid) {
	return get_sql_value("SELECT fid FROM folders where isPersonal = true AND uid = $uid");
}



// *****************************************
// FUNCTION: add_new_folder($foldername)
// *****************************************
// Creates a new folder and returns the FID
function add_new_folder($foldername) {
	// Check that a folder with the same name doesn't already exist (case sensitive)
	if (get_sql_value("select count(*) from folders where name='$foldername' and isPersonal = false;") > 0) {
		die("ERROR: A folder with the name '$foldername' already exists!");
	}
	else {
		run_sql_command("INSERT INTO folders(name, isPersonal) VALUES ('$foldername', false)");
		$ret = get_sql_value("SELECT MAX(fid) AS fid FROM folders WHERE name='$foldername'");
		return $ret;
	}
}



// *****************************************
// FUNCTION: get_folder_name($fid)
// *****************************************
// Gets the name of a folder with given fid
function get_folder_name($fid) {
	return get_sql_value("SELECT name FROM folders WHERE fid=$fid");
}



// *****************************************
// FUNCTION: rename_folder($fid, $newname)
// *****************************************
// Changes the name of a folder with a given fid
function rename_folder($fid, $newname) {
	run_sql_command("UPDATE folders SET name='$newname' WHERE fid=$fid");
	return true;
}



// ****************************************
// FUNCTION: remove_group_from_folder ($fid, $gid)
// ****************************************
// Removes given group GID from folder fid
function remove_group_from_folder($gid, $fid) {
	run_sql_command("DELETE FROM permissions WHERE fid='$fid' AND gid='$gid'");
	return true;
}



//
// FUNCTION: remove_user_from_folder($uid, $fid)
//
// Removes given user UID from folder FID
function remove_user_from_folder($uid, $fid) {
	run_sql_command("DELETE FROM permissions WHERE fid='$fid' AND uid='$uid'");
	return true;
}
?>
