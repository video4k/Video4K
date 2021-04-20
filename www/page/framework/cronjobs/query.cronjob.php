<?php
	if (php_sapi_name () != 'cli' || !empty ($_SERVER['REMOTE_ADDR'])) die ();
	
	require_once (dirname ($argv[0]) . '/../common.php');
	
	$Result = $DB->Provide ("SELECT ID, UID, tag, links, tries FROM api_query WHERE tries < ? ORDER BY timestamp ASC", array ('d', 4));
	
	if ($Result->num_rows)
	{
		new PID ();
		
		while ($Entry = $Result->fetch_assoc ())
		{
			$_IMDB = $IMDB->GetIMDBReleaseTag ($Entry['tag']);
			
			if (preg_match ('/^tt\\d/', $_IMDB))
			{
				$User->Info = array ('ID' => $Entry['UID']);
				$ReleaseInfo = $IMDB->ParseRelease ($Entry['tag']);
				
				if ($ReleaseInfo['RATED'] != NULL)
					$IMDB->ParseManual (array ('IMDB' => $_IMDB, 'LID' => $ReleaseInfo['LID'], 'LINKS' => $Entry['links'], 'RATED' => $ReleaseInfo['RATED'], 'SEASON' => $ReleaseInfo['SEASON'], 'EPISODE' => $ReleaseInfo['EPISODE']));
				
				$DB->Provide ("DELETE FROM api_query WHERE ID = ?", array ('d', $Entry['ID']));
			} else $DB->Provide ("UPDATE api_query SET tries = (tries + 1) WHERE ID = ?", array ('d', $Entry['ID']));
			
			@sleep (3);
		}
	}
?>