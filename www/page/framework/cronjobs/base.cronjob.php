<?php
	if (php_sapi_name () != 'cli' || !empty ($_SERVER['REMOTE_ADDR'])) die ();
	
	require_once (dirname ($argv[0]) . '/../common.php');
	set_time_limit (120);
	
	// Remove expired Login Sessions
	$DB->Provide ("DELETE FROM sessions WHERE name = 'login' AND (UNIX_TIMESTAMP() - timestamp) >= (7 * 24 * 60 * 60)", NULL);
	
	// Clear Updates
	$DB->Provide ("DELETE FROM updates WHERE datestamp < DATE_SUB(CURDATE(), INTERVAL 7 DAY)", NULL);
	
	// Delete Unmanaged API Querys
	$DB->Provide ("DELETE FROM api_query WHERE (UNIX_TIMESTAMP() - timestamp) >= (7 * 24 * 60 * 60)", NULL);
	
	// Remove inactive users
	$DB->Provide ("DELETE u FROM users AS u WHERE u.rights = 0 AND (UNIX_TIMESTAMP() - u.access) >= (14 * 24 * 60 * 60) AND u.ID NOT IN (SELECT l.UID AS ID FROM links AS l GROUP BY l.UID HAVING COUNT(l.ID) > 0)", NULL);
?>