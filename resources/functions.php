<?php

// SESSION FUNCTIONS ***********************

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
	
	echo "Function: check_login // ";
	
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
			echo "Login type is LDAP // ";
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
	run_sql_command("INSERT INTO sessions(session_id,timestamp,uid) VALUES('". session_id() . "',now(), $uid)");
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
	$SessionTimestamp = get_sql_value("SELECT timestamp FROM sessions WHERE session_id='" . session_id() . "'");
	
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
	
	run_sql_command("DELETE FROM sessions WHERE session_id='" . session_id() . "'");
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
	
	run_sql_command("UPDATE sessions SET timestamp = now() WHERE session_id = '" . session_id() . "'");
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
			run_sql_command("DELETE FROM sessions WHERE session_id = '" . $session['session_id'] . "'");
		}
	}	
}
//End function







// USER AND GROUP FUNCTIONS ****************


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
}


// *****************************************
// FUNCTION: add_local_user($login, $password)
// *****************************************
function add_local_user($login, $password) {
	include("resources/config.php");
	
	$pass_enc = md5($password);
	run_sql_command("INSERT INTO users (login, password, type) VALUES ('$login', '$pass_enc', 'local')");
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
// FUNCTION: get_my_uid()
// *****************************************
// Returns the UID of the active logged in session
function get_my_uid() {
	include("resources/config.php");
	
	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}

	return get_sql_value("SELECT uid FROM sessions WHERE session_id='" . session_id() . "'");
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
// FUNCTION: get_shared_users($id)
// *****************************************
// Returns array of user info for all users shared with object with provided ID
// Returns NULL if object has not been shared with any users
function get_shared_users($id) {
	return get_sql_results("SELECT * FROM vPasswordSharedUsers WHERE id=$id ORDER BY shared_login");
	}

	
// *****************************************
// FUNCTION: get_shared_groups($id)
// *****************************************
// Returns array of group info for all groups shared with object with provided ID
// Returns NULL if object has not been shared with any groups
function get_shared_groups($id) {
	return get_sql_results("SELECT * FROM vPasswordSharedGroups WHERE id=$id ORDER BY shared_group");
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


// *****************************************
// FUNCTION: get_passwords_shared_with_group($gid)
// *****************************************
// Returns array of passwords shared with provided GID
function get_passwords_shared_with_group($gid) {
	return get_sql_results("SELECT id, name FROM vPasswordSharedGroups WHERE shared_gid=$gid");
	}


// *****************************************
// FUNCTION: share_with_group($id, $gid, $mode = 'r')
// *****************************************
// Share object ID with group GID. Default mode is read-only.
function share_with_group($id, $gid, $mode = 'r') {
	// Check that we aren't already sharing with this group
	if (get_sql_value("SELECT mode FROM group_permissions WHERE id=$id and gid=$gid") == NULL) {
		run_sql_command("insert into group_permissions (id, gid, mode) values ('$id', '$gid', '$mode')");
		}
	}

// *****************************************
// FUNCTION: share_with_user($id, $uid, $mode = 'r')
// *****************************************
// Share object ID with user UID. Default mode is read-only.
function share_with_user($id, $uid, $mode = 'r') {
	// Check that we aren't already sharing with this user
	if (get_sql_value("SELECT mode FROM user_permissions WHERE id=$id and uid=$uid") == NULL) {
		run_sql_command("insert into user_permissions (id, uid, mode) values ('$id', '$uid', '$mode')");
		}
	}


// *****************************************
// FUNCTION: unshare_user($id, $uid)
// *****************************************
// Removes user UID from seeing object ID
function unshare_user($id, $uid) {
	run_sql_command("DELETE FROM user_permissions WHERE id=$id AND uid=$uid");
	}
	

// *****************************************
// FUNCTION: unshare_group($id, $gid)
// *****************************************
// Removes group GID from seeing object ID
function unshare_group($id, $gid) {
	run_sql_command("DELETE FROM group_permissions WHERE id=$id AND gid=$gid");
	}


// *****************************************
// FUNCTION: get_my_passwords()
// *****************************************
// Get all passwords where my UID is the owner
function get_my_passwords() {
	$id = get_my_uid();
	return get_sql_results("SELECT * FROM data WHERE owner=$id ORDER BY name");
	}


// *****************************************
// FUNCTION: get_passwords_shared_with_me()
// *****************************************
// Get all passwords shared with my UID
function get_passwords_shared_with_me() {
	$id = get_my_uid();
	return get_sql_results("SELECT id, name FROM vPasswordSharedUsers WHERE shared_uid=$id ORDER BY name");
	}


// *****************************************
// FUNCTION: get_passwords_shared_with_my_groups()
// *****************************************
// Returns array of passwords shared with all groups my UID is a member of
function get_passwords_shared_with_my_groups() {
	$my_uid = get_my_uid();
	$my_groups = get_group_membership($my_uid);
	
	$my_shared_objects = array();
	
	if ($my_groups) {
		foreach ($my_groups as $group) {
			$group_gid = $group['gid'];
			$group_passwords = get_passwords_shared_with_group($group_gid);
			foreach ($group_passwords as $password) {
				array_push($my_shared_objects, $password);
			}
		}
	}
	
	return $my_shared_objects;
}


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
function get_decrypted_password($id, $uid) {

	// Check if I have permissions
	if (check_object_permissions($id, $uid)) {
		$encrypted_pass = get_sql_value("SELECT password FROM data WHERE id=$id");
		return decrypt_string($encrypted_pass);
	}
	else {
		return "ACCESS DENIED";
	}
}








// OBJECT FUNCTIONS **********************


// *****************************************
// FUNCTION: get_password_object($id)
// *****************************************
// Returns array of data for the provided ID
function get_password_object($id) {
	$results = get_sql_results("SELECT * FROM data WHERE id=$id");
	return $results[0];
	}
	

// *****************************************
// FUNCTION: edit_password_object($id, $name, $login, $password, $note)
// *****************************************
// Updates data for the provided ID
function edit_password_object($id, $name, $login, $password, $note) {
	run_sql_command("UPDATE data SET name='$name' WHERE id=$id");
	run_sql_command("UPDATE data SET login='$login' WHERE id=$id");
	run_sql_command("UPDATE data SET password='$password' WHERE id=$id");
	run_sql_command("UPDATE data SET note='$note' WHERE id=$id");
	}
	

// *****************************************
// FUNCTION: add_password_object($name, $login, $password, $note)
// *****************************************
// Returns array of data for the provided ID
function add_password_object($name, $login, $password, $note) {
	$my_uid = get_my_uid();
	run_sql_command("INSERT INTO data (name, login, password, note, owner) VALUES ('$name', '$login', '$password', '$note', '$my_uid')");
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
// FUNCTION: get_object_name($id)
// *****************************************
// Returns the name of object ID
function get_object_name($id) {
	return get_sql_value("SELECT name FROM data WHERE id=$id");
	}


// *****************************************
// FUNCTION: check_object_permissions($id, $uid)
// *****************************************
// Tests if a given UID has access to an pbject ID
function check_object_permissions($id, $uid) {
	// Check if UID is owner
	$owner = get_sql_value("SELECT owner FROM data WHERE id=$id");
	if (get_my_uid() == $owner) {
		return true;
	}
	
	// Check if ID is shared with UID
	if (get_sql_value("SELECT id FROM user_permissions WHERE id=$id AND uid=$uid") != null) {
		return true;
	}
	
	// Check if ID is shared with groups
	$my_groups = get_group_membership($uid);	// Get all groups that uid belongs to
	foreach ($my_groups as $group) {			// Iterate through all groups
		$gid = $group['gid'];
		if (get_sql_value("SELECT id FROM group_permissions WHERE gid='$gid' AND id='$id'") != null) {	// Check if object ID is shared with GID
			return true;
		}
	}
	
	
	// Deny otherwise
	return false;
}


// *****************************************
// FUNCTION: get_owner($id)
// *****************************************
// Returns UID of owner for the provided password ID
function get_owner($id) {
	return get_sql_value("SELECT owner FROM data WHERE id=$id");
	}

?>
