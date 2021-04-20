<?php
	if (php_sapi_name () != 'cli' || !empty ($_SERVER['REMOTE_ADDR'])) die ();
	require_once (dirname ($argv[0]) . '/../common.php');
	
	switch ($argv[1])
	{
		case 'PROCESS':
		{
			if (($gID = intval ($argv[2])) && !empty ($argv[3]))
			{
				$_c = new PID ($gID);
				new Checker ($gID, $argv[3]);
			}
		} break;
		
		default:
		{
			$Hoster = $DB->Provide ("SELECT ID, removestring FROM hoster WHERE active = 1 AND removestring <> ''", NULL);
			
			if ($Hoster->num_rows)
			{
				while ($Data = $Hoster->fetch_assoc ())
					@pclose (@popen ("/usr/bin/php -q " . __FILE__ . " PROCESS {$Data['ID']} \"{$Data['removestring']}\" >/dev/null 2>&1 &", 'r'));
			}
		} break;
	}
?>