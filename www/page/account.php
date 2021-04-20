<?php
	require_once ('./framework/common.php');
	
	$User->CheckRights (array (USR_ACTIVE, USR_TRUSTED, USR_COADMIN, USR_ADMIN));
	$Smarty->assign ('TABLE', FALSE);
	
	if ($_POST['action'] == 'password' && !empty ($_POST['apassword']) && !empty ($_POST['bpassword']) && !empty ($_POST['cpassword']))
	{
		if (md5 (sha1 ($_POST['cpassword'])) != $User->Info['password'])
		{
			$Smarty->assign ('NOTIFICATION', '<div class="notification alert alert-error">You entered a wrong account password.</div>');
		} else {
			if (($_POST['apassword'] != $_POST['bpassword']) || !preg_match ('/^.{6,20}$/', $_POST['apassword']))
			{
				$Smarty->assign ('NOTIFICATION', '<div class="notification alert alert-error">Please check your passwords (6-20 chars length).</div>');
			} else {
				if ($DB->Provide ("UPDATE users SET password = ? WHERE ID = ?", array ('sd', md5 (sha1 ($_POST['apassword'])), $User->Info['ID']))->affected_rows)
					$Smarty->assign ('NOTIFICATION', '<div class="notification alert alert-success">Your account password has been changed.</div>');
			}
		}
	}
	
	if (!empty ($_GET['name']) && (strtolower ($_GET['name']) != strtolower ($User->Info['name'])))
	{
		if ($DB->Provide ("SELECT ID FROM users WHERE name = ?", array ('s', $_GET['name']))->num_rows)
		{
			$Profile = $DB->Provide ("SELECT COUNT(l.ID) AS LINKS_COUNT, u.* FROM users AS u LEFT JOIN links AS l ON (l.UID = u.ID AND l.active = ?) WHERE LOWER(u.name) = LOWER(?)", array ('ds', 1, $_GET['name']))->fetch_assoc ();
		} else {
			$Profile = $DB->Provide ("SELECT COUNT(l.ID) AS LINKS_COUNT, u.* FROM users AS u LEFT JOIN links AS l ON (l.UID = u.ID AND l.active = ?) WHERE u.ID = ?", array ('dd', 1, $User->Info['ID']))->fetch_assoc ();
			$Smarty->assign ('NOTIFICATION', '<div class="notification alert alert-error">The profile you tried to visit <b>does not</b> exist.</div>');
		}
	} else $Profile = $DB->Provide ("SELECT COUNT(l.ID) AS LINKS_COUNT, u.* FROM users AS u LEFT JOIN links AS l ON (l.UID = u.ID AND l.active = ?) WHERE u.ID = ?", array ('dd', 1, $User->Info['ID']))->fetch_assoc ();
	
	$Smarty->assign ('PROFILE', $Profile);
	$Smarty->assign ('SIGNUP', date ('d.m.Y - G:i T', $Profile['signup']));
	$Smarty->assign ('ACCESS', date ('d.m.Y - G:i T', $Profile['access']));
	$Smarty->assign ('LINKS_COUNT', $Profile['LINKS_COUNT']);
	
	$Smarty->display ('header.tpl');
	$Smarty->display ('table.tpl');
	$Smarty->display ('contact.tpl');
	$Smarty->display ('account.tpl');
	$Smarty->display ('footer.tpl');
?>