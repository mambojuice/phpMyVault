<?php
// Require HTTPS?
// If true, all pages will automatically redirect to their HTTPS address
$require_https = false;

// Database info
$db_host = 'localhost';
$db_name = 'pmv';
$db_user = 'pmv_user';
$db_pass = 'pmv_P@ss';

// Secret_key is the hash that will be used for encrypting/decrypting all password entries.
// DO NOT CHANGE THE SECRET KEY AFTER ADDING DATA
// YOU WILL NOT BE ABLE TO RETRIEVE ANY DATA THAT WAS CREATED PRIOR TO CHANGING THE SECRET KEY
$secret_key = 'SOMETHINGREALLYSECRET';

// Locally defined superuser account
// This is the default login with full admin rights.
// It should not be used other than to create other logins and groups.
$superuser = 'pmvadmin';
$superuser_password = 'password';

// Once session idle timeout is reached users will have to login again
$session_timeout = "900";	// 900 seconds = 15 minutes

// LDAP settings
$ldap = true;						// Use LDAP? True/False
// Ignore the rest of the LDAP settings if $ldap=false
$ldap_server = "server.domain.com";			// LDAP server IP or hostname
$ldap_port = "389";							// Usually 389 but might be different
$ldap_domain = "domain";					// Name of LDAP domain
$ldap_base_dn = "DC=domain,DC=com";			// Base DN for searching
$ldap_user = "domain\\user";				// User account used to query LDAP (escape backslashes)
$ldap_password = 'password';				// Password for user account

// Set to false for a more 'professional' look
$doge = true;
?>
