<?php
	class Checker
	{
		public function __construct ($ID, $RemoveIdentifier)
		{
			global $Base, $DB;
			
			set_time_limit (0);		
			
			$cData = array ('HID' => $ID, 'REMOVESTRING' => $RemoveIdentifier, 'LINKS' => array ());
			$Cinemas = $DB->Provide ("SELECT l.ID, l.EID, l.URL, l.language, l.season, l.episode FROM directory AS d LEFT JOIN links AS l ON (l.EID = d.ID) WHERE d.type = 0 AND l.active = 1 AND l.HID = ?", array ('d', $cData['HID']));
			
			if ($Cinemas->num_rows)
			{
				while ($Data = $Cinemas->fetch_assoc ())
					$cData['LINKS'][] = $Data;
			}
			
			$Random = $DB->Provide ("SELECT l.ID, l.EID, l.URL, l.language, l.season, l.episode FROM directory AS d LEFT JOIN links AS l ON (l.EID = d.ID) WHERE d.type > 0 AND l.active = 1 AND l.HID = ? ORDER BY RAND() LIMIT 200", array ('d', $cData['HID']));
			
			if ($Random->num_rows)
			{
				while ($Data = $Random->fetch_assoc ())
					$cData['LINKS'][] = $Data;
			}
			
			foreach ($cData['LINKS'] AS &$File)
			{
				$Content = $Base->FetchContent ($File['URL']);
				
				if (!empty ($Content))
				{
					if (@preg_match ("/{$cData['REMOVESTRING']}/i", $Content))
					{
						$DB->Provide ("DELETE FROM links WHERE ID = ?", array ('d', $File['ID']));
						
						$Check = $DB->Provide ("SELECT COUNT(ID) AS ACTIVE FROM links WHERE active = 1 AND EID = ? AND HID = ? AND language = ? AND season = ? AND episode = ?",
							array ('ddddd', $File['EID'], $cData['HID'], $File['language'], $File['season'], $File['episode']))->fetch_assoc ();
						
						if ($Check['ACTIVE'] < 5)
						{
							$DB->Provide ("UPDATE links SET active = 1 WHERE EID = ? AND HID = ? AND language = ? AND season = ? AND episode = ? AND active = 0 LIMIT ?",
								array ('dddddd', $File['EID'], $cData['HID'], $File['language'], $File['season'], $File['episode'], (5 - $Check['ACTIVE'])));
							
							$Check = $DB->Provide ("SELECT COUNT(ID) AS ACTIVE FROM links WHERE active = 1 AND EID = ? AND language = ?", array ('dd', $File['EID'], $File['language']))->fetch_assoc ();
							
							if ($Check['ACTIVE'] == 0) $DB->Provide ("DELETE FROM languages_index WHERE EID = ? AND LID = ?", array ('dd', $File['EID'], $File['language']));
						}
					}
				}
				
				@sleep (3);
			}
		}
	}
?>