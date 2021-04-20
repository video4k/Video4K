<?php
	require_once ('./framework/common.php');
	
	if (strlen ($_POST['KEY']) == 32 && !empty ($_POST['RELEASE']) && !empty ($_POST['URL']))
	{
		$Access = $DB->Provide ("SELECT ID FROM users WHERE api = ? AND rights > ? LIMIT 1", array ('sd', $_POST['KEY'], 1));
		
		if ($Access->num_rows > 0)
		{
			$Access = $Access->fetch_assoc ();
			$User->Info = array ('ID' => $Access['ID']);
			$_IMDB = $IMDB->GetIMDBReleaseTag ($_POST['RELEASE']);
			
			if (preg_match ('/^tt\\d/', $_IMDB))
			{
				$ReleaseInfo = $IMDB->ParseRelease ($_POST['RELEASE']);
				
				if ($ReleaseInfo['RATED'] != NULL)
					$Result = $IMDB->ParseManual (array ('IMDB' => $_IMDB, 'LID' => $ReleaseInfo['LID'], 'LINKS' => $_POST['URL'], 'RATED' => $ReleaseInfo['RATED'], 'SEASON' => $ReleaseInfo['SEASON'], 'EPISODE' => $ReleaseInfo['EPISODE']));
				
				if (intval ($Result['success']) > 0) exit ("SUCCESS");
			} else {
				if ($DB->Provide ("INSERT INTO api_query (UID, tag, links, timestamp) VALUES (?, ?, ?, UNIX_TIMESTAMP())", array ('dss', $Access['ID'], $_POST['RELEASE'], $_POST['URL']))->affected_rows)
					exit ("ON HOLD");
			}
			
			header ('HTTP/1.1 406 Not Acceptable');
			exit ();
		}
	} header ('HTTP/1.1 403 Access Denied');
?>