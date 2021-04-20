<?php
	class IMDB extends IMDBParser
	{
		public function ParseBulk ($Bulk)
		{
			$List = preg_replace ("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $Bulk);
			$List = explode ("\n", $List);
			
			foreach ($List AS $Entry)
			{
				if (strncmp ($Entry, '#', 1) != 0)
				{
					$Info = array ();
					$Data = explode ('|', $Entry);
					
					if (count ($Data) >= 5)
					{
						foreach ($Data AS $Part)
						{
							$Pair = explode (':', $Part, 2);
							$Info[strtoupper ($Pair[0])] = $Pair[1];
						}
						
						$this->AddLink ($Info);
					}
				}
			}
		}
		
		public function ParseManual ($Data)
		{
			$Entry = $this->ProvideEntry ($Data['IMDB'], NULL);
			$Response = array ('success' => 0, 'failed' => array ());
			
			if (!empty ($Entry['EID']))
			{
				$List = preg_replace ("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $Data['LINKS']);
				$List = explode ("\n", $List);
				
				foreach ($List AS $Link)
				{
					$Data['HID'] = $this->ProvideHoster ($Link);
					
					if ($Data['HID'])
					{
						if ($this->AddLink (array ('ENTRY' => $Entry, 'LID' => $Data['LID'], 'HID' => $Data['HID'], 'RATED' => $Data['RATED'], 'LINK' => $Link, 'SEASON' => $Data['SEASON'], 'EPISODE' => $Data['EPISODE'])))
						{
							$Response['success']++;
						} else $Response['failed'][] = array (2, $Link);
					} else $Response['failed'][] = array (1, $Link);
				}
			}
			
			return $Response;
		}
		
		public function ParseRelease ($Tag)
		{
			global $CONFIG;
			
			$Data = array ('LID' => $CONFIG['LANG_DEFAULT_ID'], 'RATED' => NULL, 'SEASON' => 1, 'EPISODE' => 0);			
			
			$VideoPattern = array ('.BluRay.' => 3, '.BDRiP.' => 3,
				'.DVDRip.' => 3, '.WEBRiP.' => 3, '.VODRip.' => 3,
				'.HDRip.' => 3, '.HDTVRiP.' => 3, '.HDTV.' => 3, '.dTV.' => 3,
				'.TS.' => 2, '.TELESYNC.' => 2,
				'.CAM.' => 1
			);
			
			$AudioPattern = array ('.LD.' => 1, '.Line.Dubbed.' => 1, '.Dubbed.' => 1, '.MD.' => 2, '.MIC.DUBBED.' => 2);
			
			$LanguagePattern = array ('.German.Subbed.' => 'EN-DE', '.Subbed.German' => 'EN-DE', '.German.Custom.Subbed.' => 'EN-DE', '.German.' => 'DE', '.Spanish.' => 'ES', '.Latino.' => 'ES', '.French.' => 'FR', '.Greek.' => 'GR',
										'.Italian.' => 'IT', '.Japanese.' => 'JP', '.Dutch.' => 'NL', '.Russian.' => 'RU', '.Turkish.' => 'TR');
			
			foreach ($VideoPattern AS $Key => $Value)
			{
				if (preg_match (('/' . strrev ($Key) . '/i'), strrev ($Tag)))
				{
					$Data['RATED'] = $Value;
					break;
				}
			}
			
			if ($Data['RATED'] > 0)
			{
				if ($Data['RATED'] == 3)
				{
					foreach ($AudioPattern AS $Key => $Value)
					{
						if (preg_match (('/' . strrev ($Key) . '/i'), strrev ($Tag)))
						{
							$Data['RATED'] = intval ($Data['RATED'] - $Value);
							break;
						}
					}
				}
				
				switch ($Data['RATED'])
				{
					case 3: $Data['RATED'] = 'good'; break;
					case 2: $Data['RATED'] = 'medium'; break;
					case 1: $Data['RATED'] = 'bad'; break;
					default: $Data['RATED'] = NULL; break;
				}
				
				if ($Data['RATED'] != NULL)
				{
					foreach ($LanguagePattern AS $Key => $Value)
					{
						if (preg_match (('/' . strrev ($Key) . '/i'), strrev ($Tag)))
						{
							foreach ($CONFIG['LANG'] AS &$Language)
							{
								if ($Language['symbol'] == $Value)
								{
									$Data['LID'] = $Language['ID'];
									break;
								}
							} break;
						}
					}
					
					$DataSet = array ();
					
					if (preg_match ("/(\d{1,4})S./i", strrev ($Tag), $DataSet))
						$Data['SEASON'] = intval (strrev ($DataSet[1]));
					
					if (preg_match ("/.(\d{1,4})E/i", strrev ($Tag), $DataSet))
						$Data['EPISODE'] = intval (strrev ($DataSet[1]));
				}
			}
			
			return $Data;
		}
		
		public function AddLink ($Info)
		{
			global $CONFIG, $DB, $User;
			
			if ((!empty ($Info['IMDB']) || isset ($Info['ENTRY'])) && (!empty ($Info['LANG']) || isset ($Info['LID'])) && !empty ($Info['LINK']) && !empty ($User->Info))
			{
				if (!isset ($Info['ENTRY'])) $Info['ENTRY'] = $this->ProvideEntry ($Info['IMDB'], $Info['TYPE']);
				if (!isset ($Info['HID'])) $Info['HID'] = $this->ProvideHoster ($Info['LINK']);
				
				if (!isset ($Info['LID']))
				{
					if (!empty ($Info['SUBTITLE']))
					{
						$Info['LID'] = $this->ProvideLanguage ("{$Info['LANG']}-{$Info['SUBTITLE']}");
					} else $Info['LID'] = $this->ProvideLanguage ($Info['LANG']);
				}
				
				if ($Info['ENTRY']['EID'] && $Info['HID'] && $Info['LID'])
				{
					switch (strtolower ($Info['RATED']))
					{
						case 'good': $Info['QUALITY'] = 2; break;
						case 'medium': $Info['QUALITY'] = 1; break;
						case 'bad': $Info['QUALITY'] = 0; break;
						default: $Info['QUALITY'] = -1; break;
					}
					
					if ($Info['ENTRY']['TYPE'] == 2 && intval ($Info['EPISODE']) < 0) return FALSE;
					
					$Info['SEASON'] = (intval ($Info['SEASON']) > 0 ? intval ($Info['SEASON']) : 1);
					$Info['EPISODE'] = (intval ($Info['EPISODE']) >= 0 ? intval ($Info['EPISODE']) : 0);
					
					$Check = $DB->Provide ("SELECT MAX(quality) AS QUALITY FROM links WHERE active = 1 AND EID = ? AND HID = ? AND language = ? AND season = ? AND episode = ?",
									array ('ddddd', $Info['ENTRY']['EID'], $Info['HID'], $Info['LID'], $Info['SEASON'], $Info['EPISODE']))->fetch_assoc ();
					
					if (intval ($Check['QUALITY']) > intval ($Info['QUALITY'])) return FALSE; // Drop bad quality links

					// Quality - Upgrade Check
					if (($Info['ENTRY']['TYPE'] != 2) && (intval ($Check['QUALITY']) < intval ($Info['QUALITY'])))
					{
						$DB->Provide ("DELETE FROM links WHERE EID = ? AND HID = ? AND language = ?", array ('ddd', $Info['ENTRY']['EID'], $Info['HID'], $Info['LID']));
						$DB->Provide ("UPDATE directory SET timestamp = UNIX_TIMESTAMP() WHERE ID = ?", array ('d', $Info['ENTRY']['EID']));
						$DB->Provide ("INSERT IGNORE INTO updates (EID, LID, season, episode, datestamp) VALUES (?, ?, ?, ?, CURDATE()) ON DUPLICATE KEY UPDATE datestamp = VALUES(datestamp)", array ('dddd', $Info['ENTRY']['EID'], $Info['LID'], -1, -1));
					}
					
					// Episode - Upgrade Check
					if ($Info['ENTRY']['TYPE'] == 2)
					{
						$Upgrade = $DB->Provide ("SELECT MAX(episode) AS EPISODE FROM links WHERE active = 1 AND EID = ? AND language = ? AND season = ?", array ('ddd', $Info['ENTRY']['EID'], $Info['LID'], $Info['SEASON']))->fetch_assoc ();
						
						if (intval ($Upgrade['EPISODE']) < $Info['EPISODE'])
							$DB->Provide ("INSERT IGNORE INTO updates (EID, LID, season, episode, datestamp) VALUES (?, ?, ?, ?, CURDATE()) ON DUPLICATE KEY UPDATE datestamp = VALUES(datestamp), season = VALUES(season), episode = VALUES(episode)",
											array ('dddd', $Info['ENTRY']['EID'], $Info['LID'], $Info['SEASON'], $Info['EPISODE']));
					}
					
					// Insert Link into Database
					$Check = $DB->Provide ("SELECT COUNT(ID) AS ACTIVE FROM links WHERE active = 1 AND EID = ? AND HID = ? AND language = ? AND season = ? AND episode = ?",
									array ('ddddd', $Info['ENTRY']['EID'], $Info['HID'], $Info['LID'], $Info['SEASON'], $Info['EPISODE']))->fetch_assoc ();
					
					if ($DB->Provide ("INSERT IGNORE INTO links (EID, UID, HID, URL, language, season, episode, quality, active, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP())",
							array ('dddsddddd', $Info['ENTRY']['EID'], $User->Info['ID'], $Info['HID'], $Info['LINK'], $Info['LID'], $Info['SEASON'], $Info['EPISODE'], intval ($Info['QUALITY']), ($Check['ACTIVE'] < 5 ? TRUE : FALSE)))->affected_rows)
					{
						$DB->Provide ("INSERT IGNORE languages_index (EID, LID) VALUES (?, ?)", array ('dd', $Info['ENTRY']['EID'], $Info['LID']));
						return TRUE;
					}
				}
			}
			
			return FALSE;
		}
		
		public function AddEntryUpdates ($EID)
		{
			global $DB;
			
			if ($EID)
			{
				$Languages = $DB->Provide ("SELECT language FROM links WHERE EID = ? AND ((UNIX_TIMESTAMP() - timestamp) <= (24 * 60 * 60)) GROUP BY language", array ('d', $EID));
				
				while ($Entry = $Languages->fetch_assoc ())
					$DB->Provide ("INSERT IGNORE INTO updates (EID, LID, season, episode, datestamp) VALUES (?, ?, ?, ?, CURDATE()) ON DUPLICATE KEY UPDATE datestamp = VALUES(datestamp)",	array ('dddd', $EID, $Entry['language'], -1, -1));
			}
		}
		
		public function CleanEntryUpdates ()
		{
			global $DB;
			
			$Data = array ();
			$Result = $DB->Provide ("SELECT u.EID, u.LID, u.season, u.episode FROM updates AS u WHERE (season != 0 AND episode != 0) AND datestamp = CURDATE() ORDER BY u.EID, u.season DESC, u.episode DESC", NULL);
			
			while ($Entry = $Result->fetch_assoc ())
			{
				if ($Data[$Entry['EID']]++ >= 2)
					$DB->Provide ("DELETE FROM updates WHERE EID = ? AND LID = ? AND season = ? AND episode = ?", array ('dddd', $Entry['EID'], $Entry['LID'], $Entry['season'], $Entry['episode']));
			}
		}
		
		public function ProvideEntry ($MID, $Type)
		{
			global $DB;
			
			$MID = substr ($MID, 2);
			$Entry = array ('EID' => NULL, 'TYPE' => NULL);
			
			if (intval ($MID) > 0 && (intval ($MID) < 0x98967f))
			{
				$Result = $DB->Provide ("SELECT ID, type FROM directory WHERE MID = ?", array ('d', $MID));
				
				if (!($Result->num_rows))
				{
					switch ($Type)
					{
						case 'c': $Entry['TYPE'] = 0; break;
						case 'm': $Entry['TYPE'] = 1; break;
						case 's': $Entry['TYPE'] = 2; break;
						default:
						{
							set_time_limit (30);
							$Check = $this->GetEntryInformation ("tt{$MID}", FALSE);
							
							if ($Check['STATUS']) $Entry['TYPE'] = (intval ($Check['TYPE']) ? 2 : 1);
								else $Entry['TYPE'] = 1;
						} break;
					}
					
					$Result = $DB->Provide ("INSERT INTO directory (MID, type, timestamp) VALUES (?, ?, UNIX_TIMESTAMP())", array ('dd', $MID, $Entry['TYPE']));
					
					if ($Result->affected_rows)
					{
						$Entry['EID'] = $Result->insert_id;
						$DB->Provide ("INSERT INTO crawler_query (ID, timestamp, priority) VALUES (?, UNIX_TIMESTAMP(), ?)", array ('dd', $Entry['EID'], $Entry['TYPE']));
					}
				} else {
					$Result = $Result->fetch_assoc ();
					$Entry['EID'] = $Result['ID'];
					$Entry['TYPE'] = $Result['type'];
				}
			}
			
			return $Entry;
		}
		
		private function ProvideHoster ($Link)
		{
			global $CONFIG;
			$ID = NULL;
			
			if (filter_var ($Link, FILTER_VALIDATE_URL))
			{
				$Host = parse_url ($Link);
				$Host = $Host['host'];
				
				foreach ($CONFIG['HOSTER'] AS $Key => $Entry)
				{
					if (strpos (strtolower ($Entry['URL']), strtolower ($Host)) !== FALSE)
					{
						$ID = $Key;
						break;
					}
				}
			}
			
			return $ID;
		}
		
		private function ProvideLanguage ($Language)
		{
			global $CONFIG;
			$ID = NULL;
			
			foreach ($CONFIG['LANG'] AS $Key => $Entry)
			{
				if ($Entry['symbol'] == strtoupper ($Language))
				{
					$ID = $Key;
					break;
				}
			}
			
			return $ID;
		}

		public function ProvideGenres ($EID, $Genres)
		{
			global $DB;
			
			foreach ($Genres AS $Genre)
			{
				if (!empty ($Genre))
				{
					$DB->Provide ("INSERT IGNORE INTO genres (name) VALUES (?)", array ('s', $Genre));
					$DB->Provide ("INSERT INTO genres_index (EID, GID) VALUES (?, (SELECT ID FROM genres WHERE name = ?))", array ('ds', $EID, $Genre));
				}
			}
		}
		
		public function ProvideActors ($EID, $Actors)
		{
			global $DB;
			
			foreach ($Actors AS $Actor)
			{
				if (!empty ($Actor))
				{
					$DB->Provide ("INSERT IGNORE INTO actors (name) VALUES (?)", array ('s', $Actor));
					$DB->Provide ("INSERT INTO actors_index (EID, AID) VALUES (?, (SELECT ID FROM actors WHERE name = ?))", array ('ds', $EID, $Actor));
				}
			}
		}
		
		public function ProvideDirectors ($EID, $Directors)
		{
			global $DB;
			
			foreach ($Directors AS $Director)
			{
				if (!empty ($Director))
				{
					$DB->Provide ("INSERT IGNORE INTO directors (name) VALUES (?)", array ('s', $Director));
					$DB->Provide ("INSERT INTO directors_index (EID, DID) VALUES (?, (SELECT ID FROM directors WHERE name = ?))", array ('ds', $EID, $Director));
				}
			}
		}
	}
?>