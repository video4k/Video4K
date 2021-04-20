<?php
	if (php_sapi_name () != 'cli' || !empty ($_SERVER['REMOTE_ADDR'])) die ();
	
	require_once (dirname ($argv[0]) . '/../common.php');	
	$Tasks = $DB->Provide ("SELECT UID, BID FROM bulk_query ORDER BY timestamp ASC", NULL);
	
	set_time_limit (0);
	
	if ($Tasks->num_rows)
	{
		$cInfo = new finfo (FILEINFO_MIME_TYPE);
		$_c = new PID ();
		
		while ($Task = $Tasks->fetch_assoc ())
		{
			$DB->Provide ("DELETE FROM bulk_query WHERE BID = ?", array ('s', $Task['BID']));
			$fFile = (BULK_PATH . DIRECTORY_SEPARATOR . $Task['BID']);
			$User->Info = array ('ID' => $Task['UID']);
			
			if (file_exists ($fFile))
			{
				if ($cInfo->file ($fFile) == 'text/plain')
				{
					$fF = fopen ($fFile, 'r');
					$Content = fread ($fF, filesize ($fFile));
					fclose ($fF);
					
					if (strlen ($Content) == filesize ($fFile))
						$IMDB->ParseBulk ($Content);
				}
			}
		}
	}
	
	@array_map (create_function ('$file', '@unlink ($file);'), glob (BULK_PATH . DIRECTORY_SEPARATOR . '*')); // clear whole directory
?>